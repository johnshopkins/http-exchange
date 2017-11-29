<?php

namespace HttpExchange\Adapters\Guzzle6;

class Client implements \HttpExchange\Interfaces\ClientInterface
{
  /**
   * Instance of Guzzle6
   * @var object
   */
	public $http;

  /**
   * Record verbose debug data
   * @var boolean
   */
	public $debug = false;

  /**
   * Response of last request
   * @var object
   */
	public $response;

  /**
   * Error of last request
   * @var null/array
   */
  public $log = null;

	public $json_types = array(
		'application/json',
		'text/json',
		'text/x-json',
		'text/javascript'
	);

	public $xml_types = array(
		'application/xml',
		'text/xml',
		'application/rss+xml',
		'application/xhtml+xml',
		'application/atom+xml',
		'application/xslt+xml',
		'application/mathml+xml'
	);

	public function __construct($guzzle)
	{
		$this->http = $guzzle;
		$this->debug = $this->http->getConfig('debug');
	}

	/**
	 * Fetch a batch of requests.
	 * @param  array $requests Array of requests created using $this->createRequest
	 * @return array Data returned by each request
	 */
	public function batch($requests)
	{
    $this->log = array();
    $this->response = array();

    $requests = array_map(array($this, "createBatchRequests"), $requests);

    // start output buffering
    if ($this->debug) ob_start();

    // make requests
    $response = \GuzzleHttp\Promise\settle($requests)->wait();

    // save debug data if debug is on
    // debug data comes in one bug chunk -- not able to parse into separate requests
    // $debug = $this->debug ? ob_get_contents() : null;

    // end output buffering
    if ($this->debug) ob_end_clean();

    foreach ($response as $i => $r) {

      // add response to $this->response no matter the result
      $this->response[$i] = $r;

      if ($r['state'] !== 'fulfilled') {

        $e = $r['reason'];

        $log = $this->createLog($e);

        // if ($this->debug) {
        //   // add debug data
        //   $log['debug'] = $debug;
        // }

        $this->log[] = $log;
      }
    }

		return $this;
	}

  public function createRequest($method, $url, $opts)
  {
    $request = new Request($method, $url, $opts);
    return $request->get();
  }

  protected function createBatchRequests($args)
	{
    $request = new Request(...$args);
    return $this->http->sendAsync($request->get());
	}

  protected function send($request)
  {
    $this->log = array();
    $this->response = null;

    try {

      // start output buffering
      if ($this->debug) ob_start();

      // send request
      $this->response = $this->http->send($request);

      // end output bufferin
      if ($this->debug) ob_end_clean();

    } catch (\Exception $e) {

      $log = $this->createLog($e);

      if ($this->debug) {
        $log['debug'] = ob_get_contents();
        ob_end_clean(); // end output buffering
      }

      $this->log[] = $log;

    }
  }

  protected function createLog($e)
  {
    $request = $e->getRequest();
    $error = $e->getMessage();

    $log = [
      'method' => $request->getMethod(),
      'uri' => (string) $request->getUri(),
      'headers' => $request->getHeaders(),
      'full_error' => $error,
      'short_error' => $this->getShortError($error)
    ];

    if (isset($_SERVER['HTTP_HOST'])) {
      $log['requested_from_host'] = $_SERVER['HTTP_HOST';
    }

    if (isset($_SERVER['REQUEST_URI'])) {
      $log['requested_from_uri'] = $_SERVER['REQUEST_URI';
    }

    return $log;
  }

  /**
   * Parse the error, looking for the curl error number
   * @param  string $error cURL error
   * @return string        Short cURL error
   */
  protected function getShortError($error)
  {
    preg_match('/cURL error \d+/', $error, $matches);
    return !empty($matches) ? $matches[0] : null;
  }

  public function get($url, $opts = [])
	{
    $request = $this->createRequest('get', $url, $opts);
    $this->send($request);

		return $this;
	}

	public function post($url, $opts = [])
	{
    $request = $this->createRequest('post', $url, $opts);
    $this->send($request);

		return $this;
	}

	public function put($url, $opts = [])
	{
    $request = $this->createRequest('put', $url, $opts);
    $this->send($request);

		return $this;
	}

	public function patch($url, $opts = [])
	{
    $request = $this->createRequest('patch', $url, $opts);
    $this->send($request);

		return $this;
	}

	public function delete($url, $opts = [])
	{
    $request = $this->createRequest('delete', $url, $opts);
    $this->send($request);

		return $this;
	}

	public function head($url, $opts = [])
	{
    $request = $this->createRequest('head', $url, $opts);
    $this->send($request);

		return $this;
	}

	/**
	 * Get the body of the response, which
	 * can be one response or a pool or responses.
	 * @return mixed
	 */
	public function getBody()
	{
		if (!$this->response) return null;

		if (is_array($this->response)) {
			// batch response
			$responses = array();
			foreach ($this->response as $response) {

				if ($response['state'] != 'fulfilled') {
					$responses[] = null;
					continue;
				}

				$responses[] = $this->parseBody($response['value']);
			}
			return $responses;
		} else {
			// single response
			return $this->parseBody($this->response);
		}
	}

	/**
	 * Based on the headers of the response,
	 * determine the formatted body (xml or json)
	 * @param  object $response Response object
	 * @return mixed
	 */
	protected function parseBody($response)
	{
		if (!$response) return null;

		$headers = $response->getHeaders();

		// make case consistent
		$headers = array_change_key_case($headers, CASE_LOWER);

		if (isset($headers['content-type']) && is_array($headers['content-type'])) {
			$contentType = end($headers['content-type']);
		}

		$contentType = preg_split('/[;\s]+/', $contentType);
		$contentType = $contentType[0];

		$body = (string) $response->getBody();

		if (in_array($contentType, $this->json_types) || strpos($contentType, '+json') !== false) {

			return json_decode($body);

		} elseif (in_array($contentType, $this->xml_types) || strpos($contentType, '+xml') !== false) {

			return new \SimpleXMLElement($body);

		} else {
			return $body;
		}
	}


	public function getStatusCode()
	{
		if ($this->response) {
			return $this->response->getStatusCode();
		} else {
			// exception
			return null;
		}

	}

}

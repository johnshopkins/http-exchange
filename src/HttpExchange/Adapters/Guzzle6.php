<?php

namespace HttpExchange\Adapters;

class Guzzle6 implements \HttpExchange\Interfaces\ClientInterface
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
		"application/json",
		"text/json",
		"text/x-json",
		"text/javascript"
	);

	public $xml_types = array(
		"application/xml",
		"text/xml",
		"application/rss+xml",
		"application/xhtml+xml",
		"application/atom+xml",
		"application/xslt+xml",
		"application/mathml+xml"
	);

	public function __construct($guzzle)
	{
		$this->http = $guzzle;
		$this->debug = $this->http->getConfig("debug");
	}

	public function createAsynsRequest($method, $url, $params = null, $headers = null, $options = null)
	{
		$args = array(
			"headers" => $headers,
			"query" => $params
		);

		if (is_array($options)) {
			$args = array_merge($options, $args);
		}

		$method = strtolower($method) . "Async";
		return $this->http->$method($url, $args);
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

    // start output buffering
    if ($this->debug) ob_start();

    // make requests
    $response = \GuzzleHttp\Promise\settle($requests)->wait();

    // save debug data if debug is on
    $debug = $this->debug ? ob_get_contents() : null;

    // end output buffering
    if ($this->debug) ob_end_clean();

    foreach ($response as $i => $r) {

      // add response to $this->response no matter the result
      $this->response[$i] = $r;

      if ($r["state"] !== "fulfilled") {

        $context = $r["reason"]->getHandlerContext();

        $log = array(
          "api_uri" => $context["url"],
          "error" => $context["error"],
          "url" => $_SERVER["REQUEST_URI"]
        );

        if ($this->debug) {
          // add debug data
          $log["debug"] = $debug;
        }

        $this->log[] = $log;
      }
    }

		return $this;
	}

	public function get($url, $params = null, $headers = null, $options = null)
	{
    $this->log = null;
    $this->response = null;

    $args = array(
			"headers" => $headers,
			"query" => $params,
			"exceptions" => false
		);

		if (is_array($options)) {
			$args = array_merge($options, $args);
		}

    try {


      // start output buffering
      if ($this->debug) ob_start();

      // run method
      $this->response = $this->http->get($url, $args);

      // end output buffering
      if ($this->debug) ob_end_clean();

    } catch (\Exception $e) {

      $log = array(
        "endpoint" => $url,
        "params" => $params,
        "headers" => $headers,
        "error" => $e->getMessage(),
        "url" => $_SERVER["REQUEST_URI"]
      );

      if ($this->debug) {
        $log["debug"] = ob_get_contents();
        ob_end_clean(); // end output buffering
      }

      $this->log = $log;

    }

		return $this;
	}

	public function post($url, $params = null, $headers = null, $options = null)
	{
    $this->log = null;
    $this->response = null;

		$args = array(
			"headers" => $headers,
			"query" => $params,
			"exceptions" => false
		);

		if (is_array($options)) {
			$args = array_merge($options, $args);
		}

		try {

			if ($this->debug) ob_start();
	    $this->response = $this->http->post($url, $args);
			if ($this->debug) ob_end_clean();

    } catch (\Exception $e) {

      $log = array(
				"endpoint" => $url,
				"params" => $params,
				"headers" => $headers,
				"error" => $e->getMessage()
			);

      if ($this->debug) {
        $log["debug"] = ob_get_contents();
        ob_end_clean(); // end output buffering
      }

      $this->log = $log;

		}

		return $this;
	}

	public function put($url, $params = null, $headers = null, $options = null)
	{
		$args = array(
			"headers" => $headers,
			"query" => $params,
			"exceptions" => false
		);

		if (is_array($options)) {
			$args = array_merge($options, $args);
		}

		try {

			if ($this->debug) ob_start();
	    $this->response = $this->http->put($url, $args);
			if ($this->debug) ob_end_clean();

    } catch (\Exception $e) {

			$log = array(
				"endpoint" => $url,
				"params" => $params,
				"headers" => $headers,
				"error" => $e->getMessage()
			);

      if ($this->debug) {
        $log["debug"] = ob_get_contents();
        ob_end_clean(); // end output buffering
      }

      $this->log = $log;

		}

		return $this;
	}

	public function patch($url, $params = null, $headers = null, $options = null)
	{
		$args = array(
			"headers" => $headers,
			"query" => $params,
			"exceptions" => false
		);

		if (is_array($options)) {
			$args = array_merge($options, $args);
		}

		try {

			if ($this->debug) ob_start();
	    $this->response = $this->http->patch($url, $args);
			if ($this->debug) ob_end_clean();

		} catch (\Exception $e) {

			$log = array(
				"endpoint" => $url,
				"params" => $params,
				"headers" => $headers,
				"error" => $e->getMessage()
			);

      if ($this->debug) {
        $log["debug"] = ob_get_contents();
        ob_end_clean(); // end output buffering
      }

      $this->log = $log;
		}

		return $this;
	}

	public function delete($url, $params = null, $headers = null, $options = null)
	{
		$args = array(
			"headers" => $headers,
			"query" => $params,
			"exceptions" => false
		);

		if (is_array($options)) {
			$args = array_merge($options, $args);
		}

		try {

			if ($this->debug) ob_start();
	    $this->response = $this->http->delete($url, $args);
			if ($this->debug) ob_end_clean();

    } catch (\Exception $e) {

			$log = array(
				"endpoint" => $url,
				"params" => $params,
				"headers" => $headers,
				"error" => $e->getMessage()
			);

      if ($this->debug) {
        $log["debug"] = ob_get_contents();
        ob_end_clean(); // end output buffering
      }

      $this->log = $log;
		}

		return $this;
	}

	public function head($url, $params = null, $headers = null, $options = null)
	{
		$args = array(
			"headers" => $headers,
			"query" => $params,
			"exceptions" => false
		);

		if (is_array($options)) {
			$args = array_merge($options, $args);
		}

		try {

			if ($this->debug) ob_start();
	    $this->response = $this->http->head($url, $args);
			if ($this->debug) ob_end_clean();

		} catch (\Exception $e) {

      $log =  array(
				"endpoint" => $url,
				"params" => $params,
				"headers" => $headers,
				"error" => $e->getMessage()
			);

      if ($this->debug) {
        $log["debug"] = ob_get_contents();
        ob_end_clean(); // end output buffering
      }

      $this->log = $log;
		}

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

				if ($response["state"] != "fulfilled") {
					$responses[] = null;
					continue;
				}

				$responses[] = $this->parseBody($response["value"]);
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

		if (isset($headers["content-type"]) && is_array($headers["content-type"])) {
			$contentType = end($headers["content-type"]);
		}

		$contentType = preg_split("/[;\s]+/", $contentType);
		$contentType = $contentType[0];

		$body = (string) $response->getBody();

		if (in_array($contentType, $this->json_types) || strpos($contentType, "+json") !== false) {

			return json_decode($body);

		} elseif (in_array($contentType, $this->xml_types) || strpos($contentType, "+xml") !== false) {

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

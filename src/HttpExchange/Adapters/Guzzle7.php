<?php

namespace HttpExchange\Adapters;

class Guzzle7 implements \HttpExchange\Interfaces\ClientInterface
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

  public $json_types = [
    'application/json',
    'text/json',
    'text/x-json',
    'text/javascript'
  ];

  public $xml_types = [
    'application/xml',
    'text/xml',
    'application/rss+xml',
    'application/xhtml+xml',
    'application/atom+xml',
    'application/xslt+xml',
    'application/mathml+xml'
  ];

  public function __construct($guzzle)
  {
    $this->http = $guzzle;
    $this->debug = $this->http->getConfig('debug');
  }

  /**
   * Fetch a batch of requests.
   * @param  array $requests Array of requests [ [$method, $url, $opts] ]
   * @return array Data returned by each request
   */
  public function batch($requests)
  {
    $this->log = [];
    $this->response = [];

    $requests = array_map([$this, 'createBatchRequest'], $requests);

    // make requests
    $response = \GuzzleHttp\Promise\settle($requests)->wait();

    foreach ($response as $i => $r) {

      // add response to $this->response no matter the result
      $this->response[$i] = $r;

      if ($r['state'] !== 'fulfilled') {

        $e = $r['reason'];

        $log = $this->createLog($e);

        $this->log[] = $log;
      }
    }

    return $this;
  }

  /**
   * Send an HTTP request
   * @param $method string HTTP method (ex: get, post, etc...)
   * @param $url
   * @param $opts
   * @return void
   */
  public function sendRequest($method, $url, $opts)
  {
    $this->log = [];
    $this->response = null;

    try {
      $method = strtolower($method);
      $this->response = $this->http->$method($url, $opts);
    } catch (\Exception $e) {
      $this->log[] = $this->createLog($e);
    }
  }

  protected function createBatchRequest($args)
  {
    $method = array_shift($args) . 'Async';
    return $this->http->$method(...$args);
  }

  /**
   * Create a log entry based on the given exception
   * @param $e object Exception object
   * @return array
   */
  protected function createLog($e): array
  {
    $request = $e->getRequest();
    $error = $e->getMessage();
    $code = $e->getCode();
    $reasonPhrase = $error;

    $shortError = null;

    if (method_exists($e, 'getResponse')) {
      $response = $e->getResponse();
      $shortError = $code . ' ' . $response->getReasonPhrase();
    } else {
      // parse cURL error
      $shortError = $this->getShortError($error);
    }

    $log = [
      'short_error' => $shortError,
      'full_error' => $error,
      'method' => $request->getMethod(),
      'uri' => (string) $request->getUri(),
      'code' => $code,
      'headers' => $request->getHeaders(),
      'handler_context' => $e->getHandlerContext()
    ];

    if (isset($_SERVER['HTTP_HOST'])) {
      $log['requested_from_host'] = $_SERVER['HTTP_HOST'];
    }

    if (isset($_SERVER['REQUEST_URI'])) {
      $log['requested_from_uri'] = $_SERVER['REQUEST_URI'];
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
    $this->sendRequest('get', $url, $opts);
    return $this;
  }

  public function post($url, $opts = [])
  {
    $this->sendRequest('post', $url, $opts);
    return $this;
  }

  public function put($url, $opts = [])
  {
    $this->sendRequest('put', $url, $opts);
    return $this;
  }

  public function patch($url, $opts = [])
  {
    $this->sendRequest('patch', $url, $opts);
    return $this;
  }

  public function delete($url, $opts = [])
  {
    $this->sendRequest('delete', $url, $opts);
    return $this;
  }

  public function head($url, $opts = [])
  {
    $this->sendRequest('head', $url, $opts);
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
      $responses = [];
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

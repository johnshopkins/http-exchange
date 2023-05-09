<?php

namespace HttpExchange\Adapters;

class Guzzle7 implements \HttpExchange\Interfaces\ClientInterface
{
  /**
   * Response of last request
   * @var object
   */
  public $response;

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

  public function __construct(protected $http)
  {

  }

  /**
   * Fetch a batch of requests.
   * @param  array $requests Array of requests [ [$method, $url, $opts] ]
   * @return array Data returned by each request
   */
  public function batch($requests)
  {
    $requests = array_map([$this, 'createBatchRequest'], $requests);
    $this->response = \GuzzleHttp\Promise\settle($requests)->wait();

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
    $this->response = $this->http->$method($url, $opts);
  }

  protected function createBatchRequest($args)
  {
    $method = array_shift($args) . 'Async';
    return $this->http->$method(...$args);
  }

  public function get($url, $opts = [])
  {
    $this->sendRequest('GET', $url, $opts);
    return $this;
  }

  public function post($url, $opts = [])
  {
    $this->sendRequest('POST', $url, $opts);
    return $this;
  }

  public function put($url, $opts = [])
  {
    $this->sendRequest('PUT', $url, $opts);
    return $this;
  }

  public function patch($url, $opts = [])
  {
    $this->sendRequest('PATCH', $url, $opts);
    return $this;
  }

  public function delete($url, $opts = [])
  {
    $this->sendRequest('DELETE', $url, $opts);
    return $this;
  }

  public function head($url, $opts = [])
  {
    $this->sendRequest('HEAD', $url, $opts);
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

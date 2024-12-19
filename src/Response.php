<?php

namespace HttpExchange;

class Response
{
  public array $json_types = [
    'application/json',
    'text/json',
    'text/x-json',
    'text/javascript'
  ];

  public array $xml_types = [
    'application/xml',
    'text/xml',
    'application/rss+xml',
    'application/xhtml+xml',
    'application/atom+xml',
    'application/xslt+xml',
    'application/mathml+xml'
  ];

  public function __construct(protected \GuzzleHttp\Psr7\Response $response)
  {

  }

  public function getStatusCode()
  {
    return $this->response->getStatusCode();
  }

  /**
   * Get the body of the response, which
   * can be one response or a pool or responses.
   * @return mixed
   */
  public function getBody()
  {
    if (is_array($this->response)) {
      // batch response
      return array_map([$this, 'parseBody'], $this->response);
    }

    return $this->parseBody($this->response);
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

    $contentType = $headers['content-type'] ?? '';
    if (is_array($contentType)) {
      $contentType = end($contentType);
    }

    $contentType = preg_split('/[;\s]+/', $contentType);
    $contentType = $contentType[0];

    $body = (string) $response->getBody();

    if (in_array($contentType, $this->json_types) || strpos($contentType, '+json') !== false) {
      return json_decode($body);
    }

    if (in_array($contentType, $this->xml_types) || strpos($contentType, '+xml') !== false) {
      return new \SimpleXMLElement($body);
    }

    return $body;
  }
}

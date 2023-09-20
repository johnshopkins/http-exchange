<?php

namespace HttpExchange;

class Response
{
  public function __construct(protected $response)
  {

  }

  /**
   * Get the body of the response, which
   * can be one response or a pool or responses.
   * @return mixed
   */
  public function getBody()
  {
    if (!$this->response) {
      return null;
    }

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
    if (!$response) {
      return null;
    }

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

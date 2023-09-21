<?php

namespace HttpExchange\Adapters;

use HttpExchange\Response;

class AdapterBase
{
  public function __construct(protected $http)
  {

  }

  public function get(string $uri, array $options = [])
  {
    return $this->sendRequest('get', $uri, $options);
  }

  public function delete(string $uri, array $options = [])
  {
    return $this->sendRequest('delete', $uri, $options);
  }

  public function head(string $uri, array $options = [])
  {
    return $this->sendRequest('head', $uri, $options);
  }

  public function options(string $uri, array $options = [])
  {
    return $this->sendRequest('options', $uri, $options);
  }

  public function patch(string $uri, array $options = [])
  {
    return $this->sendRequest('patch', $uri, $options);
  }

  public function post(string $uri, array $options = [])
  {
    return $this->sendRequest('post', $uri, $options);
  }

  public function put(string $uri, array $options = [])
  {
    return $this->sendRequest('put', $uri, $options);
  }

  /**
   * Fetch a batch of requests.
   * @param  array $requests Array of requests [ [$method, $url, $opts] ]
   * @return array Array of request results.
   */
  public function batch(array $requests): array
  {
    $promises = $this->compileBatchRequests($requests);
    $data = $this->resolvePromises($promises);

    // map each response to Response or Exception
    // do not throw exception so successful responses can still be used
    return array_map(function ($promise) {

      if ($promise['state'] === 'fulfilled') {
        return new Response($promise['value']);
      }

      // return an exception
      $exception = new \HttpExchange\Exceptions\HTTP($promise['reason']->getMessage(), $promise['reason']->getCode());
      $exception->addAdditionalData($this->getExceptionData($promise['reason']));

      return $exception;

    }, $data);
  }

  /**
   * Send HTTP request
   * @param $method
   * @param $uri
   * @param $opts
   * @return Response
   * @throws \HttpExchange\Exceptions\HTTP
   */
  protected function sendRequest($method, $uri, $opts)
  {
    try {
      $result = $this->http->$method($uri, $opts);
      return new Response($result);
    } catch (\Exception $e) {

      $exception = new \HttpExchange\Exceptions\HTTP($e->getMessage(), $e->getCode());
      $exception->addAdditionalData($this->getExceptionData($e));

      throw $exception;
    }
  }

  /**
   * Compile requests into async promises
   * @param array $requests
   * @return array
   */
  protected function compileBatchRequests(array $requests): array
  {
    return array_map(function ($args) {
      $method = array_shift($args) . 'Async';
      return $this->http->$method(...$args);
    }, $requests);
  }

  public function getExceptionData(\Exception $exception): array
  {
    $request = $exception->getRequest();
    $error = $exception->getMessage();

    if (method_exists($exception, 'getResponse')) {
      // get the full response content (guzzle truncates $e->getMessage())
      if ($response = $exception->getResponse()) {
        $error = json_decode($response->getBody()->getContents());
      }
    }

    return [
      'original_exception_type' => get_class($exception),
      'full_error' => $error,
      'method' => $request->getMethod(),
      'uri' => (string) $request->getUri(),
      'headers' => $request->getHeaders(),
      'handler_context' => $exception->getHandlerContext(),
    ];
  }
}

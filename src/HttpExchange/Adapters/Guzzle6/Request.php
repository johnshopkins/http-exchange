<?php

namespace HttpExchange\Adapters\Guzzle6;

class Request
{
  /**
   * Instance of \GuzzleHttp\Psr7\Request
   * @var [type]
   */
  protected $request;

  /**
   * Default request options
   * @var array
   */
  protected $defaultOpts = [
    'query' => [],
    'headers' => [],
    'body' => null
  ];

  protected $methods = ['get', 'post', 'put', 'patch', 'delete', 'head'];

  /**
   * Constructor
   * @param string $method HTTP method
   * @param string $url    Request URL
   * @param array  $opts   Request options. See $this->defaultOpts for default
   */
  public function __construct($method, $url, $opts = array())
  {
    $this->validateMehod($method);
    $this->validateUrl($method);

    $opts = array_merge($this->defaultOpts, $opts);

    $this->request = $this->createRequest($method, $url, $opts);
  }

  protected function validateMehod($method)
  {
    $lower = strtolower($method);
    if (!in_array($lower, $this->methods)) {
      throw new \BadMethodCallException("{$method} is not a valid method.");
    }
  }

  protected function validateUrl($url)
  {
    // only basic checking
    if (!preg_match('/^https?:\/\//i', $url)) {
      throw new \BadMethodCallException("{$url} is not a valid URL.");
    }
  }

  /**
   * Creates the request object
   * @var object
   */
  protected function createRequest($method, $url, $opts)
  {
    $args = $this->compileArgs($method, $url, $opts);
    return new \GuzzleHttp\Psr7\Request(...$args);
  }

  protected function compileArgs($method, $url, $opts)
  {
    return [
      $method,
      $url . '?' . http_build_query($opts['query']),
      $opts['headers'],
      $opts['body']
    ];
  }

  /**
   * Returns the request object
   * @return object
   */
  public function get()
  {
    return $this->request;
  }
}

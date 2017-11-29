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
    $this->method = $this->validateMehod($method);
    $this->url = $this->validateUrl($url);
    $this->opts = array_merge($this->defaultOpts, $opts);
  }

  protected function validateMehod($method)
  {
    $lower = strtolower($method);
    if (!in_array($lower, $this->methods)) {
      throw new \BadMethodCallException("{$method} is not a valid method.");
    }

    return $method;
  }

  protected function validateUrl($url)
  {
    // only basic checking
    if (!preg_match('/^https?:\/\//i', $url)) {
      throw new \BadMethodCallException("{$url} is not a valid URL.");
    }

    return $url;
  }

  protected function compileArgs()
  {
    return [
      $this->method,
      $this->url . '?' . http_build_query($this->opts['query']),
      $this->opts['headers'],
      $this->opts['body']
    ];
  }

  /**
   * Returns the request object
   * @return object
   */
  public function get()
  {
    if (!$this->request) {
      $args = $this->compileArgs();
      $this->request = new \GuzzleHttp\Psr7\Request(...$args);
    }

    return $this->request;
  }
}

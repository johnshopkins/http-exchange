<?php

namespace HttpExchange\Adapters\Guzzle6;

class RequestTest extends \PHPUnit_Framework_TestCase
{
  public function testInvalidMethod()
  {
    $this->setExpectedException("BadMethodCallException");
    $request = new Request('blah', 'blah');
  }

  public function testInvalidUrl()
  {
    $this->setExpectedException("BadMethodCallException");
    $request = new Request('blah', 'blah');
  }

  public function testValidMethodAndUrlAndDefaultOpts()
  {
    $method = 'get';
    $url = 'http://www.google.com';
    $defaultOpts = [
      'query' => [],
      'headers' => [],
      'body' => null
    ];

    $request = new Request($method, $url);

    $this->assertEquals($method, $request->method);
    $this->assertEquals($url, $request->url);
    $this->assertEquals($defaultOpts, $request->opts);

  }

  public function testPassedOptsWithoutBody()
  {
    $given = [
      'query' => ['one' => 'two'],
      'headers' => ['three' => 'four']
    ];

    $expected = $given;
    $expected['body'] = null;

    $request = new Request('get', 'http://www.google.com', $given);

    $this->assertEquals($expected, $request->opts);
  }

  public function testPassedOptsWithBody()
  {
    $given = $expected = [
      'query' => ['one' => 'two'],
      'headers' => ['three' => 'four'],
      'body' => 'blahblah'
    ];

    $request = new Request('get', 'http://www.google.com', $given);

    $this->assertEquals($expected, $request->opts);
  }
}

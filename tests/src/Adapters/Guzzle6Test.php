<?php

namespace HttpExchange\Adapters;

use GuzzleHttp\Client;
use HttpExchange\Exceptions\HTTP;

class Guzzle6Test extends \HttpExchange\Base
{
  public function setUp(): void
  {
    parent::setUp();

    $this->adapter = new Guzzle6(new Client());
  }

  protected function httpMethod($method)
  {
    $url = "https://httpbin.org/$method";
    return $this->adapter->$method($url);
  }

  protected function httpStatus($method = 'get', $status = 200)
  {
    $url = "https://httpbin.org/status/$status";
    return $this->adapter->$method($url);
  }

  public function testGet()
  {
    $response = $this->httpMethod('get');

    $this->assertEquals("https://httpbin.org/get", $response->getBody()->url);
    $this->assertEquals(200, $response->getStatusCode());
  }

  public function testDelete()
  {
    $response = $this->httpMethod('delete');

    $this->assertEquals("https://httpbin.org/delete", $response->getBody()->url);
    $this->assertEquals(200, $response->getStatusCode());
  }

  // public function testHead()
  // {
  //   $response = $this->httpMethod('head');
  //
  //   $this->assertEquals("https://httpbin.org/head", $response->getBody()->url);
  //   $this->assertEquals(200, $response->getStatusCode());
  // }

  // public function testOptions()
  // {
  //   $response = $this->httpMethod('options');
  //
  //   $this->assertEquals("https://httpbin.org/options", $response->getBody()->url);
  //   $this->assertEquals(200, $response->getStatusCode());
  // }

  public function testPatch()
  {
    $response = $this->httpMethod('patch');

    $this->assertEquals("https://httpbin.org/patch", $response->getBody()->url);
    $this->assertEquals(200, $response->getStatusCode());
  }

  public function testPost()
  {
    $response = $this->httpMethod('post');

    $this->assertEquals("https://httpbin.org/post", $response->getBody()->url);
    $this->assertEquals(200, $response->getStatusCode());
  }

  public function testPut()
  {
    $response = $this->httpMethod('put');

    $this->assertEquals("https://httpbin.org/put", $response->getBody()->url);
    $this->assertEquals(200, $response->getStatusCode());
  }

  public function testBatch()
  {
    $requests = [
      ['get', 'https://httpbin.org/get'],
      ['post', 'https://httpbin.org/post'],
    ];

    $responses = $this->adapter->batch($requests);

    $this->assertEquals("https://httpbin.org/get", $responses[0]->getBody()->url);
    $this->assertEquals(200, $responses[0]->getStatusCode());

    $this->assertEquals("https://httpbin.org/post", $responses[1]->getBody()->url);
    $this->assertEquals(200, $responses[1]->getStatusCode());
  }

  public function testGet__exeption()
  {
    try {
      $this->httpStatus('get', 404);
      $this->fail('HTTP Exception was not thrown');
    } catch (HTTP $e) {
      $data = $e->getAdditionalData();
      $this->assertEquals($data['original_exception_type'], 'GuzzleHttp\Exception\ClientException');
      $this->assertEquals($data['uri'], 'https://httpbin.org/status/404');
    }
  }

  public function testDelete__exeption()
  {
    try {
      $this->httpStatus('delete', 404);
      $this->fail('HTTP Exception was not thrown');
    } catch (HTTP $e) {
      $data = $e->getAdditionalData();
      $this->assertEquals($data['original_exception_type'], 'GuzzleHttp\Exception\ClientException');
      $this->assertEquals($data['uri'], 'https://httpbin.org/status/404');
    }
  }

  // public function testHead()
  // {
  //   $response = $this->httpMethod('head');
  //
  //   $this->assertEquals("https://httpbin.org/head", $response->getBody()->url);
  //   $this->assertEquals(200, $response->getStatusCode());
  // }

  // public function testOptions()
  // {
  //   $response = $this->httpMethod('options');
  //
  //   $this->assertEquals("https://httpbin.org/options", $response->getBody()->url);
  //   $this->assertEquals(200, $response->getStatusCode());
  // }

  public function testPatch__exeption()
  {
    try {
      $this->httpStatus('patch', 404);
      $this->fail('HTTP Exception was not thrown');
    } catch (HTTP $e) {
      $data = $e->getAdditionalData();
      $this->assertEquals($data['original_exception_type'], 'GuzzleHttp\Exception\ClientException');
      $this->assertEquals($data['uri'], 'https://httpbin.org/status/404');
    }
  }

  public function testPost__exeption()
  {
    try {
      $this->httpStatus('post', 404);
      $this->fail('HTTP Exception was not thrown');
    } catch (HTTP $e) {
      $data = $e->getAdditionalData();
      $this->assertEquals($data['original_exception_type'], 'GuzzleHttp\Exception\ClientException');
      $this->assertEquals($data['uri'], 'https://httpbin.org/status/404');
    }
  }

  public function testPut__exeption()
  {
    try {
      $this->httpStatus('put', 404);
      $this->fail('HTTP Exception was not thrown');
    } catch (HTTP $e) {
      $data = $e->getAdditionalData();
      $this->assertEquals($data['original_exception_type'], 'GuzzleHttp\Exception\ClientException');
      $this->assertEquals($data['uri'], 'https://httpbin.org/status/404');
    }
  }

  public function testBatch__exception()
  {
    $requests = [
      ['get', 'https://httpbin.org/get'],
      ['get', 'https://httpbin.org/status/404'],
    ];

    $responses = $this->adapter->batch($requests);

    $this->assertEquals('https://httpbin.org/get', $responses[0]->getBody()->url);
    $this->assertEquals(200, $responses[0]->getStatusCode());

    $this->assertEquals('Client error: `GET https://httpbin.org/status/404` resulted in a `404 NOT FOUND` response', $responses[1]->getMessage());
    $this->assertEquals(404, $responses[1]->getStatusCode());
  }
}

# HTTP Exchange

A collection of PHP HTTP client adapters to make swapping out HTTP client dependencies quick and easy.

Available adapters:

* [Guzzle 6](https://docs.guzzlephp.org/en/6.5/)
* [Guzzle 7](https://docs.guzzlephp.org/en/7.0/)

## Requirements

* PHP >= 8.0
* Guzzle >= 6.5

## Installation

To install the library, you will need to use Composer in your project.

```bash
composer require johnshopkins/http-exchange
```

## Basic usage

### Single request

```php
$client = new GuzzleHttp\Client();
$http = new HttpExchange\Adapters\Guzzle7($client);

$response = $http->get('http://httpbin.org/get');
$body = $response->getBody();
echo $body->url;

// prints: http://httpbin.org/get
```

### Batch requests

```php
$client = new GuzzleHttp\Client();
$http = new HttpExchange\Adapters\Guzzle7($client);

$responses = $http->batch([
  ['get', 'http://httpbin.org/get'],
  ['post', 'http://httpbin.org/post']
]);

foreach ($responses as $response) {
  $body = $response->getBody();
  echo $body->url . "\n";
}

// prints:
// http://httpbin.org/get
// http://httpbin.org/post
```

## Error handling

### Single request

If a request fails, a `HttpExchange\Exceptions\HTTP` exception is thrown. See the [exception methods documentation](#exception-methods) for more information.

```php
$client = new GuzzleHttp\Client();
$http = new HttpExchange\Adapters\Guzzle7($client);

try {
  $response = $http->get('http://httpbin.org/status/503');
  $body = $response->getBody();
} catch (\Exception $e) {
  echo $->getCode() . ': ' . $e->getMessage();
}

// prints:  503: Server error: `GET http://httpbin.org/status/503` resulted in a `503 SERVICE UNAVAILABLE` response
```

### Batch requests

Instead of throwing a `HttpExchange\Exceptions\HTTP` when any one of the requests in a batch request fails, the exception is instead _returned_. This allows your application to gracefully handle failed requests, while processing successful ones.

```php
$client = new GuzzleHttp\Client();
$http = new HttpExchange\Adapters\Guzzle7($client);

$responses = $http->batch([
  ['get', 'http://httpbin.org/get'],
  ['get', 'http://httpbin.org/status/503']
]);

foreach ($responses as $response) {
  if ($response->getStatusCode() === 200) {
    $body = $response->getBody();
    echo $body->url . "\n";
  } else {
    echo $body->url . "This request failed :(\n";
  }
}

// prints:
// http://httpbin.org/get
// This request failed :(
```
Alternatively, you can check which kind of object was returned from each request (`HttpExchange\Response` or `HttpExchange\Exceptions\HTTP`) and proceed accordingly:

```php
$client = new GuzzleHttp\Client();
$http = new HttpExchange\Adapters\Guzzle7($client);

$responses = $http->batch([
  ['get', 'http://httpbin.org/get'],
  ['get', 'http://httpbin.org/status/503']
]);

foreach ($responses as $response) {
  if ($response instanceof HttpExchange\Response) {
    $body = $response->getBody();
    echo $body->url . "\n";
  } else {
    echo $body->url . "This request failed :(\n";
  }
}

// prints:
// http://httpbin.org/get
// This request failed :(
```

## Documentation

### Initialization

#### Guzzle 6
```php
$client = new GuzzleHttp\Client();
$http = new HttpExchange\Adapters\Guzzle6($client);
```

#### Guzzle 7
```php
$client = new GuzzleHttp\Client();
$http = new HttpExchange\Adapters\Guzzle7($client);
```

### Adapter methods

#### __`batch(array $requests)`__

Send multiple requests concurrently.

Returns: array containing the result of each request. A `HttpExchange\Response` object indicates a successful request while a `HttpExchange\Exceptions\HTTP` exception object indicates a failed request.

Arguments:

* `$requests`: An array of requests to make concurrently. Format:
  ```php
  $requests = [[$method, $url, $request_options], ...];
  ```
  * `$method`: HTTP method
  * `$uri`: Request URI
  * `$request_options`: Request options to pass to HTTP client ([Guzzle 6](https://docs.guzzlephp.org/en/6.5/request-options.html) or [Guzzle 7](https://docs.guzzlephp.org/en/7.0/request-options.html))


#### __`get(array $requests)`__

Make a GET request.

Returns: A `HttpExchange\Response` object. Throw a `HttpExchange\Exceptions\HTTP` exception object if the request fails.

Arguments:

* `$method`: HTTP method
* `$uri`: Request URI
* `$request_options`: Request options to pass to HTTP client ([Guzzle 6](https://docs.guzzlephp.org/en/6.5/request-options.html) or [Guzzle 7](https://docs.guzzlephp.org/en/7.0/request-options.html))


#### __`post(array $requests)`__

Make a POST request.

Returns: A `HttpExchange\Response` object. Throw a `HttpExchange\Exceptions\HTTP` exception object if the request fails.

Arguments:

* `$method`: HTTP method
* `$uri`: Request URI
* `$request_options`: Request options to pass to HTTP client ([Guzzle 6](https://docs.guzzlephp.org/en/6.5/request-options.html) or [Guzzle 7](https://docs.guzzlephp.org/en/7.0/request-options.html))


#### __`put(array $requests)`__

Make a PUT request.

Returns: A `HttpExchange\Response` object. Throw a `HttpExchange\Exceptions\HTTP` exception object if the request fails.

Arguments:

* `$method`: HTTP method
* `$uri`: Request URI
* `$request_options`: Request options to pass to HTTP client ([Guzzle 6](https://docs.guzzlephp.org/en/6.5/request-options.html) or [Guzzle 7](https://docs.guzzlephp.org/en/7.0/request-options.html))


#### __`delete(array $requests)`__

Make a DELETE request.

Returns: A `HttpExchange\Response` object. Throw a `HttpExchange\Exceptions\HTTP` exception object if the request fails.

Arguments:

* `$method`: HTTP method
* `$uri`: Request URI
* `$request_options`: Request options to pass to HTTP client ([Guzzle 6](https://docs.guzzlephp.org/en/6.5/request-options.html) or [Guzzle 7](https://docs.guzzlephp.org/en/7.0/request-options.html))


#### __`patch(array $requests)`__

Make a PATCH request.

Returns: A `HttpExchange\Response` object. Throw a `HttpExchange\Exceptions\HTTP` exception object if the request fails.

Arguments:

* `$method`: HTTP method
* `$uri`: Request URI
* `$request_options`: Request options to pass to HTTP client ([Guzzle 6](https://docs.guzzlephp.org/en/6.5/request-options.html) or [Guzzle 7](https://docs.guzzlephp.org/en/7.0/request-options.html))


#### __`head(array $requests)`__

Make a HEAD request.

Returns: A `HttpExchange\Response` object. Throw a `HttpExchange\Exceptions\HTTP` exception object if the request fails.

Arguments:

* `$method`: HTTP method
* `$uri`: Request URI
* `$request_options`: Request options to pass to HTTP client ([Guzzle 6](https://docs.guzzlephp.org/en/6.5/request-options.html) or [Guzzle 7](https://docs.guzzlephp.org/en/7.0/request-options.html))


#### __`options(array $requests)`__

Make an OPTIONS request.

Returns: A `HttpExchange\Response` object. Throw a `HttpExchange\Exceptions\HTTP` exception object if the request fails.

Arguments:

* `$method`: HTTP method
* `$uri`: Request URI
* `$request_options`: Request options to pass to HTTP client ([Guzzle 6](https://docs.guzzlephp.org/en/6.5/request-options.html) or [Guzzle 7](https://docs.guzzlephp.org/en/7.0/request-options.html))


### Response methods

#### __`getBody()`__

Get the body of the response.

Returns: `SimpleXMLElement` object if the response is XML. Object or array if the response is JSON.

#### __`getStatusCode()`__

Get the HTTP status code of the response;

Returns: Integer

### Exception methods

All default methods available on PHP exceptions are available plus:

#### __`getStatusCode()`__

Get the HTTP status code of the response;

Returns: Integer

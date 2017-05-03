<?php

namespace HttpExchange\Adapters;

class Guzzle implements \HttpExchange\Interfaces\ClientInterface
{
	public $http;
	protected $logger;
	public $response;

	public $json_types = array(
		"application/json",
		"text/json",
		"text/x-json",
		"text/javascript"
	);

	public $xml_types = array(
		"application/xml",
		"text/xml",
		"application/rss+xml",
		"application/xhtml+xml",
		"application/atom+xml",
		"application/xslt+xml",
		"application/mathml+xml"
	);

	public function __construct($guzzle, $logger = null)
	{
		$this->http = $guzzle;
		$this->logger = $logger;

		if (defined("ENV") && ENV == "local") {
			// do not try to verify cert on local
			$this->http->setDefaultOption("verify", false);
		}
	}

	protected function log($level = "error", $message, $data = array())
	{
		$method = "add" . ucfirst($level);

		if ($this->logger && method_exists($this->logger, $method)) {
			$this->logger->$method($message, $data);
		}
	}

	public function setCredentials($username, $password)
	{
		$this->http->setCredentials($username, $password);
	}

	public function createRequest($method, $url, $params = null, $header = null, $options = null)
	{
		$args = array(
			"headers" => $headers,
			"query" => $params,
			"exceptions" => false
		);

		if (is_array($options)) {
			$args = array_merge($options, $args);
		}

		return $this->http->createRequest($method, $url, $args);
	}

	/**
	 * Fetch a batch of requests.
	 * @param  array $requests Array of requests created using $this->createRequest
	 * @return array Data returned by each request
	 */
	public function batch($requests)
	{
		$options = array("pool_size" => 5);
		$results = \GuzzleHttp\Pool::batch($this->http, $requests, $options);

		$this->response = $results->getIterator();

		foreach ($this->response as $num => &$response) {

			// evaluate responses for exceptions when we
			// have access to the request info

			if (is_a($response, "Exception")) {

				$request = $requests[$num];

				$this->log("error", "Request in Guzzle BATCH request failed", array(
					"method" => $request->getMethod(),
					"url" => $request->getScheme() . "://" . $request->getHost() . $request->getPath(),
					"params" => $request->getQuery(),
					"error"
				));

				$response = null;
			}
		}

		return $this;
	}

	public function get($url, $params = null, $headers = null, $options = null)
	{
		$args = array(
			"headers" => $headers,
			"query" => $params,
			"exceptions" => false
		);

		if (is_array($options)) {
			$args = array_merge($options, $args);
		}

		try {
			$this->response = $this->http->get($url, $args);
		} catch (\Exception $e) {
			$this->log("error", "Guzzle GET request failed", array(
				"url" => $url,
				"params" => $params,
				"headers" => $headers,
				"error" => $e->getMessage(),
				"url" => $_SERVER["REQUEST_URI"]
			));
			$this->response = null;
		}

		return $this;
	}

	public function post($url, $params = null, $headers = null, $options = null)
	{
		$args = array(
			"headers" => $headers,
			"query" => $params,
			"exceptions" => false
		);

		if (is_array($options)) {
			$args = array_merge($options, $args);
		}

		try {
			$this->response = $this->http->post($url, $args);
		} catch (\Exception $e) {
			$this->log("error", "Guzzle POST request failed", array(
				"url" => $url,
				"params" => $params,
				"headers" => $headers,
				"error" => $e->getMessage()
			));
			$this->response = null;
		}

		return $this;
	}

	public function put($url, $params = null, $headers = null, $options = null)
	{
		$args = array(
			"headers" => $headers,
			"query" => $params,
			"exceptions" => false
		);

		if (is_array($options)) {
			$args = array_merge($options, $args);
		}

		try {
			$this->response = $this->http->put($url, $args);
		} catch (\Exception $e) {
			$this->log("error", "Guzzle PUT request failed", array(
				"url" => $url,
				"params" => $params,
				"headers" => $headers,
				"error" => $e->getMessage()
			));
			$this->response = null;
		}

		return $this;
	}

	public function patch($url, $params = null, $headers = null, $options = null)
	{
		$args = array(
			"headers" => $headers,
			"query" => $params,
			"exceptions" => false
		);

		if (is_array($options)) {
			$args = array_merge($options, $args);
		}

		try {
			$this->response = $this->http->patch($url, $args);
		} catch (\Exception $e) {
			$this->log("error", "Guzzle PATCH request failed", array(
				"url" => $url,
				"params" => $params,
				"headers" => $headers,
				"error" => $e->getMessage()
			));
			$this->response = null;
		}

		return $this;
	}

	public function delete($url, $params = null, $headers = null, $options = null)
	{
		$args = array(
			"headers" => $headers,
			"query" => $params,
			"exceptions" => false
		);

		if (is_array($options)) {
			$args = array_merge($options, $args);
		}

		try {
			$this->response = $this->http->delete($url, $args);
		} catch (\Exception $e) {
			$this->log("error", "Guzzle DELETE request failed", array(
				"url" => $url,
				"params" => $params,
				"headers" => $headers,
				"error" => $e->getMessage()
			));
			$this->response = null;
		}

		return $this;
	}

	public function head($url, $params = null, $headers = null, $options = null)
	{
		$args = array(
			"headers" => $headers,
			"query" => $params,
			"exceptions" => false
		);

		if (is_array($options)) {
			$args = array_merge($options, $args);
		}

		try {
			$this->response = $this->http->head($url, $args);
		} catch (\Exception $e) {
			$this->log("error", "Guzzle HEAD request failed", array(
				"url" => $url,
				"params" => $params,
				"headers" => $headers,
				"error" => $e->getMessage()
			));
			$this->response = null;
		}

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

		if (method_exists($this->response, "getHeaders")) {
			// single response
			return $this->parseBody($this->response);
		} else {
			// batch response
			$responses = array();
			foreach ($this->response as $response) {
				$responses[] = $this->parseBody($response);
			}
			return $responses;
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

		if (isset($headers["content-type"]) && is_array($headers["content-type"])) {
			$contentType = end($headers["content-type"]);
		}

		$contentType = preg_split("/[;\s]+/", $contentType);
		$contentType = $contentType[0];

		$body = (string) $response->getBody();

		if (in_array($contentType, $this->json_types) || strpos($contentType, "+json") !== false) {

			return json_decode($body);

		} elseif (in_array($contentType, $this->xml_types) || strpos($contentType, "+xml") !== false) {

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

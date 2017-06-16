<?php

namespace HttpExchange\Adapters;

class Guzzle6 implements \HttpExchange\Interfaces\ClientInterface
{
	public $http;
	protected $logger;
	public $response;
	protected $debug = false;

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
		$this->debug = $this->http->getConfig("debug");
	}

	protected function log($level = "error", $message, $data = array())
	{
		$method = "add" . ucfirst($level);

		if ($this->logger && method_exists($this->logger, $method)) {
			$this->logger->$method($message, $data);
		}
	}

	public function createAsynsRequest($method, $url, $params = null, $header = null, $options = null)
	{
		$args = array(
			"headers" => $headers,
			"query" => $params
		);

		if (is_array($options)) {
			$args = array_merge($options, $args);
		}

		$method = strtolower($method) . "Async";
		return $this->http->$method($url, $args);
	}

	/**
	 * Fetch a batch of requests.
	 * @param  array $requests Array of requests created using $this->createRequest
	 * @return array Data returned by each request
	 */
	public function batch($requests)
	{
		if ($this->debug) ob_start();

		// Wait for the requests to complete, even if some of them fail
		$this->response = \GuzzleHttp\Promise\settle($requests)->wait();

		if ($this->debug) ob_end_clean();

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

			// start output buffering
			if ($this->debug) ob_start();

			// run method
	    $this->response = $this->http->get($url, $args);

			// end output buffering
	    if ($this->debug) ob_end_clean();

		} catch (\Exception $e) {

			$logData = array(
				"endpoint" => $url,
				"params" => $params,
				"headers" => $headers,
				"error" => $e->getMessage(),
				"url" => $_SERVER["REQUEST_URI"]
			);

			if ($this->debug) {
				$logData["debug"] = ob_get_contents();
				ob_end_clean(); // end output buffering
			}

			$this->log("error", "Guzzle GET request failed", $logData);

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
			if ($this->debug) ob_start();
	    $this->response = $this->http->post($url, $args);
			if ($this->debug) ob_end_clean();
		} catch (\Exception $e) {
			$this->log("error", "Guzzle POST request failed", array(
				"endpoint" => $url,
				"params" => $params,
				"headers" => $headers,
				"error" => $e->getMessage()
			));
			if ($this->debug) ob_end_clean();
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
			if ($this->debug) ob_start();
	    $this->response = $this->http->put($url, $args);
			if ($this->debug) ob_end_clean();
		} catch (\Exception $e) {
			$this->log("error", "Guzzle PUT request failed", array(
				"endpoint" => $url,
				"params" => $params,
				"headers" => $headers,
				"error" => $e->getMessage()
			));
			if ($this->debug) ob_end_clean();
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
			if ($this->debug) ob_start();
	    $this->response = $this->http->patch($url, $args);
			if ($this->debug) ob_end_clean();
		} catch (\Exception $e) {
			$this->log("error", "Guzzle PATCH request failed", array(
				"endpoint" => $url,
				"params" => $params,
				"headers" => $headers,
				"error" => $e->getMessage()
			));
			if ($this->debug) ob_end_clean();
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
			if ($this->debug) ob_start();
	    $this->response = $this->http->delete($url, $args);
			if ($this->debug) ob_end_clean();
		} catch (\Exception $e) {
			$this->log("error", "Guzzle DELETE request failed", array(
				"endpoint" => $url,
				"params" => $params,
				"headers" => $headers,
				"error" => $e->getMessage()
			));
			if ($this->debug) ob_end_clean();
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
			if ($this->debug) ob_start();
	    $this->response = $this->http->head($url, $args);
			if ($this->debug) ob_end_clean();
		} catch (\Exception $e) {
			$this->log("error", "Guzzle HEAD request failed", array(
				"endpoint" => $url,
				"params" => $params,
				"headers" => $headers,
				"error" => $e->getMessage()
			));
			if ($this->debug) ob_end_clean();
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

		if (is_array($this->response)) {
			// batch response
			$responses = array();
			foreach ($this->response as $response) {

				if ($response["state"] != "fulfilled") {
					$responses[] = null;
					continue;
				}

				$responses[] = $this->parseBody($response["value"]);
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

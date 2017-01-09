<?php

namespace HttpExchange\Adapters;

class Guzzle implements \HttpExchange\Interfaces\ClientInterface
{
	public $http;
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

	public function __construct($guzzle)
	{
		$this->http = $guzzle;

		if (defined("ENV") && ENV == "local") {
			// do not try to verify cert on local
			$this->http->setDefaultOption("verify", false);
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

		$this->response = $this->http->get($url, $args);

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

		$this->response = $this->http->post($url, $args);

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

		$this->response = $this->http->put($url, $args);

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

		$this->response = $this->http->patch($url, $args);

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

		$this->response = $this->http->delete($url, $args);

		return $this;
	}

	/**
	 * Get the body of the response, which
	 * can be one response or a pool or responses.
	 * @return mixed
	 */
	public function getBody()
	{
		if (method_exists($this->response, "getHeaders")) {
			// single response
			return $this->parseBody($this->response);
		} else {
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
		if (!method_exists($response, "getHeaders")) {
			error_log("getHeaders method does not exist. Message: " . $response->getMessage());
			// var_dump($response->getMessage()); die();
		}

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
		return $this->response->getStatusCode();
	}

}

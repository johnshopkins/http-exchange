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
	}

	public function setCredentials($username, $password)
	{
		$this->http->setCredentials($username, $password);
	}

	public function get($url, $params = null, $headers = null, $options = null)
	{
		$this->response = $this->http->get($url, array(
			"headers" => $headers,
			"query" => $params,
			"exceptions" => false
		));
		return $this;
	}

	public function post($url, $params = null, $headers = null, $options = null)
	{
		$this->response = $this->http->post($url, array(
			"headers" => $headers,
			"query" => $params,
			"exceptions" => false
		));
		return $this;
	}

	public function put($url, $params = null, $headers = null, $options = null)
	{
		$this->response = $this->http->put($url, array(
			"headers" => $headers,
			"query" => $params,
			"exceptions" => false
		));
		return $this;
	}

	public function patch($url, $params = null, $headers = null, $options = null)
	{
		$this->response = $this->http->patch($url, array(
			"headers" => $headers,
			"query" => $params,
			"exceptions" => false
		));
		return $this;
	}

	public function delete($url, $params = null, $headers = null, $options = null)
	{
		$this->response = $this->http->delete($url, array(
			"headers" => $headers,
			"query" => $params,
			"exceptions" => false
		));
		return $this;
	}

	public function getBody()
	{
		$headers = $this->response->getHeaders();

		// if redirected, we use last Content-Type
		if (is_array($headers["Content-Type"])) {
			$contentType = end($headers["Content-Type"]);
		}

		$contentType = preg_split("/[;\s]+/", $contentType);
		$contentType = $contentType[0];

		$body = (string) $this->response->getBody();

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

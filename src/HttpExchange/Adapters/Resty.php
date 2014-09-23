<?php

namespace HttpExchange\Adapters;

class Resty implements \HttpExchange\Interfaces\ClientInterface
{
	public $http;
	protected $response;

	public function __construct($resty)
	{
		$this->http = $resty;
	}

	public function get($url, $params = null, $headers = null, $options = null)
	{
		$this->response = $this->http->get($url, $params, $headers, $options);
		return $this;
	}

	public function post($url, $params = null, $headers = null, $options = null)
	{
		$this->response = $this->http->post($url, $params, $headers, $options);
		return $this;
	}

	public function patch($url, $params = null, $headers = null, $options = null)
	{
		$this->response = $this->http->patch($url, $params, $headers, $options);
		return $this;
	}

	public function getBody()
	{
		return $this->response["body"];
	}

	public function getStatusCode()
	{
		return $this->response["status"];
	}

}

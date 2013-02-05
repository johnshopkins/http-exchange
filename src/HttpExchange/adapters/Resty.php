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

	public function get($url, $params = null, $headers = null)
	{
		$this->response = $this->http->get($url, $params, $headers);
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
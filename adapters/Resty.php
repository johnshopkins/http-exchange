<?php

namespace HttpExchange\adapters;

class Resty implements \HttpExchange\interfaces\ClientInterface
{
	public $http;
	protected $response;

	public function __construct($resty)
	{
		$this->http = $resty;
	}

	public function get($url)
	{
		$this->response = $this->http->get($url);
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
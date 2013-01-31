<?php

namespace HttpExchange\adapters;

class Guzzle implements \HttpExchange\interfaces\ClientInterface
{
	public $http;
	protected $response;

	public function __construct($guzzle)
	{
		$this->http = $guzzle;
	}

	public function get($url)
	{
		$this->response = $this->http->get($url)->send();
		return $this;
	}

	public function getBody()
	{
		$body = $this->response->getBody();
		return json_decode($body);
	}

	public function getStatusCode()
	{
		return $this->response->getStatusCode();
	}

}
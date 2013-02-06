<?php

namespace HttpExchange\Interfaces;

interface ClientInterface
{
	public function get($url, $params, $headers);
	public function getBody();
	public function getStatusCode();
}
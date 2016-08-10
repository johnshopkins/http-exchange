<?php

namespace HttpExchange\Interfaces;

interface ClientInterface
{
	public function setCredentials($username, $password);
	public function get($url, $data, $headers, $options);
	public function post($url, $data, $headers, $options);
	public function getBody();
	public function getStatusCode();
}

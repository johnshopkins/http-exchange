<?php

namespace HttpExchange\interfaces;

class ClientInterface
{
	public function get($url);
	public function getBody();
	public function getStatusCode();
}
<?php

namespace HttpExchange\Interfaces;

interface ClientInterface
{
	public function __construct($httpEngine);
	public function get($url);
	public function getBody();
	public function getStatusCode();
}
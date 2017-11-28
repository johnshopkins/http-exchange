<?php

namespace HttpExchange\Interfaces;

interface ClientInterface
{
	public function get($url, $opts);
	public function post($url, $opts);
	public function put($url, $opts);
	public function delete($url, $opts);
	public function head($url,$opts);

	public function getBody();
	public function getStatusCode();
}

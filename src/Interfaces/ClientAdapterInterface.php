<?php

namespace HttpExchange\Interfaces;

interface ClientAdapterInterface
{
	public function get(string $uri, array $options = []);
  public function delete(string $uri, array $options = []);
  public function head(string $uri, array $options = []);
  public function options(string $uri, array $options = []);
  public function patch(string $uri, array $options = []);
	public function post(string $uri, array $options = []);
	public function put(string $uri, array $options = []);
  public function batch(array $requests): array;
}

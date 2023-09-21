<?php

namespace HttpExchange\Interfaces;

interface Response
{
  public function getStatusCode();
  public function getBody();
}

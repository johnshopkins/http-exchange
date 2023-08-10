<?php

namespace HttpExchange\Exceptions;

class HTTP extends \Exception
{
  protected array $data = [];

  public function addAdditionalData($data = [])
  {
    $this->data = $data;
  }

  public function getAdditionalData()
  {
    return $this->data;
  }
}

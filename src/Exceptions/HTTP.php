<?php

namespace HttpExchange\Exceptions;

class HTTP extends \Exception
{
  protected array $data;

  public function addAdditionalData($data): void
  {
    $this->data = $data;
  }

  public function getAdditionalData()
  {
    return $this->data;
  }

  public function getStatusCode()
  {
    return $this->getCode();
  }
}

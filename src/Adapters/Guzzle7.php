<?php

namespace HttpExchange\Adapters;

use GuzzleHttp\Promise\Utils;
use HttpExchange\Interfaces\ClientAdapterInterface;

class Guzzle7 extends AdapterBase implements ClientAdapterInterface
{
  protected function resolvePromises($promises): array
  {
    return Utils::settle($promises)->wait();
  }
}

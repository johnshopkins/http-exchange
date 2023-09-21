<?php

namespace HttpExchange\Adapters;

use GuzzleHttp\Promise;
use HttpExchange\Interfaces\ClientAdapterInterface;

class Guzzle6 extends AdapterBase implements ClientAdapterInterface
{
  protected function resolvePromises($promises): array
  {
    try {
      Promise\unwrap($promises);
    } catch (\Exception $e) {
      // fail silently here
    }

    return Promise\settle($promises)->wait();
  }
}

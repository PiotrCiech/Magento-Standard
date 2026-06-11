<?php

declare(strict_types=1);

namespace Rokezzz\Gus\Model;

use GusApi\GusApi;

class GusApiFactory
{
    public function create(string $apiKey, string $environment): GusApi
    {
        return new GusApi($apiKey, $environment);
    }
}

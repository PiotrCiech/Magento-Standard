<?php

declare(strict_types=1);

namespace Rokezzz\Offers\Model;

class OfferAccessTokenGenerator
{
    public function generate(): string
    {
        return bin2hex(random_bytes(32));
    }
}

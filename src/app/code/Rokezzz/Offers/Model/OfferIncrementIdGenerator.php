<?php

declare(strict_types=1);

namespace Rokezzz\Offers\Model;

class OfferIncrementIdGenerator
{
    public function generate(): string
    {
        return sprintf(
            'OFF-%s-%s',
            date('YmdHis'),
            bin2hex(random_bytes(4))
        );
    }
}

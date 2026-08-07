<?php

declare(strict_types=1);

namespace Rokezzz\Gus\Model;

use GusApi\Exception\InvalidUserKeyException;
use GusApi\Exception\NotFoundException;
use Magento\Framework\Exception\LocalizedException;

final class GusApiExceptionMapper
{
    public function toLocalizedException(InvalidUserKeyException|NotFoundException $exception): LocalizedException
    {
        if ($exception instanceof InvalidUserKeyException) {
            return new LocalizedException(__('Invalid GUS API key.'), $exception);
        }

        return new LocalizedException(__('No company found for the given NIP.'), $exception);
    }
}

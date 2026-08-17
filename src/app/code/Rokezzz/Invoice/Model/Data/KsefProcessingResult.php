<?php

declare(strict_types=1);

namespace Rokezzz\Invoice\Model\Data;

final class KsefProcessingResult
{
    public function __construct(
        private readonly ?string $ksefNumber,
        private readonly ?string $upoDownloadUrl,
        private readonly bool $isDuplicate
    ) {
    }

    public function getKsefNumber(): ?string
    {
        return $this->ksefNumber;
    }

    public function getUpoDownloadUrl(): ?string
    {
        return $this->upoDownloadUrl;
    }

    public function isDuplicate(): bool
    {
        return $this->isDuplicate;
    }
}

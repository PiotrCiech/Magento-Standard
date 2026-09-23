<?php

declare(strict_types=1);

namespace Rokezzz\Offers\Model\Quote;

class QuoteRequestOperationContext
{
    private int $depth = 0;

    public function begin(): void
    {
        ++$this->depth;
    }

    public function end(): void
    {
        if ($this->depth > 0) {
            --$this->depth;
        }
    }

    public function isActive(): bool
    {
        return $this->depth > 0;
    }
}

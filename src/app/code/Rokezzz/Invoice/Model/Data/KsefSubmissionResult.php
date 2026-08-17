<?php

declare(strict_types=1);

namespace Rokezzz\Invoice\Model\Data;

final class KsefSubmissionResult
{
    /**
     * @param string[] $errors
     */
    public function __construct(
        private readonly int $total,
        private readonly int $success,
        private readonly int $failed,
        private readonly array $errors = []
    ) {
    }

    public function getTotal(): int
    {
        return $this->total;
    }

    public function getSuccess(): int
    {
        return $this->success;
    }

    public function getFailed(): int
    {
        return $this->failed;
    }

    /**
     * @return string[]
     */
    public function getErrors(): array
    {
        return $this->errors;
    }

    public static function empty(): self
    {
        return new self(0, 0, 0);
    }
}

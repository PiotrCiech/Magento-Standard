<?php

declare(strict_types=1);

namespace Rokezzz\Invoice\Api;

interface KsefClientInterface
{
    public function authenticate(?int $storeId = null): void;

    public function createSession(?int $storeId = null): string;

    /**
     * @return array<string, mixed>
     */
    public function sendInvoice(string $xmlContent, ?int $storeId = null): array;

    /**
     * @return array<string, mixed>
     */
    public function getSessionInvoiceStatus(string $invoiceReferenceNumber, ?int $storeId = null): array;

    public function getUpo(string $ksefNumber, ?int $storeId = null): string;

    public function tryGetUpo(string $ksefNumber, ?int $storeId = null): ?string;

    public function closeSession(?int $storeId = null): void;

    public function getSessionReferenceNumber(): ?string;
}

<?php

declare(strict_types=1);

namespace Rokezzz\Invoices\Model\Ksef;

use Rokezzz\Invoices\Api\KsefClientInterface;

class KsefClient implements KsefClientInterface
{
    public function __construct(
        private readonly KsefAuthenticator $authenticator,
        private readonly KsefSessionManager $sessionManager,
        private readonly KsefInvoiceSender $invoiceSender,
        private readonly KsefInvoiceStatusProvider $invoiceStatusProvider,
        private readonly KsefUpoFetcher $upoFetcher,
        private readonly KsefState $state
    ) {
    }

    public function authenticate(?int $storeId = null): void
    {
        $this->authenticator->authenticate($this->state, $storeId);
    }

    public function createSession(?int $storeId = null): string
    {
        return $this->sessionManager->create($this->state, $storeId);
    }

    public function sendInvoice(string $xmlContent, ?int $storeId = null): array
    {
        return $this->invoiceSender->send($xmlContent, $this->state, $storeId);
    }

    public function getSessionInvoiceStatus(string $invoiceReferenceNumber, ?int $storeId = null): array
    {
        return $this->invoiceStatusProvider->get($invoiceReferenceNumber, $this->state, $storeId);
    }

    public function getUpo(string $ksefNumber, ?int $storeId = null): string
    {
        return $this->upoFetcher->get($ksefNumber, $this->state, $storeId);
    }

    public function tryGetUpo(string $ksefNumber, ?int $storeId = null): ?string
    {
        return $this->upoFetcher->tryGet($ksefNumber, $this->state, $storeId);
    }

    public function closeSession(?int $storeId = null): void
    {
        $this->sessionManager->close($this->state, $storeId);
    }

    public function getSessionReferenceNumber(): ?string
    {
        return $this->state->getSessionReferenceNumber();
    }
}

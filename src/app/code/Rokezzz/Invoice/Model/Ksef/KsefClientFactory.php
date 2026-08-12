<?php

declare(strict_types=1);

namespace Rokezzz\Invoices\Model\Ksef;

use Rokezzz\Invoices\Api\KsefClientInterface;

class KsefClientFactory
{
    public function __construct(
        private readonly KsefAuthenticator $authenticator,
        private readonly KsefSessionManager $sessionManager,
        private readonly KsefInvoiceSender $invoiceSender,
        private readonly KsefInvoiceStatusProvider $invoiceStatusProvider,
        private readonly KsefUpoFetcher $upoFetcher
    ) {
    }

    public function create(): KsefClientInterface
    {
        return new KsefClient(
            $this->authenticator,
            $this->sessionManager,
            $this->invoiceSender,
            $this->invoiceStatusProvider,
            $this->upoFetcher,
            new KsefState()
        );
    }
}

<?php

declare(strict_types=1);

namespace Rokezzz\Invoices\Cron;

use Psr\Log\LoggerInterface;
use Rokezzz\Invoices\Model\Invoice\KsefInvoiceSubmissionService;

class SendInvoicesToKsef
{
    public function __construct(
        private readonly KsefInvoiceSubmissionService $submissionService,
        private readonly LoggerInterface $logger
    ) {
    }

    public function execute(): void
    {
        $result = $this->submissionService->execute();

        if ($result->getTotal() === 0) {
            return;
        }

        $this->logger->info('KSeF invoice submission finished', [
            'total' => $result->getTotal(),
            'success' => $result->getSuccess(),
            'failed' => $result->getFailed(),
            'errors' => $result->getErrors(),
        ]);
    }
}

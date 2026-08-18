<?php

declare(strict_types=1);

namespace Rokezzz\Invoice\Console\Command;

use Magento\Framework\Console\Cli;
use Rokezzz\Invoice\Model\Invoice\KsefInvoiceSubmissionService;
use Symfony\Component\Console\Command\Command;
use Symfony\Component\Console\Input\InputInterface;
use Symfony\Component\Console\Output\OutputInterface;

class SendInvoicesToKsefCommand extends Command
{
    public const COMMAND_NAME = 'rokezzz:invoices:send-ksef';

    public function __construct(
        private readonly KsefInvoiceSubmissionService $submissionService,
        ?string $name = null
    ) {
        parent::__construct($name);
    }

    protected function configure(): void
    {
        $this->setName(self::COMMAND_NAME);
        $this->setDescription('Send pending invoices to KSeF.');
    }

    protected function execute(InputInterface $input, OutputInterface $output): int
    {
        $result = $this->submissionService->execute();

        $output->writeln(sprintf(
            '<info>KSeF submission finished. Total: %d, success: %d, failed: %d</info>',
            $result->getTotal(),
            $result->getSuccess(),
            $result->getFailed()
        ));

        foreach ($result->getErrors() as $error) {
            $output->writeln('<error>' . $error . '</error>');
        }

        return $result->getFailed() > 0 ? Cli::RETURN_FAILURE : Cli::RETURN_SUCCESS;
    }
}

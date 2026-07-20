<?php

declare(strict_types=1);

namespace Rokezzz\Omnibus\Console\Command;

use Magento\Framework\Console\Cli;
use Rokezzz\Omnibus\Model\RecordAllProductPrices;
use Symfony\Component\Console\Command\Command;
use Symfony\Component\Console\Input\InputInterface;
use Symfony\Component\Console\Output\OutputInterface;

class RecordPricesCommand extends Command
{
    private const COMMAND_NAME = 'rokezzz:omnibus:record-prices';

    public function __construct(
        private readonly RecordAllProductPrices $recordAllProductPrices
    ) {
        parent::__construct();
    }

    protected function configure(): void
    {
        $this->setName(self::COMMAND_NAME)
            ->setDescription('Record gross final prices for simple products (Omnibus history backfill).');
    }

    protected function execute(InputInterface $input, OutputInterface $output): int
    {
        $this->recordAllProductPrices->execute();
        $output->writeln('<info>Omnibus price history updated.</info>');

        return Cli::RETURN_SUCCESS;
    }
}

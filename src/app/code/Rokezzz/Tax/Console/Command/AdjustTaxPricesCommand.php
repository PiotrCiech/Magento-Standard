<?php

declare(strict_types=1);

namespace Rokezzz\Tax\Console\Command;

use Magento\Catalog\Model\ResourceModel\Product as ProductResource;
use Rokezzz\Tax\Model\ProductCollectionProvider;
use Symfony\Component\Console\Command\Command;
use Symfony\Component\Console\Input\InputArgument;
use Symfony\Component\Console\Input\InputInterface;
use Symfony\Component\Console\Output\OutputInterface;

class AdjustTaxPricesCommand extends Command
{
    private const PRICE_ATTRIBUTE = 'price';

    public function __construct(
        private readonly ProductCollectionProvider $productCollectionProvider,
        private readonly ProductResource $productResource,
        ?string $name = null
    ) {
        parent::__construct($name);
    }

    protected function configure(): void
    {
        $this->setName('rokezzz:tax:adjust-prices')
            ->setDescription('Adjust all product base prices by percentage')
            ->addArgument(
                'percent',
                InputArgument::REQUIRED,
                'Percentage value, e.g. 23 or 8'
            )
            ->addArgument(
                'operation',
                InputArgument::REQUIRED,
                'Operation: up or down'
            );
    }

    protected function execute(InputInterface $input, OutputInterface $output): int
    {
        $percent = (float) $input->getArgument('percent');
        $operation = strtolower((string) $input->getArgument('operation'));
        $factor = $this->resolveFactor($percent, $operation);

        if ($percent <= 0 || $factor <= 0) {
            $output->writeln('<error>Invalid combination of percent and operation.</error>');
            
            return self::FAILURE;
        }

        $updatedCount = $this->updatePrices($factor);
        $output->writeln(sprintf('<info>Done: %d</info>', $updatedCount));

        return self::SUCCESS;
    }

    private function resolveFactor(float $percent, string $operation): float
    {
        return match ($operation) {
            'up' => 1 + ($percent / 100),
            'down' => 1 - ($percent / 100),
            default => 0,
        };
    }

    private function updatePrices(float $factor): int
    {
        $collection = $this->productCollectionProvider->create();
        $collection->addAttributeToSelect([self::PRICE_ATTRIBUTE]);
        $collection->setStoreId(0);

        $updatedCount = 0;

        foreach ($collection as $product) {
            $currentPrice = (float) $product->getData(self::PRICE_ATTRIBUTE);
            $newPrice = round($currentPrice * $factor, 2);

            $product->setStoreId(0);
            $product->setData(self::PRICE_ATTRIBUTE, $newPrice);
            $this->productResource->saveAttribute($product, self::PRICE_ATTRIBUTE);
            $updatedCount++;
        }

        return $updatedCount;
    }
}
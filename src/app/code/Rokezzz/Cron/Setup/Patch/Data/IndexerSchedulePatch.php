<?php

declare(strict_types=1);

namespace Rokezzz\Cron\Setup\Patch\Data;

use Magento\Framework\Indexer\ConfigInterface;
use Magento\Framework\Indexer\IndexerRegistry;
use Magento\Framework\Setup\Patch\DataPatchInterface;
use Magento\Framework\Setup\Patch\NonTransactionableInterface;

final class IndexerSchedulePatch implements DataPatchInterface, NonTransactionableInterface
{
    public function __construct(
        private readonly ConfigInterface $indexerConfig,
        private readonly IndexerRegistry $indexerRegistry
    ) {
    }

    public function apply(): void
    {
        $indexers = $this->indexerConfig->getIndexers();

        foreach (array_keys($indexers) as $indexerId) {
            $indexer = $this->indexerRegistry->get($indexerId);
            $indexer->setScheduled(true);
        }
    }

    public static function getDependencies(): array
    {
        return [];
    }

    public function getAliases(): array
    {
        return [];
    }
}
<?php

declare(strict_types=1);

namespace Rokezzz\Tax\Model\Config;

use Magento\CatalogSearch\Model\Indexer\Fulltext as FulltextIndexer;
use Magento\Framework\App\Cache\TypeListInterface;
use Magento\Framework\App\Config\ScopeConfigInterface;
use Magento\Framework\App\Config\Storage\WriterInterface;
use Magento\Framework\Indexer\IndexerRegistry;
use Magento\Tax\Model\Config as TaxConfig;

final class PriceDisplayModeSynchronizer
{
    public const CONFIG_PATH = 'tax/rokezzz_display/mode';

    public function __construct(
        private readonly WriterInterface $configWriter,
        private readonly PriceDisplayPaths $priceDisplayPaths,
        private readonly IndexerRegistry $indexerRegistry,
        private readonly TypeListInterface $cacheTypeList
    ) {
    }

    public function normalize(int $mode): int
    {
        if ($mode !== TaxConfig::DISPLAY_TYPE_INCLUDING_TAX
            && $mode !== TaxConfig::DISPLAY_TYPE_EXCLUDING_TAX
        ) {
            return TaxConfig::DISPLAY_TYPE_INCLUDING_TAX;
        }

        return $mode;
    }

    public function sync(
        int $mode,
        string $scope = ScopeConfigInterface::SCOPE_TYPE_DEFAULT,
        int $scopeId = 0,
        bool $invalidateSearchIndex = true
    ): void {
        $value = (string) $this->normalize($mode);

        $this->configWriter->save(self::CONFIG_PATH, $value, $scope, $scopeId);
        $this->applyMagentoDisplayPaths($value, $scope, $scopeId);
        $this->cleanCaches();

        if ($invalidateSearchIndex) {
            $this->indexerRegistry->get(FulltextIndexer::INDEXER_ID)->invalidate();
        }
    }

    public function applyFromAdminField(
        int $mode,
        string $scope = ScopeConfigInterface::SCOPE_TYPE_DEFAULT,
        int $scopeId = 0
    ): void {
        $value = (string) $this->normalize($mode);
        $this->applyMagentoDisplayPaths($value, $scope, $scopeId);
        $this->cleanCaches();
        $this->indexerRegistry->get(FulltextIndexer::INDEXER_ID)->invalidate();
    }

    private function applyMagentoDisplayPaths(string $value, string $scope, int $scopeId): void
    {
        foreach ($this->priceDisplayPaths->getPaths() as $path) {
            $this->configWriter->save($path, $value, $scope, $scopeId);
        }
    }

    private function cleanCaches(): void
    {
        $this->cacheTypeList->cleanType('config');
        $this->cacheTypeList->cleanType('full_page');
    }
}

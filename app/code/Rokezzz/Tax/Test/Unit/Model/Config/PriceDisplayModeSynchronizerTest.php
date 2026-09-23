<?php

declare(strict_types=1);

namespace Rokezzz\Tax\Test\Unit\Model\Config;

use Magento\CatalogSearch\Model\Indexer\Fulltext as FulltextIndexer;
use Magento\Framework\App\Cache\TypeListInterface;
use Magento\Framework\App\Config\ScopeConfigInterface;
use Magento\Framework\App\Config\Storage\WriterInterface;
use Magento\Framework\Indexer\IndexerInterface;
use Magento\Framework\Indexer\IndexerRegistry;
use Magento\Tax\Model\Config as TaxConfig;
use PHPUnit\Framework\MockObject\MockObject;
use PHPUnit\Framework\TestCase;
use Rokezzz\Tax\Model\Config\PriceDisplayModeSynchronizer;
use Rokezzz\Tax\Model\Config\PriceDisplayPaths;

final class PriceDisplayModeSynchronizerTest extends TestCase
{
    private WriterInterface&MockObject $configWriter;
    private IndexerRegistry&MockObject $indexerRegistry;
    private TypeListInterface&MockObject $cacheTypeList;
    private PriceDisplayModeSynchronizer $synchronizer;

    protected function setUp(): void
    {
        $this->configWriter = $this->createMock(WriterInterface::class);
        $this->indexerRegistry = $this->createMock(IndexerRegistry::class);
        $this->cacheTypeList = $this->createMock(TypeListInterface::class);
        $this->synchronizer = new PriceDisplayModeSynchronizer(
            $this->configWriter,
            new PriceDisplayPaths(),
            $this->indexerRegistry,
            $this->cacheTypeList
        );
    }

    public function testNormalizeFallsBackToIncludingTax(): void
    {
        $this->assertSame(
            TaxConfig::DISPLAY_TYPE_INCLUDING_TAX,
            $this->synchronizer->normalize(999)
        );
    }

    public function testSyncWritesModeAndMagentoDisplayPaths(): void
    {
        $expectedPaths = array_merge(
            [PriceDisplayModeSynchronizer::CONFIG_PATH],
            (new PriceDisplayPaths())->getPaths()
        );

        $saved = [];
        $this->configWriter->expects($this->exactly(count($expectedPaths)))
            ->method('save')
            ->willReturnCallback(
                function (string $path, string $value, string $scope, int $scopeId) use (&$saved): void {
                    $saved[] = [$path, $value, $scope, $scopeId];
                }
            );

        $this->cacheTypeList->expects($this->exactly(2))
            ->method('cleanType')
            ->willReturnCallback(function (string $type): void {
                $this->assertContains($type, ['config', 'full_page']);
            });

        $indexer = $this->createMock(IndexerInterface::class);
        $indexer->expects($this->once())->method('invalidate');
        $this->indexerRegistry->expects($this->once())
            ->method('get')
            ->with(FulltextIndexer::INDEXER_ID)
            ->willReturn($indexer);

        $this->synchronizer->sync(TaxConfig::DISPLAY_TYPE_EXCLUDING_TAX);

        $this->assertSame($expectedPaths, array_column($saved, 0));
        foreach ($saved as $row) {
            $this->assertSame((string) TaxConfig::DISPLAY_TYPE_EXCLUDING_TAX, $row[1]);
            $this->assertSame(ScopeConfigInterface::SCOPE_TYPE_DEFAULT, $row[2]);
            $this->assertSame(0, $row[3]);
        }
    }

    public function testSyncCanSkipSearchIndexInvalidation(): void
    {
        $this->configWriter->expects($this->atLeastOnce())->method('save');
        $this->indexerRegistry->expects($this->never())->method('get');

        $this->synchronizer->sync(
            TaxConfig::DISPLAY_TYPE_INCLUDING_TAX,
            invalidateSearchIndex: false
        );
    }

    public function testApplyFromAdminFieldDoesNotRewriteOwnConfigPath(): void
    {
        $savedPaths = [];
        $this->configWriter->expects($this->atLeastOnce())
            ->method('save')
            ->willReturnCallback(function (string $path) use (&$savedPaths): void {
                $savedPaths[] = $path;
            });

        $indexer = $this->createMock(IndexerInterface::class);
        $indexer->expects($this->once())->method('invalidate');
        $this->indexerRegistry->method('get')->willReturn($indexer);

        $this->synchronizer->applyFromAdminField(TaxConfig::DISPLAY_TYPE_INCLUDING_TAX);

        $this->assertNotContains(PriceDisplayModeSynchronizer::CONFIG_PATH, $savedPaths);
        $this->assertContains(TaxConfig::CONFIG_XML_PATH_PRICE_DISPLAY_TYPE, $savedPaths);
    }
}

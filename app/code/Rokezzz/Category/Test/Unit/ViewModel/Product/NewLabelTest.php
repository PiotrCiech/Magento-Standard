<?php

declare(strict_types=1);

namespace Rokezzz\Category\Test\Unit\ViewModel\Product;

use ArrayIterator;
use DateTime;
use Magento\Catalog\Model\Product;
use Magento\Framework\App\CacheInterface;
use Magento\Framework\App\Config\ScopeConfigInterface;
use Magento\Framework\DataObject;
use Magento\Framework\Serialize\SerializerInterface;
use Magento\Framework\Stdlib\DateTime\TimezoneInterface;
use Magento\Sales\Model\ResourceModel\Report\Bestsellers\Collection as BestsellersReportCollection;
use Magento\Store\Api\Data\StoreInterface;
use Magento\Store\Model\StoreManagerInterface;
use PHPUnit\Framework\TestCase;
use Rokezzz\Category\Model\Bestsellers\TopProductIdsProvider;
use Rokezzz\Category\Model\Config;
use Rokezzz\Category\Model\Newest\NewsDateFilter;
use Rokezzz\Category\ViewModel\Product\NewLabel;

final class NewLabelTest extends TestCase
{
    public function testIsNewDelegatesStringDatesToFilter(): void
    {
        $product = $this->createProduct(7, '2026-06-01 00:00:00', '2026-06-30 23:59:59');

        $this->assertTrue($this->createViewModel([])->isNew($product));
    }

    public function testIsNewPassesNullWhenDatesAreNotStrings(): void
    {
        $product = $this->createProduct(7, null, null);

        $this->assertFalse($this->createViewModel([])->isNew($product));
    }

    public function testGetBadgePrefersBestsellerOverNew(): void
    {
        $product = $this->createProduct(7, '2026-06-01 00:00:00', '2026-06-30 23:59:59');

        $badge = $this->createViewModel([7, 9], true)->getBadge($product);

        $this->assertSame('product-label--bestseller', $badge['css']);
    }

    public function testGetBadgeFallsBackToNewWhenNotBestseller(): void
    {
        $product = $this->createProduct(7, '2026-06-01 00:00:00', '2026-06-30 23:59:59');

        $badge = $this->createViewModel([1, 2], true)->getBadge($product);

        $this->assertSame('product-label--new', $badge['css']);
    }

    public function testGetBadgeReturnsNullWhenNothingApplies(): void
    {
        $product = $this->createProduct(7, null, null);

        $this->assertNull($this->createViewModel([], true)->getBadge($product));
    }

    public function testGetDiscountLabelReturnsNullWhenDisabled(): void
    {
        $product = $this->createProduct(7, null, null, 100.0, 70.0);

        $this->assertNull($this->createViewModel([], false, false)->getDiscountLabel($product));
    }

    public function testGetDiscountLabelReturnsPercentOff(): void
    {
        $product = $this->createProduct(7, null, null, 100.0, 70.0);

        $this->assertSame('-30%', $this->createViewModel([], false, true)->getDiscountLabel($product));
    }

    public function testGetDiscountLabelReturnsNullWhenNoPromotion(): void
    {
        $product = $this->createProduct(7, null, null, 100.0, 100.0);

        $this->assertNull($this->createViewModel([], false, true)->getDiscountLabel($product));
    }

    /**
     * @param int[] $bestsellerIds
     */
    private function createViewModel(array $bestsellerIds, bool $badgeEnabled = false, bool $discountEnabled = false): NewLabel
    {
        return new NewLabel(
            $this->createFilter(),
            $this->createTopIdsProvider($bestsellerIds),
            $this->createConfig($badgeEnabled, $discountEnabled)
        );
    }

    private function createFilter(): NewsDateFilter
    {
        $localeDate = $this->createStub(TimezoneInterface::class);
        $localeDate->method('date')->willReturn(new DateTime('2026-06-15 12:00:00'));

        return new NewsDateFilter($localeDate);
    }

    /**
     * @param int[] $ids
     */
    private function createTopIdsProvider(array $ids): TopProductIdsProvider
    {
        $reportCollection = $this->createStub(BestsellersReportCollection::class);
        $reportCollection->method('getIterator')->willReturn(new ArrayIterator(
            array_map(static fn (int $id): DataObject => new DataObject(['product_id' => $id]), $ids)
        ));

        $cache = $this->createStub(CacheInterface::class);
        $cache->method('load')->willReturn(false);

        $serializer = $this->createStub(SerializerInterface::class);

        $store = $this->createStub(StoreInterface::class);
        $store->method('getId')->willReturn(1);
        $storeManager = $this->createStub(StoreManagerInterface::class);
        $storeManager->method('getStore')->willReturn($store);

        return new TopProductIdsProvider($reportCollection, $cache, $serializer, $storeManager);
    }

    private function createConfig(bool $badgeEnabled, bool $discountEnabled): Config
    {
        $scopeConfig = $this->createStub(ScopeConfigInterface::class);
        $scopeConfig->method('isSetFlag')->willReturnCallback(
            static fn (string $path): bool => str_contains($path, 'enable_discount_badge')
                ? $discountEnabled
                : $badgeEnabled
        );

        return new Config($scopeConfig);
    }

    private function createProduct(
        int $id,
        ?string $from,
        ?string $to,
        float $price = 0.0,
        float $finalPrice = 0.0
    ): Product {
        $product = $this->getMockBuilder(Product::class)
            ->disableOriginalConstructor()
            ->onlyMethods(['getId', 'getPrice', 'getFinalPrice'])
            ->getMock();
        $product->method('getId')->willReturn($id);
        $product->method('getPrice')->willReturn($price);
        $product->method('getFinalPrice')->willReturn($finalPrice);
        $product->setData('news_from_date', $from);
        $product->setData('news_to_date', $to);

        return $product;
    }
}

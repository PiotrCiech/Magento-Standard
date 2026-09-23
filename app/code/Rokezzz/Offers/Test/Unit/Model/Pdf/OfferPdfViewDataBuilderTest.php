<?php

declare(strict_types=1);

namespace Rokezzz\Offers\Test\Unit\Model\Pdf;

use Magento\Customer\Api\CustomerRepositoryInterface;
use Magento\Customer\Api\Data\CustomerInterface;
use Magento\Framework\Exception\LocalizedException;
use Magento\Framework\Pricing\PriceCurrencyInterface;
use Magento\Framework\Stdlib\DateTime\TimezoneInterface;
use Magento\Store\Model\StoreManagerInterface;
use PHPUnit\Framework\MockObject\MockObject;
use PHPUnit\Framework\TestCase;
use Rokezzz\Offers\Api\OfferProviderInterface;
use Rokezzz\Offers\Model\Pdf\OfferPdfViewDataBuilder;
use Rokezzz\Offers\Test\Unit\Support\CreatesOfferEntities;

final class OfferPdfViewDataBuilderTest extends TestCase
{
    use CreatesOfferEntities;

    private OfferProviderInterface&MockObject $offerProvider;
    private CustomerRepositoryInterface&MockObject $customerRepository;
    private StoreManagerInterface&MockObject $storeManager;
    private PriceCurrencyInterface&MockObject $priceCurrency;
    private TimezoneInterface&MockObject $timezone;
    private OfferPdfViewDataBuilder $offerPdfViewDataBuilder;

    protected function setUp(): void
    {
        $this->offerProvider = $this->createMock(OfferProviderInterface::class);
        $this->customerRepository = $this->createMock(CustomerRepositoryInterface::class);
        $this->storeManager = $this->createMock(StoreManagerInterface::class);
        $this->priceCurrency = $this->createMock(PriceCurrencyInterface::class);
        $this->timezone = $this->createMock(TimezoneInterface::class);

        $this->offerPdfViewDataBuilder = new OfferPdfViewDataBuilder(
            $this->offerProvider,
            $this->customerRepository,
            $this->storeManager,
            $this->priceCurrency,
            $this->timezone
        );
    }

    public function testBuildReturnsFormattedOfferData(): void
    {
        $offer = $this->createOfferModel([
            'entity_id' => 10,
            'increment_id' => 'OFF-00010',
            'customer_id' => 5,
            'customer_note' => 'Need 50 units',
            'admin_note' => 'Special pricing',
            'expires_at' => '2026-12-31 23:59:59',
        ]);

        $offerItem = $this->createOfferItemModel([
            'sku' => 'SKU-1',
            'name' => 'Sample product',
            'offered_qty' => 2,
            'offered_price' => 100.0,
        ]);

        $customer = $this->createMock(CustomerInterface::class);
        $customer->method('getStoreId')->willReturn(1);
        $customer->method('getFirstname')->willReturn('Jan');
        $customer->method('getLastname')->willReturn('Kowalski');

        $this->customerRepository->method('getById')->with(5)->willReturn($customer);
        $this->offerProvider->method('getItemsByOfferId')->with(10)->willReturn([$offerItem]);
        $this->priceCurrency->method('format')->willReturnCallback(
            static fn (float $amount): string => number_format($amount, 2, '.', '') . ' PLN'
        );
        $this->timezone->method('formatDate')->willReturn('2026-12-31');

        $result = $this->offerPdfViewDataBuilder->build($offer);

        $this->assertSame('OFF-00010', $result['increment_id']);
        $this->assertSame('Jan Kowalski', $result['customer_name']);
        $this->assertSame('200.00 PLN', $result['total']);
        $this->assertSame('System powered by Rokezzz', $result['platform_signature']);
        $this->assertCount(1, $result['items']);
        $this->assertSame('SKU-1', $result['items'][0]['sku']);
        $this->assertSame('200.00 PLN', $result['items'][0]['line_total']);
    }

    public function testBuildThrowsWhenOfferHasNoEntityId(): void
    {
        $offer = $this->createOfferModel();

        $this->expectException(LocalizedException::class);

        $this->offerPdfViewDataBuilder->build($offer);
    }
}

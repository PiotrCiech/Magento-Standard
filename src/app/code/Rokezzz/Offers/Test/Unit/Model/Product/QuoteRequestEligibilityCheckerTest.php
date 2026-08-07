<?php

declare(strict_types=1);

namespace Rokezzz\Offers\Test\Unit\Model\Product;

use Magento\Catalog\Api\Data\ProductInterface;
use Magento\Catalog\Model\Product;
use Magento\Framework\DataObject;
use Magento\Framework\Exception\LocalizedException;
use PHPUnit\Framework\MockObject\MockObject;
use PHPUnit\Framework\TestCase;
use Rokezzz\Offers\Model\Product\QuoteRequestEligibilityChecker;
use Rokezzz\Offers\Model\Quote\QuoteRequestQuoteItemManager;

final class QuoteRequestEligibilityCheckerTest extends TestCase
{
    private QuoteRequestEligibilityChecker $quoteRequestEligibilityChecker;

    protected function setUp(): void
    {
        $this->quoteRequestEligibilityChecker = new QuoteRequestEligibilityChecker();
    }

    public function testRequiresQuoteRequestReturnsTrueForNonSalableProduct(): void
    {
        $product = $this->createProductMock(false);

        $this->assertTrue($this->quoteRequestEligibilityChecker->requiresQuoteRequest($product));
    }

    public function testRequiresQuoteRequestReturnsFalseForSalableProduct(): void
    {
        $product = $this->createProductMock(true);

        $this->assertFalse($this->quoteRequestEligibilityChecker->requiresQuoteRequest($product));
    }

    public function testRequiresQuoteRequestReturnsFalseForNonProductInterfaceImplementation(): void
    {
        $product = $this->createMock(ProductInterface::class);

        $this->assertFalse($this->quoteRequestEligibilityChecker->requiresQuoteRequest($product));
    }

    public function testAssertCanAddViaStandardCartAllowsQuoteRequestBuyRequest(): void
    {
        $product = $this->createProductMock(false);
        $buyRequest = new DataObject([
            QuoteRequestQuoteItemManager::BUY_REQUEST_FLAG => 1,
        ]);

        $this->quoteRequestEligibilityChecker->assertCanAddViaStandardCart($product, $buyRequest);

        $this->assertTrue(true);
    }

    public function testAssertCanAddViaStandardCartThrowsForOutOfStockWithoutQuoteRequestFlag(): void
    {
        $product = $this->createProductMock(false);

        $this->expectException(LocalizedException::class);

        $this->quoteRequestEligibilityChecker->assertCanAddViaStandardCart($product, null);
    }

    private function createProductMock(bool $isSalable): Product&MockObject
    {
        $product = $this->createMock(Product::class);
        $product->method('isSalable')->willReturn($isSalable);

        return $product;
    }
}

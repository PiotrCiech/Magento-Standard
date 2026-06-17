<?php

declare(strict_types=1);

namespace Rokezzz\Omnibus\Test\Unit\Model;

use Magento\Catalog\Api\Data\ProductInterface;
use Magento\Catalog\Model\Product\Type;
use PHPUnit\Framework\TestCase;
use Rokezzz\Omnibus\Model\SimpleProductEligibility;

class SimpleProductEligibilityTest extends TestCase
{
    private SimpleProductEligibility $simpleProductEligibility;

    protected function setUp(): void
    {
        $this->simpleProductEligibility = new SimpleProductEligibility();
    }

    public function testIsEligibleReturnsTrueForSimpleProductWithId(): void
    {
        $product = $this->createStub(ProductInterface::class);
        $product->method('getTypeId')->willReturn(Type::TYPE_SIMPLE);
        $product->method('getId')->willReturn(42);

        $this->assertTrue($this->simpleProductEligibility->isEligible($product));
    }

    public function testIsEligibleReturnsFalseForNonSimpleProduct(): void
    {
        $product = $this->createStub(ProductInterface::class);
        $product->method('getTypeId')->willReturn('configurable');
        $product->method('getId')->willReturn(42);

        $this->assertFalse($this->simpleProductEligibility->isEligible($product));
    }
}

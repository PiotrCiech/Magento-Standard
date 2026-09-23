<?php

declare(strict_types=1);

namespace Rokezzz\Offers\Test\Unit\Plugin\CatalogInventory\Quote\Item;

use Magento\CatalogInventory\Model\Quote\Item\QuantityValidator;
use Magento\Framework\DataObject;
use Magento\Framework\Event\Observer;
use Magento\Quote\Model\Quote\Item\AbstractItem;
use PHPUnit\Framework\MockObject\MockObject;
use PHPUnit\Framework\TestCase;
use Rokezzz\Offers\Model\Quote\QuoteRequestOperationContext;
use Rokezzz\Offers\Model\Quote\QuoteRequestQuoteItemManager;
use Rokezzz\Offers\Plugin\CatalogInventory\Quote\Item\SkipStockValidationForQuoteRequestPlugin;

final class SkipStockValidationForQuoteRequestPluginTest extends TestCase
{
    private QuoteRequestOperationContext $quoteRequestOperationContext;
    private QuoteRequestQuoteItemManager&MockObject $quoteRequestQuoteItemManager;
    private SkipStockValidationForQuoteRequestPlugin $plugin;

    protected function setUp(): void
    {
        $this->quoteRequestOperationContext = new QuoteRequestOperationContext();
        $this->quoteRequestQuoteItemManager = $this->createMock(QuoteRequestQuoteItemManager::class);
        $this->plugin = new SkipStockValidationForQuoteRequestPlugin(
            $this->quoteRequestOperationContext,
            $this->quoteRequestQuoteItemManager
        );
    }

    public function testSkipsValidationDuringTrustedQuoteRequestOperation(): void
    {
        $this->quoteRequestOperationContext->begin();
        $proceeded = false;

        $result = $this->plugin->aroundValidate(
            $this->createMock(QuantityValidator::class),
            static function () use (&$proceeded): void {
                $proceeded = true;
            },
            $this->createObserver()
        );

        $this->assertNull($result);
        $this->assertFalse($proceeded);
    }

    public function testSkipsValidationForPersistedQuoteRequestItem(): void
    {
        $item = $this->createMock(AbstractItem::class);
        $this->quoteRequestQuoteItemManager->method('isQuoteRequestItem')->with($item)->willReturn(true);
        $proceeded = false;

        $result = $this->plugin->aroundValidate(
            $this->createMock(QuantityValidator::class),
            static function () use (&$proceeded): void {
                $proceeded = true;
            },
            $this->createObserver($item)
        );

        $this->assertNull($result);
        $this->assertFalse($proceeded);
    }

    public function testRunsCoreValidationForRegularItem(): void
    {
        $item = $this->createMock(AbstractItem::class);
        $this->quoteRequestQuoteItemManager->method('isQuoteRequestItem')->with($item)->willReturn(false);

        $result = $this->plugin->aroundValidate(
            $this->createMock(QuantityValidator::class),
            static fn (Observer $observer): string => 'validated',
            $this->createObserver($item)
        );

        $this->assertSame('validated', $result);
    }

    private function createObserver(?AbstractItem $item = null): Observer
    {
        return new Observer([
            'event' => new DataObject(['item' => $item]),
        ]);
    }
}

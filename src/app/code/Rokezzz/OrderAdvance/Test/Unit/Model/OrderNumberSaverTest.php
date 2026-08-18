<?php

declare(strict_types=1);

namespace Rokezzz\OrderAdvance\Test\Unit\Model;

use Magento\Framework\App\Config\ScopeConfigInterface;
use Magento\Quote\Api\CartRepositoryInterface;
use Magento\Quote\Model\Quote;
use PHPUnit\Framework\MockObject\MockObject;
use PHPUnit\Framework\TestCase;
use Rokezzz\OrderAdvance\Model\Config;
use Rokezzz\OrderAdvance\Model\OrderNumberSaver;

final class OrderNumberSaverTest extends TestCase
{
    private CartRepositoryInterface&MockObject $quoteRepository;

    protected function setUp(): void
    {
        $this->quoteRepository = $this->createMock(CartRepositoryInterface::class);
    }

    public function testSaveSkipsEmptyNumber(): void
    {
        $this->quoteRepository->expects($this->never())->method('getActive');

        $this->createSaver(64)->save(10, '   ');
    }

    public function testSaveStoresTrimmedNumberOnQuote(): void
    {
        $quote = $this->createMock(Quote::class);
        $quote->expects($this->once())
            ->method('setData')
            ->with(OrderNumberSaver::QUOTE_FIELD, 'PO-99');
        $this->quoteRepository->method('getActive')->with(10)->willReturn($quote);
        $this->quoteRepository->expects($this->once())->method('save')->with($quote);

        $this->createSaver(64)->save(10, '  PO-99  ');
    }

    public function testSaveTruncatesNumberToConfiguredLength(): void
    {
        $quote = $this->createMock(Quote::class);
        $quote->expects($this->once())
            ->method('setData')
            ->with(OrderNumberSaver::QUOTE_FIELD, '12345');
        $this->quoteRepository->method('getActive')->willReturn($quote);

        $this->createSaver(5)->save(10, '123456789');
    }

    private function createSaver(int $maxLength): OrderNumberSaver
    {
        $scopeConfig = $this->createStub(ScopeConfigInterface::class);
        $scopeConfig->method('getValue')->willReturn($maxLength);

        return new OrderNumberSaver($this->quoteRepository, new Config($scopeConfig));
    }
}

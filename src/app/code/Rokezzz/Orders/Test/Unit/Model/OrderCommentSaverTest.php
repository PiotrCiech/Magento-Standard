<?php

declare(strict_types=1);

namespace Rokezzz\Orders\Test\Unit\Model;

use Magento\Quote\Api\CartRepositoryInterface;
use Magento\Quote\Api\Data\CartInterface;
use PHPUnit\Framework\MockObject\MockObject;
use PHPUnit\Framework\TestCase;
use Rokezzz\Orders\Model\Config;
use Rokezzz\Orders\Model\OrderCommentSaver;

final class OrderCommentSaverTest extends TestCase
{
    private CartRepositoryInterface&MockObject $quoteRepository;
    private Config&MockObject $config;

    protected function setUp(): void
    {
        $this->quoteRepository = $this->createMock(CartRepositoryInterface::class);
        $this->config = $this->createMock(Config::class);
    }

    public function testSaveSkipsEmptyComment(): void
    {
        $this->quoteRepository->expects($this->never())->method('getActive');

        (new OrderCommentSaver($this->quoteRepository, $this->config))->save(10, '   ');
    }

    public function testSaveStoresTrimmedCommentOnQuote(): void
    {
        $quote = $this->createMock(CartInterface::class);
        $quote->expects($this->once())->method('setCustomerNote')->with('Please pack carefully');
        $this->quoteRepository->method('getActive')->with(10)->willReturn($quote);
        $this->quoteRepository->expects($this->once())->method('save')->with($quote);
        $this->config->method('getMaxLength')->willReturn(1000);

        (new OrderCommentSaver($this->quoteRepository, $this->config))->save(10, '  Please pack carefully  ');
    }

    public function testSaveTruncatesCommentToConfiguredLength(): void
    {
        $quote = $this->createMock(CartInterface::class);
        $quote->expects($this->once())->method('setCustomerNote')->with('12345');
        $this->quoteRepository->method('getActive')->willReturn($quote);
        $this->config->method('getMaxLength')->willReturn(5);

        (new OrderCommentSaver($this->quoteRepository, $this->config))->save(10, '123456789');
    }
}

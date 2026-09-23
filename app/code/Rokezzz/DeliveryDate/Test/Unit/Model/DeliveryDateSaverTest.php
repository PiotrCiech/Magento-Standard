<?php

declare(strict_types=1);

namespace Rokezzz\DeliveryDate\Test\Unit\Model;

use Magento\Quote\Api\CartRepositoryInterface;
use Magento\Quote\Model\Quote;
use PHPUnit\Framework\MockObject\MockObject;
use PHPUnit\Framework\TestCase;
use Rokezzz\DeliveryDate\Model\DeliveryDateSaver;
use Rokezzz\DeliveryDate\Model\DeliveryDateValidator;

final class DeliveryDateSaverTest extends TestCase
{
    private CartRepositoryInterface&MockObject $quoteRepository;
    private DeliveryDateValidator&MockObject $deliveryDateValidator;

    protected function setUp(): void
    {
        $this->quoteRepository = $this->createMock(CartRepositoryInterface::class);
        $this->deliveryDateValidator = $this->createMock(DeliveryDateValidator::class);
    }

    public function testSaveSkipsWhenValidatedDateIsNull(): void
    {
        $this->deliveryDateValidator->method('validate')->with('')->willReturn(null);
        $this->quoteRepository->expects($this->never())->method('getActive');

        (new DeliveryDateSaver($this->quoteRepository, $this->deliveryDateValidator))->save(10, '');
    }

    public function testSaveStoresValidatedDateOnQuote(): void
    {
        $quote = $this->createMock(Quote::class);
        $quote->expects($this->once())
            ->method('setData')
            ->with(DeliveryDateSaver::QUOTE_FIELD, '2026-07-15');
        $this->deliveryDateValidator->method('validate')->willReturn('2026-07-15');
        $this->quoteRepository->method('getActive')->with(10)->willReturn($quote);
        $this->quoteRepository->expects($this->once())->method('save')->with($quote);

        (new DeliveryDateSaver($this->quoteRepository, $this->deliveryDateValidator))->save(10, '2026-07-15');
    }

    public function testReadFromQuoteReturnsTrimmedValue(): void
    {
        $quote = $this->createMock(Quote::class);
        $quote->method('getData')->with(DeliveryDateSaver::QUOTE_FIELD)->willReturn(' 2026-07-15 ');

        $this->assertSame(
            '2026-07-15',
            (new DeliveryDateSaver($this->quoteRepository, $this->deliveryDateValidator))->readFromQuote($quote)
        );
    }
}

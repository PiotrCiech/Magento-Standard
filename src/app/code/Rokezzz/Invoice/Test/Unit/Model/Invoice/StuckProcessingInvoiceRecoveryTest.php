<?php

declare(strict_types=1);

namespace Rokezzz\Invoice\Test\Unit\Model\Invoice;

use ArrayIterator;
use Magento\Sales\Api\InvoiceRepositoryInterface;
use Magento\Sales\Model\Order\Invoice;
use Magento\Sales\Model\ResourceModel\Order\Invoice\Collection;
use PHPUnit\Framework\MockObject\MockObject;
use PHPUnit\Framework\TestCase;
use Rokezzz\Invoice\Model\Invoice\InvoiceKsefStatusWriter;
use Rokezzz\Invoice\Model\Invoice\KsefUpoStatus;
use Rokezzz\Invoice\Model\Invoice\StuckProcessingInvoiceRecovery;
use Rokezzz\Invoice\Model\InvoiceCollectionCreator;

final class StuckProcessingInvoiceRecoveryTest extends TestCase
{
    private InvoiceCollectionCreator&MockObject $invoiceCollectionCreator;
    private InvoiceRepositoryInterface&MockObject $invoiceRepository;
    private StuckProcessingInvoiceRecovery $recovery;

    protected function setUp(): void
    {
        $this->invoiceCollectionCreator = $this->createMock(InvoiceCollectionCreator::class);
        $this->invoiceRepository = $this->createMock(InvoiceRepositoryInterface::class);
        $this->recovery = new StuckProcessingInvoiceRecovery(
            $this->invoiceCollectionCreator,
            new InvoiceKsefStatusWriter($this->invoiceRepository)
        );
    }

    public function testResetStuckInvoicesReturnsZeroWhenNoEntityIds(): void
    {
        $this->invoiceCollectionCreator->expects($this->never())->method('create');

        $this->assertSame(0, $this->recovery->resetStuckInvoices([]));
    }

    public function testResetStuckInvoicesMarksMatchingInvoicesAsTodo(): void
    {
        $invoice = $this->getMockBuilder(Invoice::class)
            ->disableOriginalConstructor()
            ->onlyMethods(['getEntityId', 'setData'])
            ->getMock();
        $invoice->method('getEntityId')->willReturn(15);
        $invoice->expects($this->once())
            ->method('setData')
            ->with('rokezzz_ksef_upo_status', KsefUpoStatus::Todo->value);

        $collection = $this->createMock(Collection::class);
        $collection->method('addFieldToFilter')->willReturnSelf();
        $collection->method('getIterator')->willReturn(new ArrayIterator([$invoice]));
        $this->invoiceCollectionCreator->method('create')->willReturn($collection);
        $this->invoiceRepository->expects($this->once())->method('save')->with($invoice);

        $this->assertSame(1, $this->recovery->resetStuckInvoices([$invoice]));
    }
}

<?php

declare(strict_types=1);

namespace Rokezzz\Orders\Test\Unit\ViewModel\Order;

use Magento\Sales\Api\Data\OrderInterface;
use PHPUnit\Framework\TestCase;
use Rokezzz\Orders\ViewModel\Order\Comment;

final class CommentViewModelTest extends TestCase
{
    public function testShouldDisplayReturnsFalseForEmptyNote(): void
    {
        $order = $this->createMock(OrderInterface::class);
        $order->method('getCustomerNote')->willReturn('   ');

        $this->assertFalse((new Comment())->shouldDisplay($order));
    }

    public function testGetCustomerNoteReturnsTrimmedValue(): void
    {
        $order = $this->createMock(OrderInterface::class);
        $order->method('getCustomerNote')->willReturn('  Pack carefully  ');

        $this->assertSame('Pack carefully', (new Comment())->getCustomerNote($order));
    }
}

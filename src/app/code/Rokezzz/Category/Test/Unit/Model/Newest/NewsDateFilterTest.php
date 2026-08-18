<?php

declare(strict_types=1);

namespace Rokezzz\Category\Test\Unit\Model\Newest;

use DateTime;
use Magento\Framework\Stdlib\DateTime\TimezoneInterface;
use PHPUnit\Framework\TestCase;
use Rokezzz\Category\Model\Newest\NewsDateFilter;

final class NewsDateFilterTest extends TestCase
{
    private NewsDateFilter $filter;

    protected function setUp(): void
    {
        $localeDate = $this->createStub(TimezoneInterface::class);
        $localeDate->method('date')->willReturn(new DateTime('2026-06-15 12:00:00'));
        $this->filter = new NewsDateFilter($localeDate);
    }

    public function testIsCurrentlyNewReturnsFalseWhenBothDatesAreEmpty(): void
    {
        $this->assertFalse($this->filter->isCurrentlyNew(null, null));
        $this->assertFalse($this->filter->isCurrentlyNew('', ''));
    }

    public function testIsCurrentlyNewReturnsFalseWhenFromDateIsInTheFuture(): void
    {
        $this->assertFalse($this->filter->isCurrentlyNew('2026-06-16 00:00:00', null));
    }

    public function testIsCurrentlyNewReturnsFalseWhenToDateIsInThePast(): void
    {
        $this->assertFalse($this->filter->isCurrentlyNew(null, '2026-06-14 23:59:59'));
    }

    public function testIsCurrentlyNewReturnsTrueWhenNowIsInsideRange(): void
    {
        $this->assertTrue($this->filter->isCurrentlyNew('2026-06-01 00:00:00', '2026-06-30 23:59:59'));
    }
}

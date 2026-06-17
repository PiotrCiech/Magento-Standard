<?php

declare(strict_types=1);

namespace Rokezzz\ResubmittingArchivedOrders\Test\Unit\Model;

use PHPUnit\Framework\TestCase;
use Rokezzz\ResubmittingArchivedOrders\Model\ArchiveEligibleOrders;
use Rokezzz\ResubmittingArchivedOrders\Model\Config;
use Rokezzz\ResubmittingArchivedOrders\Model\ResourceModel\OrderArchive;

class ArchiveEligibleOrdersTest extends TestCase
{
    public function testExecuteReturnsZeroWhenFeatureDisabled(): void
    {
        $config = $this->createStub(Config::class);
        $config->method('isEnabled')->willReturn(false);

        $service = new ArchiveEligibleOrders($config, $this->createStub(OrderArchive::class));

        $this->assertSame(0, $service->execute());
    }

    public function testExecuteReturnsZeroWhenArchiveAgeIsInvalid(): void
    {
        $config = $this->createStub(Config::class);
        $config->method('isEnabled')->willReturn(true);
        $config->method('getArchiveAgeDays')->willReturn(0);
        $config->method('getArchiveStatuses')->willReturn(['complete']);

        $service = new ArchiveEligibleOrders($config, $this->createStub(OrderArchive::class));

        $this->assertSame(0, $service->execute());
    }

    public function testExecuteArchivesOrdersWhenConfigurationIsValid(): void
    {
        $config = $this->createStub(Config::class);
        $config->method('isEnabled')->willReturn(true);
        $config->method('getArchiveAgeDays')->willReturn(30);
        $config->method('getArchiveStatuses')->willReturn(['complete']);

        $orderArchive = $this->createMock(OrderArchive::class);
        $orderArchive->expects($this->once())
            ->method('archiveOrdersCreatedBefore')
            ->with($this->isString(), ['complete'])
            ->willReturn(5);

        $service = new ArchiveEligibleOrders($config, $orderArchive);

        $this->assertSame(5, $service->execute());
    }
}

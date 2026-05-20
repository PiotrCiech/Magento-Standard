<?php

declare(strict_types=1);

namespace Rokezzz\Base\Test\Unit\Console\Command;

use Magento\Catalog\Model\ResourceModel\Product as ProductResource;
use Magento\Catalog\Model\ResourceModel\Product\CollectionFactory;
use PHPUnit\Framework\TestCase;
use Rokezzz\Base\Console\Command\AdjustTaxPricesCommand;
use Symfony\Component\Console\Command\Command;
use Symfony\Component\Console\Tester\CommandTester;

class AdjustTaxPricesCommandTest extends TestCase
{
    public function testExecuteFailsWhenFirstArgumentIsInvalid(): void
    {
        $tester = new CommandTester($this->createCommand());
        $tester->execute(['percent' => '0', 'operation' => 'up']);

        $this->assertSame(Command::FAILURE, $tester->getStatusCode());
        $this->assertStringContainsString('Invalid combination', $tester->getDisplay());
    }

    public function testExecuteFailsWhenSecondArgumentIsInvalid(): void
    {
        $tester = new CommandTester($this->createCommand());
        $tester->execute(['percent' => '10', 'operation' => 'sideways']);

        $this->assertSame(Command::FAILURE, $tester->getStatusCode());
    }

    public function testExecuteFailsWhenDownWouldZeroOrNegativeFactor(): void
    {
        $tester = new CommandTester($this->createCommand());
        $tester->execute(['percent' => '100', 'operation' => 'down']);

        $this->assertSame(Command::FAILURE, $tester->getStatusCode());
    }

    private function createCommand(): AdjustTaxPricesCommand
    {
        return new AdjustTaxPricesCommand(
            $this->createMock(CollectionFactory::class),
            $this->createMock(ProductResource::class)
        );
    }
}
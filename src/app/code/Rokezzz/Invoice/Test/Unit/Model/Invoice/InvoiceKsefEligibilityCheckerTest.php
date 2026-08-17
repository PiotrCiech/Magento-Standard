<?php

declare(strict_types=1);

namespace Rokezzz\Invoice\Test\Unit\Model\Invoice;

use Magento\Framework\App\Config\ScopeConfigInterface;
use Magento\Sales\Model\Order\Invoice;
use PHPUnit\Framework\TestCase;
use Rokezzz\Invoice\Model\Config;
use Rokezzz\Invoice\Model\Invoice\InvoiceKsefEligibilityChecker;
use Rokezzz\Invoice\Model\Invoice\KsefUpoStatus;
use Rokezzz\Invoice\Model\Invoice\LegalType;
use Rokezzz\Invoice\Model\Xml\LegalTypeResolver;

final class InvoiceKsefEligibilityCheckerTest extends TestCase
{
    public function testShouldQueueForKsefReturnsFalseWhenModuleDisabled(): void
    {
        $checker = $this->createChecker(moduleEnabled: false, ksefEnabled: true, startDate: '2026-01-01');

        $this->assertFalse($checker->shouldQueueForKsef($this->createInvoice()));
    }

    public function testShouldQueueForKsefReturnsFalseForProforma(): void
    {
        $checker = $this->createChecker(moduleEnabled: true, ksefEnabled: true, startDate: '2026-01-01');

        $this->assertFalse(
            $checker->shouldQueueForKsef($this->createInvoice(legalType: LegalType::Proforma->value))
        );
    }

    public function testShouldQueueForKsefReturnsFalseBeforeStartDate(): void
    {
        $checker = $this->createChecker(moduleEnabled: true, ksefEnabled: true, startDate: '2026-06-01');

        $this->assertFalse($checker->shouldQueueForKsef($this->createInvoice(createdAt: '2026-05-31 10:00:00')));
    }

    public function testShouldQueueForKsefReturnsTrueWhenAllRulesPass(): void
    {
        $checker = $this->createChecker(moduleEnabled: true, ksefEnabled: true, startDate: '2026-01-01');

        $this->assertTrue(
            $checker->shouldQueueForKsef($this->createInvoice(legalType: LegalType::Correction->value))
        );
    }

    public function testShouldSubmitToKsefRequiresTodoStatus(): void
    {
        $checker = $this->createChecker(moduleEnabled: true, ksefEnabled: true, startDate: '2026-01-01');
        $todo = $this->createInvoice(status: KsefUpoStatus::Todo->value);
        $done = $this->createInvoice(status: KsefUpoStatus::Done->value);

        $this->assertTrue($checker->shouldSubmitToKsef($todo));
        $this->assertFalse($checker->shouldSubmitToKsef($done));
    }

    private function createChecker(bool $moduleEnabled, bool $ksefEnabled, string $startDate): InvoiceKsefEligibilityChecker
    {
        $scopeConfig = $this->createStub(ScopeConfigInterface::class);
        $scopeConfig->method('isSetFlag')->willReturnCallback(
            static function (string $path) use ($moduleEnabled, $ksefEnabled): bool {
                return match ($path) {
                    'rokezzz_invoice/general/enabled' => $moduleEnabled,
                    'rokezzz_invoice/ksef/enabled' => $ksefEnabled,
                    default => false,
                };
            }
        );
        $scopeConfig->method('getValue')->willReturn($startDate);

        return new InvoiceKsefEligibilityChecker(new Config($scopeConfig), new LegalTypeResolver());
    }

    private function createInvoice(
        string $createdAt = '2026-02-01 08:00:00',
        string $status = 'todo',
        string $legalType = 'standard'
    ): Invoice {
        $invoice = $this->getMockBuilder(Invoice::class)
            ->disableOriginalConstructor()
            ->onlyMethods(['getStoreId', 'getCreatedAt', 'getData'])
            ->getMock();
        $invoice->method('getStoreId')->willReturn(1);
        $invoice->method('getCreatedAt')->willReturn($createdAt);
        $invoice->method('getData')->willReturnCallback(
            static function (string $key) use ($status, $legalType): string {
                return match ($key) {
                    'rokezzz_ksef_upo_status' => $status,
                    'rokezzz_invoice_legal_type' => $legalType,
                    default => '',
                };
            }
        );

        return $invoice;
    }
}

<?php

declare(strict_types=1);

namespace Rokezzz\Analytics\Test\Unit\Model;

use Magento\Framework\App\Config\ScopeConfigInterface;
use PHPUnit\Framework\MockObject\Stub;
use PHPUnit\Framework\TestCase;
use Rokezzz\Analytics\Model\Config;
use Rokezzz\Analytics\Model\Config\Source\TrackingType;

final class ConfigTest extends TestCase
{
    private ScopeConfigInterface&Stub $scopeConfig;

    protected function setUp(): void
    {
        $this->scopeConfig = $this->createStub(ScopeConfigInterface::class);
    }

    public function testGetTrackingIdRejectsInvalidCharacters(): void
    {
        $this->scopeConfig->method('getValue')->willReturn('G-ABC DEF');

        $this->assertSame('', (new Config($this->scopeConfig))->getTrackingId(1));
    }

    public function testIsGtagActiveRequiresSupportedTypeAndId(): void
    {
        $this->scopeConfig->method('getValue')->willReturnCallback(
            static function (string $path): string {
                return match ($path) {
                    'rokezzz_analytics/general/tracking_type' => TrackingType::TYPE_GA4,
                    'rokezzz_analytics/general/tracking_id' => 'G-TEST123',
                    default => '',
                };
            }
        );

        $this->assertTrue((new Config($this->scopeConfig))->isGtagActive(1));
        $this->assertFalse((new Config($this->scopeConfig))->isGtmActive(1));
    }

    public function testIsGtmActiveRequiresGtmType(): void
    {
        $this->scopeConfig->method('getValue')->willReturnCallback(
            static function (string $path): string {
                return match ($path) {
                    'rokezzz_analytics/general/tracking_type' => TrackingType::TYPE_GTM,
                    'rokezzz_analytics/general/tracking_id' => 'GTM-XXXX',
                    default => '',
                };
            }
        );

        $this->assertTrue((new Config($this->scopeConfig))->isGtmActive(1));
        $this->assertFalse((new Config($this->scopeConfig))->isGtagActive(1));
    }
}

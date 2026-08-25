<?php

declare(strict_types=1);

namespace Rokezzz\Review\Test\Unit\Model\Config;

use Magento\Framework\App\Config\ScopeConfigInterface;
use PHPUnit\Framework\MockObject\Stub;
use PHPUnit\Framework\TestCase;
use Rokezzz\Review\Model\Config\ReviewConfig;

final class ReviewConfigTest extends TestCase
{
    private ScopeConfigInterface&Stub $scopeConfig;

    protected function setUp(): void
    {
        $this->scopeConfig = $this->createStub(ScopeConfigInterface::class);
    }

    public function testGetCacheTtlHoursIsAtLeastOne(): void
    {
        $this->scopeConfig->method('getValue')->willReturn(0);

        $this->assertSame(1, (new ReviewConfig($this->scopeConfig))->getCacheTtlHours(1));
    }

    public function testIsConfiguredRequiresApiKeyAndPlaceId(): void
    {
        $this->scopeConfig->method('getValue')->willReturnCallback(
            static function (string $path): string {
                return match ($path) {
                    'rokezzz_review/general/google_api_key' => 'key',
                    'rokezzz_review/general/google_place_id' => 'place',
                    default => '',
                };
            }
        );

        $this->assertTrue((new ReviewConfig($this->scopeConfig))->isConfigured(1));
    }

    public function testIsConfiguredReturnsFalseWhenPlaceIdIsMissing(): void
    {
        $this->scopeConfig->method('getValue')->willReturnCallback(
            static function (string $path): string {
                return $path === 'rokezzz_review/general/google_api_key' ? 'key' : '';
            }
        );

        $this->assertFalse((new ReviewConfig($this->scopeConfig))->isConfigured(1));
    }
}

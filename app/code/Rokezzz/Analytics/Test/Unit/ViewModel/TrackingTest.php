<?php

declare(strict_types=1);

namespace Rokezzz\Analytics\Test\Unit\ViewModel;

use Magento\Framework\App\Config\ScopeConfigInterface;
use PHPUnit\Framework\TestCase;
use Rokezzz\Analytics\Model\Config;
use Rokezzz\Analytics\Model\Config\Source\TrackingType;
use Rokezzz\Analytics\ViewModel\Tracking;

final class TrackingTest extends TestCase
{
    public function testViewModelDelegatesToConfig(): void
    {
        $scopeConfig = $this->createStub(ScopeConfigInterface::class);
        $scopeConfig->method('getValue')->willReturnCallback(
            static function (string $path): string {
                return match ($path) {
                    'rokezzz_analytics/general/tracking_type' => TrackingType::TYPE_GA4,
                    'rokezzz_analytics/general/tracking_id' => 'G-TEST123',
                    default => '',
                };
            }
        );

        $tracking = new Tracking(new Config($scopeConfig));

        $this->assertTrue($tracking->isGtagActive());
        $this->assertFalse($tracking->isGtmActive());
        $this->assertSame('G-TEST123', $tracking->getTrackingId());
    }
}

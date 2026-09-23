<?php

declare(strict_types=1);

namespace Rokezzz\InPost\Test\Unit\Model;

use PHPUnit\Framework\TestCase;
use Rokezzz\InPost\Model\CarrierCodeMatcher;

final class CarrierCodeMatcherTest extends TestCase
{
    public function testIsInpostCarrierMatchesPrefixCaseInsensitively(): void
    {
        $matcher = new CarrierCodeMatcher();

        $this->assertTrue($matcher->isInpostCarrier('inpostlocker'));
        $this->assertTrue($matcher->isInpostCarrier('InPost_Courier'));
        $this->assertFalse($matcher->isInpostCarrier('flatrate'));
    }
}

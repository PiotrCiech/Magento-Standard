<?php

declare(strict_types=1);

namespace Rokezzz\Offers\Test\Unit\Model;

use Magento\Framework\Exception\LocalizedException;
use Magento\Framework\Stdlib\DateTime\DateTime;
use PHPUnit\Framework\MockObject\MockObject;
use PHPUnit\Framework\TestCase;
use Rokezzz\Offers\Api\Data\OfferInterface;
use Rokezzz\Offers\Model\OfferStatusValidator;
use Rokezzz\Offers\Test\Unit\Support\CreatesOfferEntities;

final class OfferStatusValidatorTest extends TestCase
{
    use CreatesOfferEntities;

    private DateTime&MockObject $dateTime;
    private OfferStatusValidator $offerStatusValidator;

    protected function setUp(): void
    {
        $this->dateTime = $this->createMock(DateTime::class);
        $this->offerStatusValidator = new OfferStatusValidator($this->dateTime);
    }

    public function testAssertCanTransitionOfferAllowsInReviewToOffered(): void
    {
        $this->offerStatusValidator->assertCanTransitionOffer(
            OfferInterface::STATUS_IN_REVIEW,
            OfferInterface::STATUS_OFFERED
        );

        $this->assertTrue(true);
    }

    public function testAssertCanTransitionOfferThrowsForOfferedToInReview(): void
    {
        $this->expectException(LocalizedException::class);

        $this->offerStatusValidator->assertCanTransitionOffer(
            OfferInterface::STATUS_OFFERED,
            OfferInterface::STATUS_IN_REVIEW
        );
    }

    public function testIsOfferExpiredReturnsFalseWhenExpirationDateMissing(): void
    {
        $offer = $this->createOfferModel(['expires_at' => null]);

        $this->assertFalse($this->offerStatusValidator->isOfferExpired($offer));
    }

    public function testIsOfferExpiredReturnsTrueWhenExpirationDatePassed(): void
    {
        $offer = $this->createOfferModel(['expires_at' => '2026-01-01 00:00:00']);
        $this->dateTime->method('gmtDate')->willReturn('2026-07-01 00:00:00');

        $this->assertTrue($this->offerStatusValidator->isOfferExpired($offer));
    }

    public function testAssertOfferIsUsableThrowsWhenOfferAlreadyUsed(): void
    {
        $offer = $this->createOfferModel([
            'status' => OfferInterface::STATUS_OFFERED,
            'used_at' => '2026-07-01 00:00:00',
            'expires_at' => '2026-12-31 23:59:59',
        ]);

        $this->expectException(LocalizedException::class);

        $this->offerStatusValidator->assertOfferIsUsable($offer);
    }

    public function testAssertOfferIsUsableSucceedsForActiveOfferedOffer(): void
    {
        $offer = $this->createOfferModel([
            'status' => OfferInterface::STATUS_OFFERED,
            'used_at' => null,
            'expires_at' => '2026-12-31 23:59:59',
        ]);
        $this->dateTime->method('gmtDate')->willReturn('2026-07-01 00:00:00');

        $this->offerStatusValidator->assertOfferIsUsable($offer);

        $this->assertTrue(true);
    }

    public function testAssertOfferIsUsableSucceedsWhenExpirationDateMissing(): void
    {
        $offer = $this->createOfferModel([
            'status' => OfferInterface::STATUS_OFFERED,
            'used_at' => null,
            'expires_at' => null,
        ]);

        $this->offerStatusValidator->assertOfferIsUsable($offer);

        $this->assertTrue(true);
    }
}

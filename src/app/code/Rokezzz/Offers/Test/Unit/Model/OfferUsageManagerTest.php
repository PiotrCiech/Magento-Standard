<?php

declare(strict_types=1);

namespace Rokezzz\Offers\Test\Unit\Model;

use PHPUnit\Framework\MockObject\MockObject;
use PHPUnit\Framework\TestCase;
use Rokezzz\Offers\Api\Data\OfferInterface;
use Rokezzz\Offers\Api\Data\OfferRequestInterface;
use Rokezzz\Offers\Model\OfferStatusValidator;
use Rokezzz\Offers\Model\OfferUsageManager;
use Rokezzz\Offers\Model\OfferCreator;
use Rokezzz\Offers\Model\OfferRequestCreator;
use Rokezzz\Offers\Model\ResourceModel\OfferRequestResource;
use Rokezzz\Offers\Model\ResourceModel\OfferResource;
use Rokezzz\Offers\Test\Unit\Support\CreatesOfferEntities;

final class OfferUsageManagerTest extends TestCase
{
    use CreatesOfferEntities;

    private OfferCreator&MockObject $offerCreator;
    private OfferRequestCreator&MockObject $offerRequestCreator;
    private OfferResource&MockObject $offerResource;
    private OfferRequestResource&MockObject $offerRequestResource;
    private OfferStatusValidator&MockObject $offerStatusValidator;
    private OfferUsageManager $offerUsageManager;

    protected function setUp(): void
    {
        $this->offerCreator = $this->createMock(OfferCreator::class);
        $this->offerRequestCreator = $this->createMock(OfferRequestCreator::class);
        $this->offerResource = $this->createMock(OfferResource::class);
        $this->offerRequestResource = $this->createMock(OfferRequestResource::class);
        $this->offerStatusValidator = $this->createMock(OfferStatusValidator::class);

        $this->offerUsageManager = new OfferUsageManager(
            $this->offerCreator,
            $this->offerRequestCreator,
            $this->offerResource,
            $this->offerRequestResource,
            $this->offerStatusValidator
        );
    }

    public function testMarkAsUsedSkipsWhenOfferIsNotOffered(): void
    {
        $offer = $this->createOfferModel([
            'entity_id' => 1,
            'status' => OfferInterface::STATUS_ACCEPTED,
        ]);

        $this->offerCreator->method('create')->willReturn($offer);
        $this->offerResource->method('load');
        $this->offerResource->expects($this->never())->method('save');

        $this->offerUsageManager->markAsUsed(1);
    }

    public function testMarkAsUsedUpdatesOfferAndRequest(): void
    {
        $offer = $this->createOfferModel([
            'entity_id' => 1,
            'request_id' => 9,
            'status' => OfferInterface::STATUS_OFFERED,
            'used_at' => null,
        ]);
        $offerRequest = $this->createOfferRequestModel([
            'entity_id' => 9,
            'status' => OfferRequestInterface::STATUS_OFFERED,
        ]);

        $this->offerCreator->method('create')->willReturn($offer);
        $this->offerRequestCreator->method('create')->willReturn($offerRequest);
        $this->offerResource->expects($this->once())->method('save')->with($offer);
        $this->offerRequestResource->expects($this->once())->method('save')->with($offerRequest);
        $this->offerStatusValidator->expects($this->once())->method('assertCanTransitionOffer');
        $this->offerStatusValidator->expects($this->once())->method('assertCanTransitionRequest');
        $this->offerResource->method('load');
        $this->offerRequestResource->method('load');

        $this->offerUsageManager->markAsUsed(1);

        $this->assertSame(OfferInterface::STATUS_ACCEPTED, $offer->getStatus());
        $this->assertSame(OfferRequestInterface::STATUS_ACCEPTED, $offerRequest->getStatus());
        $this->assertNotNull($offer->getUsedAt());
    }
}

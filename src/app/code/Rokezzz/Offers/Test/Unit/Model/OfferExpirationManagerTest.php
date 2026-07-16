<?php

declare(strict_types=1);

namespace Rokezzz\Offers\Test\Unit\Model;

use Magento\Framework\Stdlib\DateTime\DateTime;
use PHPUnit\Framework\MockObject\MockObject;
use PHPUnit\Framework\TestCase;
use Rokezzz\Offers\Api\Data\OfferInterface;
use Rokezzz\Offers\Api\Data\OfferRequestInterface;
use Rokezzz\Offers\Model\OfferCollectionCreator;
use Rokezzz\Offers\Model\OfferExpirationManager;
use Rokezzz\Offers\Model\OfferRequestCreator;
use Rokezzz\Offers\Model\OfferStatusValidator;
use Rokezzz\Offers\Model\ResourceModel\Offer\Collection;
use Rokezzz\Offers\Model\ResourceModel\OfferRequestResource;
use Rokezzz\Offers\Model\ResourceModel\OfferResource;
use Rokezzz\Offers\Test\Unit\Support\CreatesOfferEntities;

final class OfferExpirationManagerTest extends TestCase
{
    use CreatesOfferEntities;

    private OfferCollectionCreator&MockObject $offerCollectionCreator;
    private OfferRequestCreator&MockObject $offerRequestCreator;
    private OfferResource&MockObject $offerResource;
    private OfferRequestResource&MockObject $offerRequestResource;
    private OfferStatusValidator&MockObject $offerStatusValidator;
    private DateTime&MockObject $dateTime;
    private OfferExpirationManager $offerExpirationManager;

    protected function setUp(): void
    {
        $this->offerCollectionCreator = $this->createMock(OfferCollectionCreator::class);
        $this->offerRequestCreator = $this->createMock(OfferRequestCreator::class);
        $this->offerResource = $this->createMock(OfferResource::class);
        $this->offerRequestResource = $this->createMock(OfferRequestResource::class);
        $this->offerStatusValidator = $this->createMock(OfferStatusValidator::class);
        $this->dateTime = $this->createMock(DateTime::class);

        $this->offerExpirationManager = new OfferExpirationManager(
            $this->offerCollectionCreator,
            $this->offerRequestCreator,
            $this->offerResource,
            $this->offerRequestResource,
            $this->offerStatusValidator,
            $this->dateTime
        );
    }

    public function testExpireDueOffersSkipsOffersWithoutExpirationDate(): void
    {
        $collection = $this->createMock(Collection::class);
        $collection->method('getIterator')->willReturn(new \ArrayIterator([]));

        $this->dateTime->method('gmtDate')->willReturn('2026-07-01 00:00:00');
        $this->offerCollectionCreator->method('create')->willReturn($collection);
        $collection->expects($this->exactly(4))->method('addFieldToFilter');

        $this->assertSame(0, $this->offerExpirationManager->expireDueOffers());
    }

    public function testExpireDueOffersMarksOfferAndRequestAsExpired(): void
    {
        $offer = $this->createOfferModel([
            'entity_id' => 3,
            'request_id' => 8,
            'status' => OfferInterface::STATUS_OFFERED,
            'expires_at' => '2026-01-01 00:00:00',
        ]);
        $offerRequest = $this->createOfferRequestModel([
            'entity_id' => 8,
            'status' => OfferRequestInterface::STATUS_OFFERED,
        ]);

        $collection = $this->createMock(Collection::class);
        $collection->method('getIterator')->willReturn(new \ArrayIterator([$offer]));

        $this->dateTime->method('gmtDate')->willReturn('2026-07-01 00:00:00');
        $this->offerCollectionCreator->method('create')->willReturn($collection);
        $this->offerStatusValidator->method('isOfferExpired')->willReturn(true);
        $this->offerRequestCreator->method('create')->willReturn($offerRequest);
        $this->offerRequestResource->method('load');
        $this->offerResource->expects($this->once())->method('save')->with($offer);
        $this->offerRequestResource->expects($this->once())->method('save')->with($offerRequest);

        $expiredCount = $this->offerExpirationManager->expireDueOffers();

        $this->assertSame(1, $expiredCount);
        $this->assertSame(OfferInterface::STATUS_EXPIRED, $offer->getStatus());
        $this->assertSame(OfferRequestInterface::STATUS_EXPIRED, $offerRequest->getStatus());
    }
}

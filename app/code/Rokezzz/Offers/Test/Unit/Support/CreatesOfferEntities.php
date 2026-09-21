<?php

declare(strict_types=1);

namespace Rokezzz\Offers\Test\Unit\Support;

use Rokezzz\Offers\Model\Offer;
use Rokezzz\Offers\Model\OfferItem;
use Rokezzz\Offers\Model\OfferRequest;

/**
 * @mixin \PHPUnit\Framework\TestCase
 */
trait CreatesOfferEntities
{
    /**
     * @param array<string, mixed> $data
     */
    private function createOfferModel(array $data = []): Offer
    {
        $offer = $this->getMockBuilder(Offer::class)
            ->disableOriginalConstructor()
            ->onlyMethods([])
            ->getMock();

        if ($data !== []) {
            $offer->setData($data);
        }

        return $offer;
    }

    /**
     * @param array<string, mixed> $data
     */
    private function createOfferRequestModel(array $data = []): OfferRequest
    {
        $offerRequest = $this->getMockBuilder(OfferRequest::class)
            ->disableOriginalConstructor()
            ->onlyMethods([])
            ->getMock();

        if ($data !== []) {
            $offerRequest->setData($data);
        }

        return $offerRequest;
    }

    /**
     * @param array<string, mixed> $data
     */
    private function createOfferItemModel(array $data = []): OfferItem
    {
        $offerItem = $this->getMockBuilder(OfferItem::class)
            ->disableOriginalConstructor()
            ->onlyMethods([])
            ->getMock();

        if ($data !== []) {
            $offerItem->setData($data);
        }

        return $offerItem;
    }
}

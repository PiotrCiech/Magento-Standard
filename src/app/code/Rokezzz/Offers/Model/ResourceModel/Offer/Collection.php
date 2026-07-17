<?php

declare(strict_types=1);

namespace Rokezzz\Offers\Model\ResourceModel\Offer;

use Magento\Framework\Model\ResourceModel\Db\Collection\AbstractCollection;
use Rokezzz\Offers\Model\Offer as OfferModel;
use Rokezzz\Offers\Model\ResourceModel\OfferResource;

class Collection extends AbstractCollection
{
    protected function _construct(): void
    {
        $this->_init(OfferModel::class, OfferResource::class);
    }
}

<?php

declare(strict_types=1);

namespace Rokezzz\Offers\Model\ResourceModel\OfferRequest;

use Magento\Framework\Model\ResourceModel\Db\Collection\AbstractCollection;
use Rokezzz\Offers\Model\OfferRequest as OfferRequestModel;
use Rokezzz\Offers\Model\ResourceModel\OfferRequestResource;

class Collection extends AbstractCollection
{
    protected function _construct(): void
    {
        $this->_init(OfferRequestModel::class, OfferRequestResource::class);
    }
}

<?php

declare(strict_types=1);

namespace Rokezzz\Offers\Model\ResourceModel\OfferRequestItem;

use Magento\Framework\Model\ResourceModel\Db\Collection\AbstractCollection;
use Rokezzz\Offers\Model\OfferRequestItem as OfferRequestItemModel;
use Rokezzz\Offers\Model\ResourceModel\OfferRequestItemResource;

class Collection extends AbstractCollection
{
    protected function _construct(): void
    {
        $this->_init(OfferRequestItemModel::class, OfferRequestItemResource::class);
    }
}

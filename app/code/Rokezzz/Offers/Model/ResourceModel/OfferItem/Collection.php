<?php

declare(strict_types=1);

namespace Rokezzz\Offers\Model\ResourceModel\OfferItem;

use Magento\Framework\Model\ResourceModel\Db\Collection\AbstractCollection;
use Rokezzz\Offers\Model\OfferItem as OfferItemModel;
use Rokezzz\Offers\Model\ResourceModel\OfferItemResource;

class Collection extends AbstractCollection
{
    protected function _construct(): void
    {
        $this->_init(OfferItemModel::class, OfferItemResource::class);
    }
}

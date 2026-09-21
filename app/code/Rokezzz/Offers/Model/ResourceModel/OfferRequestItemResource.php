<?php

declare(strict_types=1);

namespace Rokezzz\Offers\Model\ResourceModel;

use Magento\Framework\Model\ResourceModel\Db\AbstractDb;

class OfferRequestItemResource extends AbstractDb
{
    protected function _construct(): void
    {
        $this->_init('rokezzz_offer_request_item', 'entity_id');
    }
}

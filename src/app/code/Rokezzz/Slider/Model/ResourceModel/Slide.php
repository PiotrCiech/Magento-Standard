<?php

declare(strict_types=1);

namespace Rokezzz\PromotionSlider\Model\ResourceModel;

use Magento\Framework\Model\ResourceModel\Db\AbstractDb;
use Rokezzz\PromotionSlider\Api\Data\SlideInterface;

class Slide extends AbstractDb
{
    protected function _construct(): void
    {
        $this->_init(SlideInterface::TABLE_NAME, SlideInterface::SLIDE_ID);
    }
}

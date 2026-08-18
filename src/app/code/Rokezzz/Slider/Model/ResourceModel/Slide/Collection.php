<?php

declare(strict_types=1);

namespace Rokezzz\Slider\Model\ResourceModel\Slide;

use Magento\Framework\Model\ResourceModel\Db\Collection\AbstractCollection;
use Rokezzz\Slider\Model\Slide;
use Rokezzz\Slider\Model\ResourceModel\Slide as SlideResource;

class Collection extends AbstractCollection
{
    protected $_idFieldName = 'slide_id';

    protected function _construct(): void
    {
        $this->_init(Slide::class, SlideResource::class);
    }
}

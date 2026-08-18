<?php

declare(strict_types=1);

namespace Rokezzz\Offers\Block\Adminhtml\Offer\Edit;

use Magento\Backend\Block\Widget\Context;
use Magento\Framework\View\Element\UiComponent\Control\ButtonProviderInterface;

class BackButton extends GenericButton implements ButtonProviderInterface
{
    public function __construct(Context $context)
    {
        parent::__construct($context);
    }

    public function getButtonData(): array
    {
        return [
            'label' => __('Back'),
            'on_click' => sprintf("location.href = '%s';", $this->getUrl('rokezzz_offers/offer/index')),
            'class' => 'back',
            'sort_order' => 10,
        ];
    }
}

<?php

declare(strict_types=1);

namespace Rokezzz\Offers\Block\Adminhtml\Offer\Edit;

use Magento\Backend\Block\Widget\Context;
use Magento\Framework\View\Element\UiComponent\Control\ButtonProviderInterface;

class SaveButton extends GenericButton implements ButtonProviderInterface
{
    public function __construct(Context $context)
    {
        parent::__construct($context);
    }

    public function getButtonData(): array
    {
        return [
            'label' => __('Save Offer'),
            'class' => 'save primary',
            'data_attribute' => [
                'mage-init' => [
                    'buttonAdapter' => [
                        'actions' => [
                            [
                                'targetName' => 'rokezzz_offers_offer_form.rokezzz_offers_offer_form',
                                'actionName' => 'save',
                                'params' => [
                                    false,
                                    [
                                        'back' => 'edit',
                                    ],
                                ],
                            ],
                        ],
                    ],
                ],
            ],
            'sort_order' => 20,
        ];
    }
}

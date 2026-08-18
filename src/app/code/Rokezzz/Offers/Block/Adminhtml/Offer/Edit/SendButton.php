<?php

declare(strict_types=1);

namespace Rokezzz\Offers\Block\Adminhtml\Offer\Edit;

use Magento\Backend\Block\Widget\Context;
use Magento\Framework\View\Element\UiComponent\Control\ButtonProviderInterface;

class SendButton extends GenericButton implements ButtonProviderInterface
{
    public function __construct(Context $context)
    {
        parent::__construct($context);
    }

    public function getButtonData(): array
    {
        $offerId = $this->getOfferId();

        if ($offerId <= 0) {
            return [];
        }

        return [
            'label' => __('Send Offer'),
            'on_click' => $this->getConfirmPostOnClick(
                (string) __('Are you sure you want to send this offer to the customer?'),
                $this->getUrl('rokezzz_offers/offer/send', ['entity_id' => $offerId])
            ),
            'class' => 'save',
            'sort_order' => 30,
        ];
    }
}

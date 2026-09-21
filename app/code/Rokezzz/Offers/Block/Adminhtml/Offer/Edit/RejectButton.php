<?php

declare(strict_types=1);

namespace Rokezzz\Offers\Block\Adminhtml\Offer\Edit;

use Magento\Backend\Block\Widget\Context;
use Magento\Framework\View\Element\UiComponent\Control\ButtonProviderInterface;

class RejectButton extends GenericButton implements ButtonProviderInterface
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
            'label' => __('Reject Offer'),
            'on_click' => $this->getConfirmPostOnClick(
                (string) __('Are you sure you want to reject this offer?'),
                $this->getUrl('rokezzz_offers/offer/reject', ['entity_id' => $offerId])
            ),
            'class' => 'delete',
            'sort_order' => 40,
        ];
    }
}

<?php

declare(strict_types=1);

namespace Rokezzz\Offers\Controller\Adminhtml\Offer;

use Magento\Backend\App\Action\Context;
use Magento\Backend\Model\View\Result\Page;
use Magento\Framework\App\Action\HttpGetActionInterface;
use Magento\Framework\View\Result\PageFactory;
use Rokezzz\Offers\Controller\Adminhtml\Offer as OfferAction;

class Index extends OfferAction implements HttpGetActionInterface
{
    public function __construct(
        Context $context,
        private readonly PageFactory $resultPageFactory
    ) {
        parent::__construct($context);
    }

    public function execute()
    {
        /** @var Page $resultPage */
        $resultPage = $this->resultPageFactory->create();
        $resultPage->setActiveMenu('Rokezzz_Offers::offer');
        $resultPage->getConfig()->getTitle()->prepend(__('Offers'));

        return $resultPage;
    }
}

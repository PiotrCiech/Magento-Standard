<?php

declare(strict_types=1);

namespace Rokezzz\PromotionSlider\Controller\Adminhtml\Slide;

use Magento\Backend\App\Action;
use Magento\Framework\App\Action\HttpGetActionInterface;
use Magento\Framework\View\Result\PageFactory;
use Rokezzz\PromotionSlider\Model\SlideFactory;

class Edit extends Action implements HttpGetActionInterface
{
    const ADMIN_RESOURCE = 'Rokezzz_PromotionSlider::slide';

    private PageFactory $resultPageFactory;
    private SlideFactory $slideFactory;

    public function __construct(
        Action\Context $context,
        PageFactory $resultPageFactory,
        SlideFactory $slideFactory
    ) {
        parent::__construct($context);
        $this->resultPageFactory = $resultPageFactory;
        $this->slideFactory = $slideFactory;
    }

    public function execute()
    {
        $id = (int)$this->getRequest()->getParam('slide_id');
        $slide = $this->slideFactory->create();

        if ($id) {
            $slide->getResource()->load($slide, $id);
            if (!$slide->getId()) {
                $this->messageManager->addErrorMessage(__('This slide no longer exists.'));

                return $this->resultRedirectFactory->create()->setPath('*/*/');
            }
        }

        $resultPage = $this->resultPageFactory->create();
        $resultPage->setActiveMenu('Rokezzz_PromotionSlider::slide');
        $resultPage->getConfig()->getTitle()->prepend(
            $slide->getId() ? __('Edit Slide: %1', $slide->getTitle()) : __('New Slide')
        );

        return $resultPage;
    }
}

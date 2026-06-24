<?php

declare(strict_types=1);

namespace Rokezzz\PromotionSlider\Controller\Adminhtml\Slide;

use Magento\Backend\App\Action;
use Magento\Framework\App\Action\HttpPostActionInterface;
use Rokezzz\PromotionSlider\Model\SlideFactory;
use Rokezzz\PromotionSlider\Model\ResourceModel\Slide as SlideResource;

class Delete extends Action implements HttpPostActionInterface
{
    const ADMIN_RESOURCE = 'Rokezzz_PromotionSlider::slide';

    private SlideFactory $slideFactory;
    private SlideResource $slideResource;

    public function __construct(
        Action\Context $context,
        SlideFactory $slideFactory,
        SlideResource $slideResource
    ) {
        parent::__construct($context);
        $this->slideFactory = $slideFactory;
        $this->slideResource = $slideResource;
    }

    public function execute()
    {
        $id = (int)$this->getRequest()->getParam('slide_id');
        $resultRedirect = $this->resultRedirectFactory->create();

        if (!$id) {
            $this->messageManager->addErrorMessage(__('We can\'t find a slide to delete.'));

            return $resultRedirect->setPath('*/*/');
        }

        try {
            $slide = $this->slideFactory->create();
            $this->slideResource->load($slide, $id);
            $this->slideResource->delete($slide);
            $this->messageManager->addSuccessMessage(__('The slide has been deleted.'));
        } catch (\Exception $e) {
            $this->messageManager->addErrorMessage($e->getMessage());
        }

        return $resultRedirect->setPath('*/*/');
    }
}

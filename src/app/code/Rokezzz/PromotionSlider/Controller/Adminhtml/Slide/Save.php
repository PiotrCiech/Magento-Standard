<?php

declare(strict_types=1);

namespace Rokezzz\PromotionSlider\Controller\Adminhtml\Slide;

use Magento\Backend\App\Action;
use Magento\Backend\App\Action\Context;
use Magento\Framework\App\Action\HttpPostActionInterface;
use Rokezzz\PromotionSlider\Model\SlideFactory;
use Rokezzz\PromotionSlider\Model\ResourceModel\Slide as SlideResource;

class Save extends Action implements HttpPostActionInterface
{
    const ADMIN_RESOURCE = 'Rokezzz_PromotionSlider::slide';

    private SlideFactory $slideFactory;
    private SlideResource $slideResource;

    public function __construct(
        Context $context,
        SlideFactory $slideFactory,
        SlideResource $slideResource
    ) {
        parent::__construct($context);
        $this->slideFactory = $slideFactory;
        $this->slideResource = $slideResource;
    }

    public function execute()
    {
        $postData = $this->getRequest()->getPostValue();
        $resultRedirect = $this->resultRedirectFactory->create();

        if (!$postData) {
            return $resultRedirect->setPath('*/*/');
        }

        $data = $postData;
        unset($data['form_key'], $data['key']);

        $id = (int)($data['slide_id'] ?? 0);
        $slide = $this->slideFactory->create();

        if ($id) {
            $this->slideResource->load($slide, $id);
            if (!$slide->getId()) {
                $this->messageManager->addErrorMessage(__('This slide no longer exists.'));

                return $resultRedirect->setPath('*/*/');
            }
        }

        if (isset($data['image']) && is_array($data['image'])) {
            if (!empty($data['image'][0]['file'])) {
                $data['image'] = $data['image'][0]['file'];
            } elseif (!empty($data['image'][0]['name'])) {
                $data['image'] = $data['image'][0]['name'];
            } else {
                $data['image'] = null;
            }
        }

        if (empty($data['slide_id'])) {
            unset($data['slide_id']);
        }

        if ($id) {
            $slide->addData($data);
        } else {
            $slide->setData($data);
        }

        try {
            $this->slideResource->save($slide);
            $this->messageManager->addSuccessMessage(__('The slide has been saved.'));

            if ($this->getRequest()->getParam('back') === 'edit') {
                return $resultRedirect->setPath('*/*/edit', ['slide_id' => $slide->getId()]);
            }

            return $resultRedirect->setPath('*/*/');
        } catch (\Exception $e) {
            $this->messageManager->addErrorMessage($e->getMessage());

            return $resultRedirect->setPath('*/*/edit', ['slide_id' => $id]);
        }
    }
}

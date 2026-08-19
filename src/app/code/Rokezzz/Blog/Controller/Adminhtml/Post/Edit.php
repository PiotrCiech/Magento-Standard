<?php

declare(strict_types=1);

namespace Rokezzz\Blog\Controller\Adminhtml\Post;

use Magento\Backend\App\Action;
use Magento\Backend\App\Action\Context;
use Magento\Backend\Model\View\Result\Page;
use Magento\Framework\View\Result\PageFactory;
use Magento\Framework\Controller\ResultInterface;
use Rokezzz\Blog\Model\PostFactory;

class Edit extends Action
{
    public const ADMIN_RESOURCE = 'Rokezzz_Blog::posts';

    private PageFactory $resultPageFactory;
    private PostFactory $postFactory;

    public function __construct(
        Context $context,
        PageFactory $resultPageFactory,
        PostFactory $postFactory
    ) {
        parent::__construct($context);
        $this->resultPageFactory = $resultPageFactory;
        $this->postFactory = $postFactory;
    }

    public function execute(): ResultInterface
    {
        $id = $this->getRequest()->getParam('post_id');
        $model = $this->postFactory->create();

        if ($id) {
            $model->load($id);
            if (!$model->getId()) {
                $this->messageManager->addErrorMessage(__('This post no longer exists.'));
                $resultRedirect = $this->resultRedirectFactory->create();
                return $resultRedirect->setPath('*/*/');
            }
        }

        /** @var Page $resultPage */
        $resultPage = $this->resultPageFactory->create();
        $resultPage->setActiveMenu('Rokezzz_Blog::posts');
        $resultPage->getConfig()->getTitle()->prepend(
            $model->getId() ? __('Edit Post "%1"', $model->getTitle()) : __('New Post')
        );

        return $resultPage;
    }
}
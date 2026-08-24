<?php

declare(strict_types=1);

namespace Rokezzz\Blog\Controller\Adminhtml\Category;

use Magento\Backend\App\Action;
use Magento\Backend\App\Action\Context;
use Magento\Framework\App\Action\HttpGetActionInterface;
use Magento\Framework\View\Result\PageFactory;
use Rokezzz\Blog\Model\CategoryCreator;

class Edit extends Action implements HttpGetActionInterface
{
    public const ADMIN_RESOURCE = 'Rokezzz_Blog::categories';

    public function __construct(
        Context $context,
        private readonly PageFactory $resultPageFactory,
        private readonly CategoryCreator $categoryCreator
    ) {
        parent::__construct($context);
    }

    public function execute()
    {
        $categoryId = (int) $this->getRequest()->getParam('category_id');
        $category = $this->categoryCreator->create();

        if ($categoryId > 0) {
            $category->getResource()->load($category, $categoryId);
            if (!$category->getId()) {
                $this->messageManager->addErrorMessage(__('This category no longer exists.'));

                return $this->resultRedirectFactory->create()->setPath('*/*/');
            }
        }

        /** @var \Magento\Backend\Model\View\Result\Page $resultPage */
        $resultPage = $this->resultPageFactory->create();
        $resultPage->setActiveMenu('Rokezzz_Blog::categories');
        $resultPage->getConfig()->getTitle()->prepend(
            $category->getId() ? __('Edit Category: %1', $category->getName()) : __('New Category')
        );

        return $resultPage;
    }
}

<?php

declare(strict_types=1);

namespace Rokezzz\Blog\Controller\Adminhtml\Category;

use Exception;
use Magento\Backend\App\Action;
use Magento\Backend\App\Action\Context;
use Magento\Framework\App\Action\HttpPostActionInterface;
use Rokezzz\Blog\Model\CategoryCreator;
use Rokezzz\Blog\Model\ResourceModel\CategoryResource;

class Delete extends Action implements HttpPostActionInterface
{
    public const ADMIN_RESOURCE = 'Rokezzz_Blog::categories';

    public function __construct(
        Context $context,
        private readonly CategoryCreator $categoryCreator,
        private readonly CategoryResource $categoryResource
    ) {
        parent::__construct($context);
    }

    public function execute()
    {
        $categoryId = (int) $this->getRequest()->getParam('category_id');
        $resultRedirect = $this->resultRedirectFactory->create();

        if ($categoryId <= 0) {
            $this->messageManager->addErrorMessage(__('We can\'t find a category to delete.'));

            return $resultRedirect->setPath('*/*/');
        }

        try {
            $category = $this->categoryCreator->create();
            $this->categoryResource->load($category, $categoryId);
            $this->categoryResource->delete($category);
            $this->messageManager->addSuccessMessage(__('The category has been deleted.'));
        } catch (Exception $exception) {
            $this->messageManager->addErrorMessage($exception->getMessage());
        }

        return $resultRedirect->setPath('*/*/');
    }
}

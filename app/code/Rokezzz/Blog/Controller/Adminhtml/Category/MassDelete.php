<?php

declare(strict_types=1);

namespace Rokezzz\Blog\Controller\Adminhtml\Category;

use Magento\Backend\App\Action;
use Magento\Backend\App\Action\Context;
use Magento\Framework\App\Action\HttpPostActionInterface;
use Magento\Framework\Controller\Result\Redirect;
use Magento\Framework\Controller\ResultFactory;
use Magento\Ui\Component\MassAction\Filter;
use Rokezzz\Blog\Model\ResourceModel\Category\CollectionCreator;
use Rokezzz\Blog\Model\ResourceModel\CategoryResource;

class MassDelete extends Action implements HttpPostActionInterface
{
    public const ADMIN_RESOURCE = 'Rokezzz_Blog::categories';

    public function __construct(
        Context $context,
        private readonly Filter $filter,
        private readonly CollectionCreator $collectionCreator,
        private readonly CategoryResource $categoryResource
    ) {
        parent::__construct($context);
    }

    public function execute(): Redirect
    {
        $collection = $this->filter->getCollection($this->collectionCreator->create());
        $count = 0;

        foreach ($collection as $category) {
            $this->categoryResource->delete($category);
            $count++;
        }

        $this->messageManager->addSuccessMessage(__('A total of %1 category(s) have been deleted.', $count));

        /** @var Redirect $resultRedirect */
        $resultRedirect = $this->resultFactory->create(ResultFactory::TYPE_REDIRECT);

        return $resultRedirect->setPath('*/*/');
    }
}

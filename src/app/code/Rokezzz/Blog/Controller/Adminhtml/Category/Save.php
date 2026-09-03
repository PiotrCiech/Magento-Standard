<?php

declare(strict_types=1);

namespace Rokezzz\Blog\Controller\Adminhtml\Category;

use Exception;
use Magento\Backend\App\Action;
use Magento\Backend\App\Action\Context;
use Magento\Framework\App\Action\HttpPostActionInterface;
use Magento\Framework\App\Request\DataPersistorInterface;
use Magento\Framework\App\Request\Http as HttpRequest;
use Magento\Framework\Controller\Result\Redirect;
use Rokezzz\Blog\Model\CategoryCreator;
use Rokezzz\Blog\Model\ResourceModel\CategoryResource;

class Save extends Action implements HttpPostActionInterface
{
    public const ADMIN_RESOURCE = 'Rokezzz_Blog::categories';

    public function __construct(
        Context $context,
        private readonly CategoryCreator $categoryCreator,
        private readonly CategoryResource $categoryResource,
        private readonly DataPersistorInterface $dataPersistor
    ) {
        parent::__construct($context);
    }

    public function execute(): Redirect
    {
        $resultRedirect = $this->resultRedirectFactory->create();
        $request = $this->getRequest();

        if (!$request instanceof HttpRequest) {
            return $resultRedirect->setPath('*/*/');
        }

        $data = $request->getPostValue();
        if (!is_array($data) || $data === []) {
            return $resultRedirect->setPath('*/*/');
        }

        unset($data['form_key']);

        $categoryId = (int) ($data['category_id'] ?? 0);
        $category = $this->categoryCreator->create();

        if ($categoryId > 0) {
            $this->categoryResource->load($category, $categoryId);
            if (!$category->getId()) {
                $this->messageManager->addErrorMessage(__('This category no longer exists.'));

                return $resultRedirect->setPath('*/*/');
            }
        }

        if (empty($data['category_id'])) {
            unset($data['category_id']);
        }

        $category->addData($data);

        try {
            $this->categoryResource->save($category);
            $this->dataPersistor->clear('rokezzz_blog_category');
            $this->messageManager->addSuccessMessage(__('The category has been saved.'));

            if ((string) $request->getParam('back') === 'edit') {
                return $resultRedirect->setPath('*/*/edit', ['category_id' => $category->getId()]);
            }

            return $resultRedirect->setPath('*/*/');
        } catch (Exception $exception) {
            $this->dataPersistor->set('rokezzz_blog_category', $data);
            $this->messageManager->addErrorMessage($exception->getMessage());

            return $resultRedirect->setPath('*/*/edit', ['category_id' => $categoryId]);
        }
    }
}

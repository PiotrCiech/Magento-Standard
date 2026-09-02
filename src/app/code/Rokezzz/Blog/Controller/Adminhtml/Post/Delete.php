<?php

declare(strict_types=1);

namespace Rokezzz\Blog\Controller\Adminhtml\Post;

use Exception;
use Magento\Backend\App\Action;
use Magento\Backend\App\Action\Context;
use Magento\Framework\Controller\ResultInterface;
use Rokezzz\Blog\Model\PostCreator;
use Rokezzz\Blog\Model\ResourceModel\PostResource;

class Delete extends Action
{
    public const ADMIN_RESOURCE = 'Rokezzz_Blog::posts';

    public function __construct(
        Context $context,
        private readonly PostCreator $postCreator,
        private readonly PostResource $postResource
    ) {
        parent::__construct($context);
    }

    public function execute(): ResultInterface
    {
        $resultRedirect = $this->resultRedirectFactory->create();
        $id = $this->getRequest()->getParam('post_id');

        if ($id) {
            try {
                $model = $this->postCreator->create();
                $this->postResource->load($model, $id);
                $this->postResource->delete($model);
                $this->messageManager->addSuccessMessage(__('You deleted the post.'));

                return $resultRedirect->setPath('*/*/');
            } catch (Exception $e) {
                $this->messageManager->addErrorMessage($e->getMessage());

                return $resultRedirect->setPath('*/*/edit', ['post_id' => $id]);
            }
        }

        $this->messageManager->addErrorMessage(__('We can\'t find a post to delete.'));

        return $resultRedirect->setPath('*/*/');
    }
}

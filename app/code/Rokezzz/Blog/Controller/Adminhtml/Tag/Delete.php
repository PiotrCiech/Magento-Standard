<?php

declare(strict_types=1);

namespace Rokezzz\Blog\Controller\Adminhtml\Tag;

use Exception;
use Magento\Backend\App\Action;
use Magento\Backend\App\Action\Context;
use Magento\Framework\App\Action\HttpPostActionInterface;
use Rokezzz\Blog\Model\ResourceModel\Tag as TagResource;
use Rokezzz\Blog\Model\TagCreator;

class Delete extends Action implements HttpPostActionInterface
{
    public const ADMIN_RESOURCE = 'Rokezzz_Blog::tags';

    public function __construct(
        Context $context,
        private readonly TagCreator $tagCreator,
        private readonly TagResource $tagResource
    ) {
        parent::__construct($context);
    }

    public function execute()
    {
        $tagId = (int) $this->getRequest()->getParam('tag_id');
        $resultRedirect = $this->resultRedirectFactory->create();

        if ($tagId <= 0) {
            $this->messageManager->addErrorMessage(__('We can\'t find a tag to delete.'));

            return $resultRedirect->setPath('*/*/');
        }

        try {
            $tag = $this->tagCreator->create();
            $this->tagResource->load($tag, $tagId);
            $this->tagResource->delete($tag);
            $this->messageManager->addSuccessMessage(__('The tag has been deleted.'));
        } catch (Exception $exception) {
            $this->messageManager->addErrorMessage($exception->getMessage());
        }

        return $resultRedirect->setPath('*/*/');
    }
}

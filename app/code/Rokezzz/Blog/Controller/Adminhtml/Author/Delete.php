<?php

declare(strict_types=1);

namespace Rokezzz\Blog\Controller\Adminhtml\Author;

use Exception;
use Magento\Backend\App\Action;
use Magento\Backend\App\Action\Context;
use Magento\Framework\App\Action\HttpPostActionInterface;
use Rokezzz\Blog\Model\AuthorCreator;
use Rokezzz\Blog\Model\ResourceModel\Author as AuthorResource;

class Delete extends Action implements HttpPostActionInterface
{
    public const ADMIN_RESOURCE = 'Rokezzz_Blog::authors';

    public function __construct(
        Context $context,
        private readonly AuthorCreator $authorCreator,
        private readonly AuthorResource $authorResource
    ) {
        parent::__construct($context);
    }

    public function execute()
    {
        $authorId = (int) $this->getRequest()->getParam('author_id');
        $resultRedirect = $this->resultRedirectFactory->create();

        if ($authorId <= 0) {
            $this->messageManager->addErrorMessage(__('We can\'t find an author to delete.'));

            return $resultRedirect->setPath('*/*/');
        }

        try {
            $author = $this->authorCreator->create();
            $this->authorResource->load($author, $authorId);
            $this->authorResource->delete($author);
            $this->messageManager->addSuccessMessage(__('The author has been deleted.'));
        } catch (Exception $exception) {
            $this->messageManager->addErrorMessage($exception->getMessage());
        }

        return $resultRedirect->setPath('*/*/');
    }
}

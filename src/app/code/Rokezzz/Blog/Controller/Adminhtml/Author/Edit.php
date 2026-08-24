<?php

declare(strict_types=1);

namespace Rokezzz\Blog\Controller\Adminhtml\Author;

use Magento\Backend\App\Action;
use Magento\Backend\Model\View\Result\Page;
use Magento\Framework\App\Action\HttpGetActionInterface;
use Magento\Framework\View\Result\PageFactory;
use Rokezzz\Blog\Model\AuthorCreator;

class Edit extends Action implements HttpGetActionInterface
{
    public const ADMIN_RESOURCE = 'Rokezzz_Blog::authors';

    public function __construct(
        Action\Context $context,
        private readonly PageFactory $resultPageFactory,
        private readonly AuthorCreator $authorCreator
    ) {
        parent::__construct($context);
    }

    public function execute()
    {
        $authorId = (int) $this->getRequest()->getParam('author_id');
        $author = $this->authorCreator->create();

        if ($authorId > 0) {
            $author->getResource()->load($author, $authorId);
            if (!$author->getId()) {
                $this->messageManager->addErrorMessage(__('This author no longer exists.'));

                return $this->resultRedirectFactory->create()->setPath('*/*/');
            }
        }

        $authorName = trim($author->getFirstname() . ' ' . $author->getLastname());

        /** @var Page $resultPage */
        $resultPage = $this->resultPageFactory->create();
        $resultPage->setActiveMenu('Rokezzz_Blog::authors');
        $resultPage->getConfig()->getTitle()->prepend(
            $author->getId() ? __('Edit Author: %1', $authorName) : __('New Author')
        );

        return $resultPage;
    }
}

<?php

declare(strict_types=1);

namespace Rokezzz\Blog\Controller\Adminhtml\Tag;

use Magento\Backend\App\Action;
use Magento\Backend\App\Action\Context;
use Magento\Backend\Model\View\Result\Page;
use Magento\Framework\App\Action\HttpGetActionInterface;
use Magento\Framework\View\Result\PageFactory;
use Rokezzz\Blog\Model\TagCreator;

class Edit extends Action implements HttpGetActionInterface
{
    public const ADMIN_RESOURCE = 'Rokezzz_Blog::tags';

    public function __construct(
        Context $context,
        private readonly PageFactory $resultPageFactory,
        private readonly TagCreator $tagCreator
    ) {
        parent::__construct($context);
    }

    public function execute()
    {
        $tagId = (int) $this->getRequest()->getParam('tag_id');
        $tag = $this->tagCreator->create();

        if ($tagId > 0) {
            $tag->getResource()->load($tag, $tagId);
            if (!$tag->getId()) {
                $this->messageManager->addErrorMessage(__('This tag no longer exists.'));

                return $this->resultRedirectFactory->create()->setPath('*/*/');
            }
        }

        /** @var Page $resultPage */
        $resultPage = $this->resultPageFactory->create();
        $resultPage->setActiveMenu('Rokezzz_Blog::tags');
        $resultPage->getConfig()->getTitle()->prepend(
            $tag->getId() ? __('Edit Tag: %1', $tag->getName()) : __('New Tag')
        );

        return $resultPage;
    }
}

<?php
declare(strict_types=1);

namespace Rokezzz\Blog\Controller\Author;

use Magento\Framework\App\Action\HttpGetActionInterface;
use Magento\Framework\App\RequestInterface;
use Magento\Framework\Controller\Result\ForwardFactory;
use Magento\Framework\View\Result\Page;
use Magento\Framework\View\Result\PageFactory;
use Rokezzz\Blog\Model\ResourceModel\Author\CollectionFactory;

class View implements HttpGetActionInterface
{
    public function __construct(
        private readonly RequestInterface $request,
        private readonly PageFactory $pageFactory,
        private readonly ForwardFactory $forwardFactory,
        private readonly CollectionFactory $authorCollectionFactory
    ) {
    }

    public function execute()
    {
        $identifier = trim((string)$this->request->getParam('identifier'), '/');
        $collection = $this->authorCollectionFactory->create();
        $collection->addFieldToFilter('identifier', $identifier);
        $author = $collection->getFirstItem();

        if (!$author->getId()) {
            return $this->forwardFactory->create()->forward('noroute');
        }

        /** @var Page $page */
        $page = $this->pageFactory->create();
        $name = trim($author->getFirstname() . ' ' . $author->getLastname());
        $page->getConfig()->getTitle()->set(__('Author: %1', $name));
        return $page;
    }
}

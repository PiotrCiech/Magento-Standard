<?php
declare(strict_types=1);

namespace Rokezzz\Blog\Controller\Tag;

use Magento\Framework\App\Action\HttpGetActionInterface;
use Magento\Framework\App\RequestInterface;
use Magento\Framework\Controller\Result\ForwardFactory;
use Magento\Framework\View\Result\Page;
use Magento\Framework\View\Result\PageFactory;
use Rokezzz\Blog\Model\ResourceModel\Tag\CollectionFactory;

class View implements HttpGetActionInterface
{
    public function __construct(
        private readonly RequestInterface $request,
        private readonly PageFactory $pageFactory,
        private readonly ForwardFactory $forwardFactory,
        private readonly CollectionFactory $tagCollectionFactory
    ) {
    }

    public function execute()
    {
        $identifier = trim((string)$this->request->getParam('identifier'), '/');
        $collection = $this->tagCollectionFactory->create();
        $collection->addFieldToFilter('identifier', $identifier);
        $tag = $collection->getFirstItem();

        if (!$tag->getId()) {
            return $this->forwardFactory->create()->forward('noroute');
        }

        /** @var Page $page */
        $page = $this->pageFactory->create();
        $page->getConfig()->getTitle()->set(__('Tag: %1', $tag->getName()));
        return $page;
    }
}

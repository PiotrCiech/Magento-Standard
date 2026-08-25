<?php
declare(strict_types=1);

namespace Rokezzz\Blog\Controller\Post;

use Magento\Framework\App\Action\HttpGetActionInterface;
use Magento\Framework\App\RequestInterface;
use Magento\Framework\Controller\Result\ForwardFactory;
use Magento\Framework\View\Result\Page;
use Magento\Framework\View\Result\PageFactory;
use Rokezzz\Blog\Model\ResourceModel\Post\CollectionFactory;

class View implements HttpGetActionInterface
{
    public function __construct(
        private readonly RequestInterface $request,
        private readonly PageFactory $pageFactory,
        private readonly ForwardFactory $forwardFactory,
        private readonly CollectionFactory $postCollectionFactory
    ) {
    }

    public function execute()
    {
        $identifier = trim((string)$this->request->getParam('identifier'), '/');

        if (!$identifier) {
            return $this->forwardFactory->create()->forward('noroute');
        }

        $collection = $this->postCollectionFactory->create();
        $collection->addFieldToFilter('identifier', $identifier)
            ->addFieldToFilter('is_active', 1);

        $post = $collection->getFirstItem();

        if (!$post || !$post->getId()) {
            return $this->forwardFactory->create()->forward('noroute');
        }

        /** @var Page $page */
        $page = $this->pageFactory->create();
        $page->getConfig()->getTitle()->set($post->getData('meta_title') ?: $post->getTitle());

        if ($post->getData('meta_description')) {
            $page->getConfig()->setDescription($post->getData('meta_description'));
        }

        return $page;
    }
}

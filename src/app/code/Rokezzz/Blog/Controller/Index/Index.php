<?php
declare(strict_types=1);

namespace Rokezzz\Blog\Controller\Index;

use Magento\Framework\App\Action\HttpGetActionInterface;
use Magento\Framework\View\Result\PageFactory;
use Magento\Framework\View\Result\Page;

class Index implements HttpGetActionInterface
{
    public function __construct(
        private readonly PageFactory $pageFactory
    ) {
    }

    public function execute(): Page
    {
        $page = $this->pageFactory->create();
        $page->getConfig()->getTitle()->set(__('Blog'));
        return $page;
    }
}

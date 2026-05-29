<?php

declare(strict_types=1);

namespace Rokezzz\DynamicCategories\Controller\Newest;

use Magento\Framework\App\Action\HttpGetActionInterface;
use Magento\Framework\Controller\ResultInterface;
use Magento\Framework\View\Result\PageFactory;

class Index implements HttpGetActionInterface
{
    public function __construct(
        private readonly PageFactory $pageFactory
    ) {
        $this->pageFactory = $pageFactory;
    }

    public function execute(): ResultInterface
    {
        $page = $this->pageFactory->create();
        $page->getConfig()->getTitle()->set(__('New Arrivals'));

        return $page;
    }
}

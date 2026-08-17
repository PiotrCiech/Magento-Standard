<?php

declare(strict_types=1);

namespace Rokezzz\Category\Controller\Newest;

use Magento\Catalog\Model\Layer\Resolver;
use Magento\Framework\App\Action\HttpGetActionInterface;
use Magento\Framework\Controller\ResultInterface;
use Magento\Framework\View\Result\PageFactory;
use Rokezzz\Category\Model\Config;
use Rokezzz\Category\Model\ForwardCreator;

class Index implements HttpGetActionInterface
{
    public function __construct(
        private readonly PageFactory $pageFactory,
        private readonly ForwardCreator $forwardCreator,
        private readonly Resolver $layerResolver,
        private readonly Config $config
    ) {
    }

    public function execute(): ResultInterface
    {
        if (!$this->config->isNewEnabled()) {
            return $this->forwardCreator->create()->forward('noroute');
        }

        $this->layerResolver->create('newest');

        $page = $this->pageFactory->create();
        $page->getConfig()->getTitle()->set(__('New Arrivals'));

        return $page;
    }
}

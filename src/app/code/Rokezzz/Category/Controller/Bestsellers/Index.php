<?php

declare(strict_types=1);

namespace Rokezzz\DynamicCategories\Controller\Bestsellers;

use Magento\Catalog\Model\Layer\Resolver;
use Magento\Framework\App\Action\HttpGetActionInterface;
use Magento\Framework\Controller\Result\ForwardFactory;
use Magento\Framework\Controller\ResultInterface;
use Magento\Framework\View\Result\PageFactory;
use Rokezzz\DynamicCategories\Model\Config;

class Index implements HttpGetActionInterface
{
    public function __construct(
        private readonly PageFactory $pageFactory,
        private readonly ForwardFactory $resultForwardFactory,
        private readonly Resolver $layerResolver,
        private readonly Config $config
    ) {
    }

    public function execute(): ResultInterface
    {
        if (!$this->config->isBestsellersEnabled()) {
            return $this->resultForwardFactory->create()->forward('noroute');
        }

        $this->layerResolver->create('bestsellers');

        $page = $this->pageFactory->create();
        $page->getConfig()->getTitle()->set(__('Bestsellers'));

        return $page;
    }
}

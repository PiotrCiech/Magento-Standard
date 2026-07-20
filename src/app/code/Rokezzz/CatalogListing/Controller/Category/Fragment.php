<?php

declare(strict_types=1);

namespace Rokezzz\CatalogListing\Controller\Category;

use Magento\Framework\App\Action\HttpGetActionInterface;
use Magento\Framework\App\RequestInterface;
use Magento\Framework\Controller\Result\JsonFactory;
use Magento\Framework\Controller\ResultInterface;
use Rokezzz\CatalogListing\Api\ListingFragmentRendererInterface;

final class Fragment implements HttpGetActionInterface
{
    public function __construct(
        private readonly JsonFactory $jsonFactory,
        private readonly RequestInterface $request,
        private readonly ListingFragmentRendererInterface $fragmentRenderer,
    ) {
    }

    public function execute(): ResultInterface
    {
        $categoryId = (int) $this->request->getParam('id');
        $payload = $this->fragmentRenderer->renderCategory($categoryId);

        $result = $this->jsonFactory->create();
        $result->setHeader('Cache-Control', 'no-store, no-cache, must-revalidate, max-age=0', true);
        $result->setData($payload);

        return $result;
    }
}

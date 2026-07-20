<?php

declare(strict_types=1);

namespace Rokezzz\CatalogListing\Controller\Search;

use Magento\Framework\App\Action\HttpGetActionInterface;
use Magento\Framework\Controller\Result\JsonFactory;
use Magento\Framework\Controller\ResultInterface;
use Rokezzz\CatalogListing\Api\ListingFragmentRendererInterface;

class Fragment implements HttpGetActionInterface
{
    public function __construct(
        private readonly JsonFactory $jsonFactory,
        private readonly ListingFragmentRendererInterface $fragmentRenderer,
    ) {
    }

    public function execute(): ResultInterface
    {
        $payload = $this->fragmentRenderer->renderSearch();

        $result = $this->jsonFactory->create();
        $result->setHeader('Cache-Control', 'no-store, no-cache, must-revalidate, max-age=0', true);
        $result->setData($payload);

        return $result;
    }
}

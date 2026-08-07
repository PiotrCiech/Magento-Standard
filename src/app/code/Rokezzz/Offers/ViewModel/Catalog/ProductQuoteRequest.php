<?php

declare(strict_types=1);

namespace Rokezzz\Offers\ViewModel\Catalog;

use Magento\Catalog\Api\Data\ProductInterface;
use Magento\Catalog\Api\ProductRepositoryInterface;
use Magento\Catalog\Model\Product;
use Magento\Customer\Model\Session as CustomerSession;
use Magento\Framework\App\RequestInterface;
use Magento\Framework\Exception\NoSuchEntityException;
use Magento\Framework\UrlInterface;
use Magento\Framework\View\Element\Block\ArgumentInterface;
use Rokezzz\Offers\Model\Product\QuoteRequestEligibilityChecker;

class ProductQuoteRequest implements ArgumentInterface
{
    private ?ProductInterface $product = null;

    private bool $productResolved = false;

    public function __construct(
        private readonly RequestInterface $request,
        private readonly ProductRepositoryInterface $productRepository,
        private readonly CustomerSession $customerSession,
        private readonly QuoteRequestEligibilityChecker $quoteRequestEligibilityChecker,
        private readonly UrlInterface $url
    ) {
    }

    public function getProduct(): ?ProductInterface
    {
        if ($this->productResolved) {
            return $this->product;
        }

        $this->productResolved = true;
        $productId = (int) $this->request->getParam('id');

        if ($productId === 0) {
            return null;
        }

        try {
            $this->product = $this->productRepository->getById($productId);
        } catch (NoSuchEntityException) {
            $this->product = null;
        }

        return $this->product;
    }

    public function requiresQuoteRequest(): bool
    {
        $product = $this->getProduct();

        if (is_null($product)) {
            return false;
        }

        return $this->quoteRequestEligibilityChecker->requiresQuoteRequest($product);
    }

    public function isCustomerLoggedIn(): bool
    {
        return (int) $this->customerSession->getCustomerId() > 0;
    }

    public function getAddUrl(): string
    {
        return $this->url->getUrl('rokezzz_offers/cart/addQuoteRequest');
    }

    public function getLoginUrl(): string
    {
        $product = $this->getProduct();

        if (!$product instanceof Product || is_null($product->getId())) {
            return $this->url->getUrl('customer/account/login');
        }

        return $this->url->getUrl('customer/account/login', [
            'referer' => base64_encode($this->url->getUrl('*/*/*', ['_use_rewrite' => true])),
        ]);
    }

    public function getProductId(): int
    {
        $product = $this->getProduct();

        if (is_null($product) || is_null($product->getId())) {
            return 0;
        }

        return (int) $product->getId();
    }
}

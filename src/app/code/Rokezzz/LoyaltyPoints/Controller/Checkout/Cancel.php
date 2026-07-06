<?php

declare(strict_types=1);

namespace Rokezzz\LoyaltyPoints\Controller\Checkout;

use Magento\Checkout\Model\Session as CheckoutSession;
use Magento\Customer\Model\Session as CustomerSession;
use Magento\Framework\App\Action\HttpPostActionInterface;
use Magento\Framework\App\CsrfAwareActionInterface;
use Magento\Framework\App\Request\InvalidRequestException;
use Magento\Framework\App\RequestInterface;
use Magento\Framework\Controller\Result\Json;
use Magento\Framework\Controller\Result\JsonFactory;
use Rokezzz\LoyaltyPoints\Model\Config;
use Rokezzz\LoyaltyPoints\Model\QuotePointsApplier;

class Cancel implements HttpPostActionInterface, CsrfAwareActionInterface
{
    public function __construct(
        private readonly JsonFactory $resultJsonFactory,
        private readonly CheckoutSession $checkoutSession,
        private readonly CustomerSession $customerSession,
        private readonly Config $config,
        private readonly QuotePointsApplier $quotePointsApplier
    ) {
    }

    public function execute(): Json
    {
        $result = $this->resultJsonFactory->create();
        $quote = $this->checkoutSession->getQuote();
        $storeId = (int) $quote->getStoreId();

        if (!$this->config->isEnabled($storeId) || !$this->customerSession->isLoggedIn()) {
            return $result->setData([
                'success' => false,
                'message' => (string) __('Loyalty points are not available.'),
            ]);
        }

        $this->quotePointsApplier->clear($quote);

        return $result->setData([
            'success' => true,
            'appliedPoints' => 0,
            'message' => (string) __('Loyalty points have been removed.'),
        ]);
    }

    public function createCsrfValidationException(RequestInterface $request): ?InvalidRequestException
    {
        return null;
    }

    public function validateForCsrf(RequestInterface $request): ?bool
    {
        return null;
    }
}

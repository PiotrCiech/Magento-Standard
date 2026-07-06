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
use Rokezzz\LoyaltyPoints\Model\RedemptionCalculator;

class Apply implements HttpPostActionInterface, CsrfAwareActionInterface
{
    public function __construct(
        private readonly JsonFactory $resultJsonFactory,
        private readonly CheckoutSession $checkoutSession,
        private readonly CustomerSession $customerSession,
        private readonly RequestInterface $request,
        private readonly Config $config,
        private readonly QuotePointsApplier $quotePointsApplier,
        private readonly RedemptionCalculator $redemptionCalculator
    ) {
    }

    public function execute(): Json
    {
        $result = $this->resultJsonFactory->create();

        if (!$this->config->isEnabled() || !$this->customerSession->isLoggedIn()) {
            return $result->setData([
                'success' => false,
                'message' => (string) __('Loyalty points are not available.'),
            ]);
        }

        $requestedPoints = (int) $this->request->getParam('points', 0);
        $quote = $this->checkoutSession->getQuote();
        $appliedPoints = $this->quotePointsApplier->apply($quote, $requestedPoints);
        $discount = $this->redemptionCalculator->getMonetaryDiscount(
            $appliedPoints,
            (int) $quote->getStoreId()
        );

        return $result->setData([
            'success' => true,
            'appliedPoints' => $appliedPoints,
            'discount' => $discount,
            'message' => $appliedPoints > 0
                ? (string) __('Applied %1 loyalty points.', $appliedPoints)
                : (string) __('No loyalty points were applied.'),
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

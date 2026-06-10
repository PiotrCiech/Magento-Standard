<?php

declare(strict_types=1);

namespace Rokezzz\ResubmittingArchivedOrders\Controller\Order;

use Magento\Customer\Model\Session as CustomerSession;
use Magento\Framework\App\Action\HttpGetActionInterface;
use Magento\Framework\Controller\Result\Redirect;
use Magento\Framework\Controller\ResultFactory;
use Magento\Framework\Controller\ResultInterface;
use Rokezzz\ResubmittingArchivedOrders\Model\Config;

class History implements HttpGetActionInterface
{
    public function __construct(
        private readonly ResultFactory $resultFactory,
        private readonly Config $config,
        private readonly CustomerSession $customerSession
    ) {
    }

    public function execute(): ResultInterface
    {
        /** @var Redirect $redirect */
        $redirect = $this->resultFactory->create(ResultFactory::TYPE_REDIRECT);

        if ((int) $this->customerSession->getCustomerId() === 0) {
            return $redirect->setPath('customer/account/login');
        }

        if (!$this->config->isEnabled()) {
            return $redirect->setPath('sales/order/history');
        }

        return $this->resultFactory->create(ResultFactory::TYPE_PAGE);
    }
}

<?php

declare(strict_types=1);

namespace Rokezzz\AvailabilityNotification\Plugin\ProductAlert\Controller;

use Magento\Framework\App\Action\Action;
use Magento\Framework\App\ActionInterface;
use Magento\Framework\App\Response\RedirectInterface;
use Magento\Framework\Controller\Result\Redirect;
use Magento\Framework\Controller\ResultFactory;
use Magento\Framework\Message\ManagerInterface as MessageManager;
use Rokezzz\AvailabilityNotification\Model\Config;

final class BlockStockAlertActionsWhenDisabledPlugin
{
    public function __construct(
        private readonly Config $config,
        private readonly ResultFactory $resultFactory,
        private readonly RedirectInterface $redirect,
        private readonly MessageManager $messageManager
    ) {
    }

    /**
     * @param Action $subject
     * @param callable $proceed
     * @return mixed
     */
    public function aroundExecute(Action $subject, callable $proceed)
    {
        if ($this->config->isEnabled()) {
            return $proceed();
        }

        if (!$subject instanceof ActionInterface) {
            return $proceed();
        }

        $this->messageManager->addErrorMessage(__('Availability notifications are disabled.'));

        /** @var Redirect $redirect */
        $redirect = $this->resultFactory->create(ResultFactory::TYPE_REDIRECT);
        $redirect->setUrl($this->redirect->getRefererUrl() ?: '/');

        return $redirect;
    }
}

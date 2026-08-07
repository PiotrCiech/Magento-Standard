<?php

declare(strict_types=1);

namespace Rokezzz\Wishlist\Plugin\Wishlist\Controller;

use Magento\Framework\App\ActionInterface;
use Magento\Framework\App\Response\RedirectInterface;
use Magento\Framework\Controller\Result\Redirect;
use Magento\Framework\Controller\ResultFactory;
use Magento\Framework\Message\ManagerInterface as MessageManager;
use Rokezzz\Wishlist\Model\Config;

final class BlockWishlistSharingWhenDisabledPlugin
{
    public function __construct(
        private readonly Config $config,
        private readonly ResultFactory $resultFactory,
        private readonly RedirectInterface $redirect,
        private readonly MessageManager $messageManager
    ) {
    }

    public function aroundExecute(ActionInterface $subject, callable $proceed)
    {
        unset($subject);

        if ($this->config->isSharingEnabled()) {
            return $proceed();
        }

        $this->messageManager->addErrorMessage(__('Wishlist sharing is disabled.'));

        /** @var Redirect $redirect */
        $redirect = $this->resultFactory->create(ResultFactory::TYPE_REDIRECT);
        $redirect->setUrl($this->redirect->getRefererUrl() ?: '/');

        return $redirect;
    }
}

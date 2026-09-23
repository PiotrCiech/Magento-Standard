<?php

declare(strict_types=1);

namespace Rokezzz\Compare\Plugin\Catalog\Controller\Product\Compare;

use Magento\Catalog\Controller\Product\Compare as CompareController;
use Magento\Framework\App\Response\RedirectInterface;
use Magento\Framework\Controller\Result\Redirect;
use Magento\Framework\Controller\ResultFactory;
use Magento\Framework\Message\ManagerInterface as MessageManager;
use Rokezzz\Compare\Model\Config;

final class BlockCompareActionsWhenDisabledPlugin
{
    public function __construct(
        private readonly Config $config,
        private readonly ResultFactory $resultFactory,
        private readonly RedirectInterface $redirect,
        private readonly MessageManager $messageManager
    ) {
    }

    public function aroundExecute(CompareController $subject, callable $proceed)
    {
        unset($subject);

        if ($this->config->isEnabled()) {
            return $proceed();
        }

        $this->messageManager->addErrorMessage(__('Product compare is disabled.'));

        /** @var Redirect $redirect */
        $redirect = $this->resultFactory->create(ResultFactory::TYPE_REDIRECT);
        $redirect->setUrl($this->redirect->getRefererUrl() ?: '/');

        return $redirect;
    }
}

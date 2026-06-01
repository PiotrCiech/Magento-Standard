<?php

declare(strict_types=1);

namespace Rokezzz\Wishlist\Plugin\Wishlist\Controller;

use Magento\Framework\App\Action\Action;
use Magento\Framework\App\ActionInterface;
use Magento\Framework\App\Response\RedirectInterface;
use Magento\Framework\Controller\Result\Redirect;
use Magento\Framework\Controller\ResultFactory;
use Magento\Wishlist\Helper\Data as WishlistHelper;

/**
 * Blocks direct access to wishlist URLs when wishlist is disabled in configuration.
 */
class BlockWishlistWhenDisabledPlugin
{
    public function __construct(
        private readonly WishlistHelper $wishlistHelper,
        private readonly ResultFactory $resultFactory,
        private readonly RedirectInterface $redirect
    ) {
    }

    /**
     * @param Action $subject
     * @param callable $proceed
     * @return mixed
     */
    public function aroundExecute(Action $subject, callable $proceed)
    {
        if ($this->wishlistHelper->isAllow()) {
            return $proceed();
        }

        if (!$subject instanceof ActionInterface) {
            return $proceed();
        }

        /** @var Redirect $redirect */
        $redirect = $this->resultFactory->create(ResultFactory::TYPE_REDIRECT);
        $redirect->setUrl($this->redirect->getRefererUrl() ?: '/');

        return $redirect;
    }
}

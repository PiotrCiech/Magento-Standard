<?php

declare(strict_types=1);

namespace Rokezzz\DynamicCategories\Model\Layer;

use Magento\Catalog\Model\Category;
use Magento\Catalog\Model\Layer\StateKeyInterface;
use Magento\Customer\Model\Session;
use Magento\Store\Model\StoreManagerInterface;

final class StateKey implements StateKeyInterface
{
    public function __construct(
        private readonly StoreManagerInterface $storeManager,
        private readonly Session $customerSession,
        private readonly string $suffix = 'dynamic'
    ) {
    }

    /**
     * @param Category $category
     */
    public function toString($category): string
    {
        return 'STORE_' . $this->storeManager->getStore()->getId()
            . '_DC_' . $this->suffix
            . '_CAT_' . $category->getId()
            . '_CUSTGROUP_' . $this->customerSession->getCustomerGroupId();
    }
}

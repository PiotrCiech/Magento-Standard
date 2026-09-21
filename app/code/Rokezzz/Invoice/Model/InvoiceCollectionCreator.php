<?php

declare(strict_types=1);

namespace Rokezzz\Invoice\Model;

use Magento\Framework\ObjectManagerInterface;
use Magento\Sales\Model\ResourceModel\Order\Invoice\Collection;

class InvoiceCollectionCreator
{
    private ObjectManagerInterface $objectManager;

    public function __construct(ObjectManagerInterface $objectManager)
    {
        $this->objectManager = $objectManager;
    }

    public function create(): Collection
    {
        return $this->objectManager->create(Collection::class);
    }
}

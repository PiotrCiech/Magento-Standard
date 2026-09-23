<?php

declare(strict_types=1);

namespace Rokezzz\Category\Model;

use Magento\Framework\Controller\Result\Forward;
use Magento\Framework\ObjectManagerInterface;

class ForwardCreator
{
    private ObjectManagerInterface $objectManager;

    public function __construct(ObjectManagerInterface $objectManager)
    {
        $this->objectManager = $objectManager;
    }

    public function create(): Forward
    {
        return $this->objectManager->create(Forward::class);
    }
}

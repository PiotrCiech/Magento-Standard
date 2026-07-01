<?php

declare(strict_types=1);

namespace Rokezzz\Orders\Block\Order;

use Magento\Framework\View\Element\Template;
use Magento\Framework\View\Element\Template\Context;
use Magento\Sales\Api\Data\OrderInterface;

class Comment extends Template
{
    /**
     * @var string[]
     */
    private const ORDER_BLOCK_NAMES = [
        'sales.order.view',
        'sales.order.info',
        'order_tab_info',
        'order_info',
    ];

    public function __construct(
        Context $context,
        array $data = []
    ) {
        parent::__construct($context, $data);
    }

    public function getOrder(): ?OrderInterface
    {
        $order = $this->resolveOrderFromParentBlocks();

        if (!is_null($order)) {
            return $order;
        }

        return $this->resolveOrderFromLayoutBlocks();
    }

    private function resolveOrderFromParentBlocks(): ?OrderInterface
    {
        $block = $this->getParentBlock();

        while (!is_null($block)) {
            if (method_exists($block, 'getOrder')) {
                $order = $block->getOrder();

                return $order instanceof OrderInterface ? $order : null;
            }

            $block = $block->getParentBlock();
        }

        return null;
    }

    private function resolveOrderFromLayoutBlocks(): ?OrderInterface
    {
        foreach (self::ORDER_BLOCK_NAMES as $blockName) {
            $layoutBlock = $this->getLayout()->getBlock($blockName);

            if (is_null($layoutBlock) || !method_exists($layoutBlock, 'getOrder')) {
                continue;
            }

            $order = $layoutBlock->getOrder();

            if ($order instanceof OrderInterface) {
                return $order;
            }
        }

        return null;
    }
}

<?php

declare(strict_types=1);

namespace Rokezzz\DynamicCategories\Observer;

use Magento\Framework\Data\Tree\NodeFactory;
use Magento\Framework\Event\Observer;
use Magento\Framework\Event\ObserverInterface;
use Magento\Framework\UrlInterface;
use Rokezzz\DynamicCategories\Model\Config;

class AddToTopmenu implements ObserverInterface
{
    private NodeFactory $nodeFactory;
    private UrlInterface $urlBuilder;
    private Config $config;

    public function __construct(
        NodeFactory $nodeFactory,
        UrlInterface $urlBuilder,
        Config $config
    ) {
        $this->nodeFactory = $nodeFactory;
        $this->urlBuilder = $urlBuilder;
        $this->config = $config;
    }

    public function execute(Observer $observer): void
    {
        /** @var \Magento\Framework\Data\Tree\Node $menu */
        $menu = $observer->getEvent()->getMenu();
        $tree = $menu->getTree();

        if ($this->config->isNewEnabled()) {
            $nodeData = [
                'name' => __('New Arrivals'),
                'id' => 'dynamiccategories-newest',
                'url' => $this->urlBuilder->getUrl('dynamiccategories/newest/index'),
                'has_active' => false,
                'is_active' => false,
            ];

            $menu->addChild(
                $this->nodeFactory->create(
                    [
                        'data' => $nodeData,
                        'idField' => 'id',
                        'tree' => $tree,
                    ]
                )
            );
        }

        if ($this->config->isBestsellersEnabled()) {
            $nodeData = [
                'name' => __('Bestsellers'),
                'id' => 'dynamiccategories-bestsellers',
                'url' => $this->urlBuilder->getUrl('dynamiccategories/bestsellers/index'),
                'has_active' => false,
                'is_active' => false,
            ];

            $menu->addChild(
                $this->nodeFactory->create(
                    [
                        'data' => $nodeData,
                        'idField' => 'id',
                        'tree' => $tree,
                    ]
                )
            );
        }
    }
}

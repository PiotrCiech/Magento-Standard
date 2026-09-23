<?php

declare(strict_types=1);

namespace Rokezzz\CategoryAdvance\Block\Catalog;

use Magento\Catalog\Model\Category;
use Magento\Framework\Registry;
use Magento\Framework\View\Element\Template;
use Magento\Framework\View\Element\Template\Context;
use Rokezzz\CategoryAdvance\Model\Config;

class DownloadButton extends Template
{
    public function __construct(
        Context $context,
        private readonly Config $config,
        private readonly Registry $registry,
        array $data = []
    ) {
        parent::__construct($context, $data);
    }

    public function isVisible(): bool
    {
        if (!$this->config->isPdfCatalogEnabled()) {
            return false;
        }

        return $this->getCategoryId() > 0;
    }

    public function getCategoryId(): int
    {
        $category = $this->registry->registry('current_category');

        if (!$category instanceof Category) {
            return 0;
        }

        return (int) $category->getId();
    }

    public function getDownloadUrl(): string
    {
        return $this->getUrl('categoryadvance/catalog/download', [
            'id' => $this->getCategoryId(),
        ]);
    }
}

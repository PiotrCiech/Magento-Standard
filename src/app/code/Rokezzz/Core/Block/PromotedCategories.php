<?php
declare(strict_types=1);

namespace Rokezzz\Core\Block;

use Magento\Catalog\Model\ResourceModel\Category\CollectionFactory;
use Magento\Framework\View\Element\Template;
use Magento\Framework\App\Config\ScopeConfigInterface;
use Magento\Store\Model\ScopeInterface;

class PromotedCategories extends Template
{
    const XML_PATH_MODULE_ENABLE = 'rokezzz_settings/general/enable';
    const XML_PATH_SHOW_IMAGES = 'rokezzz_settings/general/show_category_images';

    public function __construct(
        Template\Context $context,
        private readonly CollectionFactory $categoryCollectionFactory,
        private readonly ScopeConfigInterface $scopeConfig,
        array $data = []
    ) {
        parent::__construct($context, $data);
    }

    public function isModuleEnabled(): bool
    {
        return $this->scopeConfig->isSetFlag(
            self::XML_PATH_MODULE_ENABLE,
            ScopeInterface::SCOPE_STORE
        );
    }

    public function isShowImagesEnabled(): bool
    {
        return $this->scopeConfig->isSetFlag(
            self::XML_PATH_SHOW_IMAGES,
            ScopeInterface::SCOPE_STORE
        );
    }

    public function getPromotedCategories()
    {
        $collection = $this->categoryCollectionFactory->create();

        if (!$this->isModuleEnabled()) {
            $collection->addFieldToFilter('entity_id', ['null' => true]);
            return $collection;
        }

        $collection->addAttributeToSelect(['name', 'image', 'url_key'])
            ->addAttributeToFilter('is_active', 1)
            ->addAttributeToFilter('is_promoted_on_homepage', 1)
            ->setOrder('position', 'ASC');
        return $collection;
    }
}


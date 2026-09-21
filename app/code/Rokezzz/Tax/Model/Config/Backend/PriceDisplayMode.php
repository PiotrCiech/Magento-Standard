<?php

declare(strict_types=1);

namespace Rokezzz\Tax\Model\Config\Backend;

use Magento\Framework\App\Cache\TypeListInterface;
use Magento\Framework\App\Config\ScopeConfigInterface;
use Magento\Framework\App\Config\Value;
use Magento\Framework\Data\Collection\AbstractDb;
use Magento\Framework\Model\Context;
use Magento\Framework\Model\ResourceModel\AbstractResource;
use Magento\Framework\Registry;
use Rokezzz\Tax\Model\Config\PriceDisplayModeSynchronizer;

class PriceDisplayMode extends Value
{
    /**
     * @param mixed[] $data
     */
    public function __construct(
        Context $context,
        Registry $registry,
        ScopeConfigInterface $config,
        TypeListInterface $cacheTypeList,
        private readonly PriceDisplayModeSynchronizer $priceDisplayModeSynchronizer,
        ?AbstractResource $resource = null,
        ?AbstractDb $resourceCollection = null,
        array $data = []
    ) {
        parent::__construct($context, $registry, $config, $cacheTypeList, $resource, $resourceCollection, $data);
    }

    public function beforeSave()
    {
        $this->setValue(
            (string) $this->priceDisplayModeSynchronizer->normalize((int) $this->getValue())
        );

        return parent::beforeSave();
    }

    public function afterSave()
    {
        if ($this->isValueChanged()) {
            $scope = $this->getScope() ?: ScopeConfigInterface::SCOPE_TYPE_DEFAULT;
            $this->priceDisplayModeSynchronizer->applyFromAdminField(
                (int) $this->getValue(),
                $scope,
                (int) $this->getScopeId()
            );
            $this->_cacheManager->clean(['checkout_quote']);
        }

        return parent::afterSave();
    }
}

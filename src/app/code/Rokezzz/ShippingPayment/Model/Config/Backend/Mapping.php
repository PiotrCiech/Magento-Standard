<?php

declare(strict_types=1);

namespace Rokezzz\ShippingPayment\Model\Config\Backend;

use Magento\Framework\App\Cache\TypeListInterface;
use Magento\Framework\App\Config\ScopeConfigInterface;
use Magento\Framework\App\Config\Value;
use Magento\Framework\Data\Collection\AbstractDb;
use Magento\Framework\Model\Context;
use Magento\Framework\Model\ResourceModel\AbstractResource;
use Magento\Framework\Registry;
use Magento\Framework\Serialize\Serializer\Json;

class Mapping extends Value
{
    /**
     * @param mixed[] $data
     */
    public function __construct(
        Context $context,
        Registry $registry,
        ScopeConfigInterface $config,
        TypeListInterface $cacheTypeList,
        private readonly Json $json,
        ?AbstractResource $resource = null,
        ?AbstractDb $resourceCollection = null,
        array $data = []
    ) {
        parent::__construct($context, $registry, $config, $cacheTypeList, $resource, $resourceCollection, $data);
    }

    public function beforeSave()
    {
        $value = $this->getValue();

        if (is_array($value)) {
            unset($value['__empty']);
            $cleaned = [];

            foreach ($value as $shippingMethod => $payments) {
                if (!is_array($payments)) {
                    continue;
                }

                $codes = array_values(
                    array_filter(
                        array_map('strval', $payments),
                        static fn (string $code): bool => $code !== ''
                    )
                );

                if ($codes === []) {
                    continue;
                }

                $cleaned[(string) $shippingMethod] = $codes;
            }

            $this->setValue($this->json->serialize($cleaned));
        }

        return parent::beforeSave();
    }

    protected function _afterLoad()
    {
        $value = $this->getValue();

        if (is_string($value) && $value !== '') {
            $decoded = $this->json->unserialize($value);
            $this->setValue(is_array($decoded) ? $decoded : []);
        }

        if (is_null($this->getValue())) {
            $this->setValue([]);
        }

        return parent::_afterLoad();
    }
}

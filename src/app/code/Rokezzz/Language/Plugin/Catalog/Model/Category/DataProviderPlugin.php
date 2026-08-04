<?php

declare(strict_types=1);

namespace Rokezzz\Language\Plugin\Catalog\Model\Category;

use Magento\Catalog\Model\Category\DataProvider;

final class DataProviderPlugin
{
    /**
     * @param array<string, mixed> $result
     * @return array<string, mixed>
     */
    public function afterGetMeta(DataProvider $subject, array $result): array
    {
        return $this->translateMeta($result);
    }

    /**
     * @param array<string, mixed> $meta
     * @return array<string, mixed>
     */
    private function translateMeta(array $meta): array
    {
        foreach ($meta as $key => $config) {
            if (!is_array($config)) {
                continue;
            }

            if (isset($config['arguments']['data']['config'])
                && is_array($config['arguments']['data']['config'])
            ) {
                $fieldConfig = &$meta[$key]['arguments']['data']['config'];

                if (isset($fieldConfig['label']) && is_string($fieldConfig['label'])) {
                    $fieldConfig['label'] = __($fieldConfig['label']);
                }

                if (isset($fieldConfig['options']) && is_array($fieldConfig['options'])) {
                    foreach ($fieldConfig['options'] as $optionIndex => $option) {
                        if (!is_array($option)) {
                            continue;
                        }
                        if (isset($option['label']) && is_string($option['label'])) {
                            $fieldConfig['options'][$optionIndex]['label'] = __($option['label']);
                        }
                    }
                }
                
                unset($fieldConfig);
            }

            if (isset($config['children']) && is_array($config['children'])) {
                $meta[$key]['children'] = $this->translateMeta($config['children']);
            }
        }

        return $meta;
    }
}

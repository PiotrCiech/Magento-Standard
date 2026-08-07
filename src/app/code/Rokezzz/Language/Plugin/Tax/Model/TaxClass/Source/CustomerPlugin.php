<?php

declare(strict_types=1);

namespace Rokezzz\Language\Plugin\Tax\Model\TaxClass\Source;

use Magento\Tax\Model\TaxClass\Source\Customer;

/**
 * Tax class option labels are raw DB names; wrap with __() for i18n.
 */
final class CustomerPlugin
{
    /**
     * @param array<int, array<string, mixed>> $result
     * @return array<int, array<string, mixed>>
     */
    public function afterGetAllOptions(Customer $subject, array $result): array
    {
        foreach ($result as &$item) {
            if (isset($item['label']) && is_string($item['label'])) {
                $item['label'] = __($item['label']);
            }
        }
        
        unset($item);

        return $result;
    }
}

<?php

declare(strict_types=1);

namespace Rokezzz\Language\Plugin\Customer\Ui\Component\Listing\Column\Group;

use Magento\Customer\Ui\Component\Listing\Column\Group\Options;

/**
 * Customer group option labels are raw DB codes; wrap with __() for i18n.
 */
final class OptionsPlugin
{
    /**
     * @param array<int, array<string, mixed>> $result
     * @return array<int, array<string, mixed>>
     */
    public function afterToOptionArray(Options $subject, array $result): array
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

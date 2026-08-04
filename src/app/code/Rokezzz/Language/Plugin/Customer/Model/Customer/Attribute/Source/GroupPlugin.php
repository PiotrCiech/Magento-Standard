<?php

declare(strict_types=1);

namespace Rokezzz\Language\Plugin\Customer\Model\Customer\Attribute\Source;

use Magento\Customer\Model\Customer\Attribute\Source\Group;

/**
 * Customer form group option labels are raw DB codes; wrap with __() for i18n.
 */
final class GroupPlugin
{
    /**
     * @param array<int, array<string, mixed>> $result
     * @return array<int, array<string, mixed>>
     */
    public function afterGetAllOptions(Group $subject, array $result): array
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

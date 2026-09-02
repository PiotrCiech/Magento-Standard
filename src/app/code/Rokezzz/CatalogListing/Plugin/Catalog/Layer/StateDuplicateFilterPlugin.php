<?php

declare(strict_types=1);

namespace Rokezzz\CatalogListing\Plugin\Catalog\Layer;

use Magento\Catalog\Model\Layer\Filter\Item;
use Magento\Catalog\Model\Layer\State;

final class StateDuplicateFilterPlugin
{
    /**
     * @param callable(Item): State $proceed
     */
    public function aroundAddFilter(State $subject, callable $proceed, $filter): State
    {
        if (!$filter instanceof Item) {
            return $proceed($filter);
        }

        $requestVar = $filter->getFilter()->getRequestVar();
        $value = $filter->getValue();

        foreach ($subject->getFilters() as $existing) {
            if (!$existing instanceof Item) {
                continue;
            }

            if ($existing->getFilter()->getRequestVar() !== $requestVar) {
                continue;
            }

            if ($existing->getValue() == $value) {
                return $subject;
            }
        }

        return $proceed($filter);
    }
}

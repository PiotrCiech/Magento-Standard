<?php

declare(strict_types=1);

namespace Rokezzz\CatalogListing\Plugin\Catalog\Layer;

use Magento\Catalog\Model\Layer\Filter\FilterInterface;
use Magento\Catalog\Model\Layer\FilterList;

final class FilterListUniqueFiltersPlugin
{
    /**
     * @param FilterInterface[] $result
     * @return FilterInterface[]
     */
    public function afterGetFilters(FilterList $subject, array $result): array
    {
        $unique = [];
        $seenRequestVars = [];

        foreach ($result as $filter) {
            if (!$filter instanceof FilterInterface) {
                $unique[] = $filter;

                continue;
            }

            $requestVar = $filter->getRequestVar();
            if (isset($seenRequestVars[$requestVar])) {
                continue;
            }

            $seenRequestVars[$requestVar] = true;
            $unique[] = $filter;
        }

        return $unique;
    }
}

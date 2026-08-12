<?php

declare(strict_types=1);

namespace Rokezzz\DynamicCategories\Model\Newest;

use Magento\Catalog\Model\ResourceModel\Product\Collection;
use Magento\Framework\Stdlib\DateTime\TimezoneInterface;
use Zend_Db_Expr;

final class NewsDateFilter
{
    public function __construct(
        private readonly TimezoneInterface $localeDate
    ) {
    }

    public function apply(Collection $collection): void
    {
        $todayStartOfDayDate = $this->localeDate->date()->setTime(0, 0, 0)->format('Y-m-d H:i:s');
        $todayEndOfDayDate = $this->localeDate->date()->setTime(23, 59, 59)->format('Y-m-d H:i:s');

        $collection->addAttributeToFilter(
            'news_from_date',
            [
                'or' => [
                    0 => ['date' => true, 'to' => $todayEndOfDayDate],
                    1 => ['is' => new Zend_Db_Expr('null')],
                ],
            ],
            'left'
        )->addAttributeToFilter(
            'news_to_date',
            [
                'or' => [
                    0 => ['date' => true, 'from' => $todayStartOfDayDate],
                    1 => ['is' => new Zend_Db_Expr('null')],
                ],
            ],
            'left'
        )->addAttributeToFilter(
            [
                ['attribute' => 'news_from_date', 'is' => new Zend_Db_Expr('not null')],
                ['attribute' => 'news_to_date', 'is' => new Zend_Db_Expr('not null')],
            ]
        )->addAttributeToSort(
            'news_from_date',
            Collection::SORT_ORDER_DESC
        );
    }

    public function isCurrentlyNew(?string $newsFromDate, ?string $newsToDate): bool
    {
        $from = ($newsFromDate !== null && $newsFromDate !== '') ? $newsFromDate : null;
        $to = ($newsToDate !== null && $newsToDate !== '') ? $newsToDate : null;

        if (is_null($from) && is_null($to)) {
            return false;
        }

        $now = $this->localeDate->date()->getTimestamp();

        if (!is_null($from) && strtotime($from) > $now) {
            return false;
        }

        if (!is_null($to) && strtotime($to) < $now) {
            return false;
        }

        return true;
    }
}

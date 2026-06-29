<?php

declare(strict_types=1);

namespace Rokezzz\Gus\Api;

use Magento\Framework\Exception\LocalizedException;
use Rokezzz\Gus\Api\Data\CompanyDataInterface;

interface CompanyLookupInterface
{
    /**
     * @throws LocalizedException
     */
    public function findByNip(string $nip, ?int $storeId = null): CompanyDataInterface;
}

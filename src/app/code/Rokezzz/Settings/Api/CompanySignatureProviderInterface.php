<?php

declare(strict_types=1);

namespace Rokezzz\Settings\Api;

interface CompanySignatureProviderInterface
{
    /**
     * @return string[]
     */
    public function getLines(?int $storeId = null): array;

    public function getHtml(?int $storeId = null): string;

    public function getPlainText(?int $storeId = null): string;
}

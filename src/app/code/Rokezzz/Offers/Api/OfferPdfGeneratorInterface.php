<?php

declare(strict_types=1);

namespace Rokezzz\Offers\Api;

use Magento\Framework\Exception\LocalizedException;
use Rokezzz\Offers\Api\Data\OfferInterface;

interface OfferPdfGeneratorInterface
{
    /**
     * @throws LocalizedException
     */
    public function generate(OfferInterface $offer): string;
}

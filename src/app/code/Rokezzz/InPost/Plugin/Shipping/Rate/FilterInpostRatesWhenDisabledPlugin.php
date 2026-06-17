<?php

declare(strict_types=1);

namespace Rokezzz\InPost\Plugin\Shipping\Rate;

use Magento\Quote\Model\Quote\Address\RateResult\Method;
use Magento\Shipping\Model\Rate\Result;
use Rokezzz\InPost\Model\CarrierCodeMatcher;
use Rokezzz\InPost\Model\Config;

class FilterInpostRatesWhenDisabledPlugin
{
    public function __construct(
        private readonly Config $config,
        private readonly CarrierCodeMatcher $carrierCodeMatcher
    ) {
    }

    /**
     * @param Result $subject
     * @param array<int, AbstractResult> $rates
     * @return array<int, AbstractResult>
     */
    public function afterGetAllRates(Result $subject, array $rates): array
    {
        if ($this->config->isEnabled()) {
            return $rates;
        }

        $filteredRates = [];

        foreach ($rates as $rate) {
            if ($rate instanceof Method
                && $this->carrierCodeMatcher->isInpostCarrier((string) $rate->getCarrier())
            ) {
                continue;
            }

            $filteredRates[] = $rate;
        }

        return $filteredRates;
    }
}

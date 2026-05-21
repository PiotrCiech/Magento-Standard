<?php

declare(strict_types=1);

namespace Rokezzz\Tax\Setup\Patch\Data;

use Magento\Framework\App\Config\Storage\WriterInterface;
use Magento\Framework\Setup\Patch\DataPatchInterface;
use Magento\Framework\Setup\ModuleDataSetupInterface;
use Magento\Tax\Model\Calculation\Rate;
use Magento\Tax\Model\Calculation\RateFactory;
use Magento\Tax\Model\Calculation\RuleFactory;
use Magento\Tax\Api\TaxRateRepositoryInterface;
use Magento\Tax\Api\TaxRuleRepositoryInterface;

class AddTaxRulePatch implements DataPatchInterface
{

    private const COUNTRY_ID = 'PL';
    private const REGION_NAME = 'Wielkopolskie';
    
    private const TAX_RATES = [
        'PL-0%-Rate' => 0.00,
        'PL-5%-Rate' => 5.00,
        'PL-8%-Rate' => 8.00,
        'PL-23%-Rate' => 23.00,
    ];

    private const CONFIG_TO_INSTALL = [
        'tax/calculation/price_includes_tax' => '1',
        'shipping/origin/country_id' => self::COUNTRY_ID,
        'shipping/origin/region' => self::REGION_NAME,
        'shipping/origin/postcode' => '63-510',
        'shipping/origin/city' => 'Mikstat',
        'shipping/origin/street_line1' => 'Biskupice Zabaryczne 169',
    ];
    public function __construct(
        private readonly ModuleDataSetupInterface $moduleDataSetup,
        private readonly RateFactory $rateFactory,
        private readonly RuleFactory $ruleFactory,
        private readonly TaxRuleRepositoryInterface $taxRuleRepository,
        private readonly TaxRateRepositoryInterface $taxRateRepository,
        private readonly WriterInterface $configWriter,
    ) {
    }

    public static function getDependencies(): array
    {
        return [];
    }

    public function getAliases(): array
    {
        return [];
    }

    public function apply(): void
    {
        $connection = $this->moduleDataSetup->getConnection();
        $connection->startSetup();

        $taxRates = $this->createTaxRates(self::TAX_RATES);
        $createdTaxRateIds = array_filter(
            array_map(
                fn(Rate $taxRate): ?int => $taxRate->getId() !== null ? (int) $taxRate->getId() : null,
                $taxRates
            )
        );

        $this->createTaxRule('main-rule', $createdTaxRateIds);

        foreach (self::CONFIG_TO_INSTALL as $path => $value) {
            $this->configWriter->save($path, $value);
        }


        $connection->endSetup();
    }

    private function createTaxRates(array $taxRates): array
    {
        $rates = [];

        foreach ($taxRates as $code => $rate) {
            $taxRate = $this->rateFactory->create();
            $taxRate->setCode($code);
            $taxRate->setTaxCountryId("PL");
            $taxRate->setTaxPostcode("*");
            $taxRate->setRate($rate);
            $this->taxRateRepository->save($taxRate);

            $rates[] = $taxRate;
        }

        return $rates;
    }

    private function createTaxRule(string $ruleCode, array $taxRateIds): void
    {
        $taxRule = $this->ruleFactory->create();
        $taxRule->setCode($ruleCode);
        $taxRule->setPriority(0);
        $taxRule->setCustomerTaxClassIds([3]);
        $taxRule->setProductTaxClassIds([2]);
        $taxRule->setTaxRateIds($taxRateIds);

        $this->taxRuleRepository->save($taxRule);
    }
}
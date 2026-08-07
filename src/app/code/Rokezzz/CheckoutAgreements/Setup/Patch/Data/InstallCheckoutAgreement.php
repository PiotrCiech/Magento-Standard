<?php

declare(strict_types=1);

namespace Rokezzz\CheckoutAgreements\Setup\Patch\Data;

use Magento\CheckoutAgreements\Model\Agreement;
use Magento\CheckoutAgreements\Model\AgreementModeOptions;
use Magento\CheckoutAgreements\Model\ResourceModel\Agreement as AgreementResource;
use Magento\Config\Model\ResourceModel\Config as ConfigResource;
use Magento\Framework\Setup\ModuleDataSetupInterface;
use Magento\Framework\Setup\Patch\DataPatchInterface;
use Magento\Store\Model\Store;

final class InstallCheckoutAgreement implements DataPatchInterface
{
    private const AGREEMENT_NAME = 'Store terms and conditions';
    private const AGREEMENT_CONTENT = 'I accept the store terms and conditions.';
    private const AGREEMENT_CHECKBOX_TEXT = 'I have read and accept the store terms and conditions';
    private const ENABLE_AGREEMENTS_CONFIG_PATH = 'checkout/options/enable_agreements';

    public function __construct(
        private readonly ModuleDataSetupInterface $moduleDataSetup,
        private readonly Agreement $agreement,
        private readonly AgreementResource $agreementResource,
        private readonly ConfigResource $configResource
    ) {
    }

    public function apply(): void
    {
        $connection = $this->moduleDataSetup->getConnection();
        $connection->startSetup();
        $this->agreementResource->load($this->agreement, self::AGREEMENT_NAME, 'name');

        $this->agreement->setData('name', self::AGREEMENT_NAME);
        $this->agreement->setData('content', self::AGREEMENT_CONTENT);
        $this->agreement->setData('checkbox_text', self::AGREEMENT_CHECKBOX_TEXT);
        $this->agreement->setData('is_active', 1);
        $this->agreement->setData('is_html', 0);
        $this->agreement->setData('mode', AgreementModeOptions::MODE_MANUAL);
        $this->agreement->setData('stores', [Store::DEFAULT_STORE_ID]);

        $this->agreementResource->save($this->agreement);

        $this->configResource->saveConfig(
            self::ENABLE_AGREEMENTS_CONFIG_PATH,
            '1',
            'default',
            0
        );
        $connection->endSetup();
    }

    public static function getDependencies(): array
    {
        return [];
    }

    public function getAliases(): array
    {
        return [];
    }
}

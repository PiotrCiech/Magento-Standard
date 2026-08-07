<?php

declare(strict_types=1);

namespace Rokezzz\ReCaptcha\Setup\Patch\Data;

use Magento\Framework\App\Config\Storage\WriterInterface;
use Magento\Framework\Setup\ModuleDataSetupInterface;
use Magento\Framework\Setup\Patch\DataPatchInterface;

final class RecaptchaConfigPatch implements DataPatchInterface
{
    private const CONFIG_TO_UPDATE = [
        'admin/captcha/enable' => 0,
        'admin/captcha/forms' => null,
        'recaptcha_frontend/type_for/customer_login' => 'recaptcha_v3',
        'recaptcha_frontend/type_for/customer_forgot_password' => 'recaptcha_v3',
        'recaptcha_frontend/type_for/customer_create' => 'recaptcha_v3',
        'recaptcha_frontend/type_for/customer_edit' => 'recaptcha_v3',
        'recaptcha_frontend/type_for/contact' => 'recaptcha_v3',
        'recaptcha_frontend/type_for/product_review' => 'recaptcha_v3',
        'recaptcha_frontend/type_for/newsletter' => 'recaptcha_v3',
        'recaptcha_frontend/type_for/sendfriend' => 'recaptcha_v3',
        'recaptcha_frontend/type_for/place_order' => 'recaptcha_v3',
        'recaptcha_frontend/type_for/coupon_code' => 'recaptcha_v3',
        'recaptcha_frontend/type_for/paypal_payflowpro' => 'recaptcha_v3',
        'recaptcha_frontend/type_for/braintree' => 'recaptcha_v3',
        'recaptcha_backend/type_for/user_login' => 'recaptcha_v3',
        'recaptcha_backend/type_for/user_forgot_password' => 'recaptcha_v3',
    ];

    public function __construct(
        private readonly ModuleDataSetupInterface $moduleDataSetup,
        private readonly WriterInterface $configWriter
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

        foreach (self::CONFIG_TO_UPDATE as $path => $value) {
            $this->configWriter->save($path, $value);
        }

        $connection->endSetup();
    }
}

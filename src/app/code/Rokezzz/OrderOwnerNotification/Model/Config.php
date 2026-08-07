<?php

declare(strict_types=1);

namespace Rokezzz\OrderOwnerNotification\Model;

use Magento\Framework\App\Config\ScopeConfigInterface;
use Magento\Store\Model\ScopeInterface;

final class Config
{
    private const XML_PATH_ENABLED = 'rokezzz_order_owner_notification/general/enabled';
    private const XML_PATH_RECIPIENT_EMAIL = 'rokezzz_order_owner_notification/general/recipient_email';
    private const XML_PATH_EMAIL_TEMPLATE = 'rokezzz_order_owner_notification/general/email_template';
    private const XML_PATH_GENERAL_CONTACT_EMAIL = 'trans_email/ident_general/email';

    public function __construct(
        private readonly ScopeConfigInterface $scopeConfig
    ) {
    }

    public function isEnabled(?int $storeId = null): bool
    {
        return $this->scopeConfig->isSetFlag(
            self::XML_PATH_ENABLED,
            ScopeInterface::SCOPE_STORE,
            $storeId
        );
    }

    public function getRecipientEmail(?int $storeId = null): string
    {
        $recipientEmail = trim((string) $this->scopeConfig->getValue(
            self::XML_PATH_RECIPIENT_EMAIL,
            ScopeInterface::SCOPE_STORE,
            $storeId
        ));

        if ($recipientEmail !== '') {
            return $recipientEmail;
        }

        return trim((string) $this->scopeConfig->getValue(
            self::XML_PATH_GENERAL_CONTACT_EMAIL,
            ScopeInterface::SCOPE_STORE,
            $storeId
        ));
    }

    public function getEmailTemplate(?int $storeId = null): string
    {
        $template = trim((string) $this->scopeConfig->getValue(
            self::XML_PATH_EMAIL_TEMPLATE,
            ScopeInterface::SCOPE_STORE,
            $storeId
        ));

        return $template !== '' ? $template : 'rokezzz_order_owner_notification_general_email_template';
    }
}

<?php

declare(strict_types=1);

namespace Rokezzz\CustomerNip\Plugin;

use Magento\Customer\Api\Data\CustomerInterface;
use Magento\Customer\Model\AccountManagement;
use Magento\Framework\Exception\InputException;
use Rokezzz\CustomerNip\Model\NipValidator;

final class ValidateCustomerNipOnCreateAccountPlugin
{
    public function __construct(
        private readonly NipValidator $nipValidator
    ) {
    }

    /**
     * @throws InputException
     */
    public function beforeCreateAccount(
        AccountManagement $_subject,
        CustomerInterface $customer,
        $password = null,
        $redirectUrl = ''
    ): array {
        $taxvat = trim((string) $customer->getTaxvat());

        if ($taxvat !== '' && !$this->nipValidator->isValid($taxvat)) {
            throw new InputException(__('Please provide a valid Polish NIP number.'));
        }

        return [$customer, $password, $redirectUrl];
    }
}

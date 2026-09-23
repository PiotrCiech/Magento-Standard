<?php

declare(strict_types=1);

namespace Rokezzz\InPost\Block\Adminhtml\System\Config\Form\Field;

use Magento\Backend\Block\Template\Context;
use Magento\Config\Block\System\Config\Form\Field;
use Magento\Framework\Data\Form\Element\AbstractElement;
use Rokezzz\InPost\Model\IntegrationStatus;

class ModuleStatus extends Field
{
    public function __construct(
        Context $context,
        private readonly IntegrationStatus $integrationStatus,
        array $data = []
    ) {
        parent::__construct($context, $data);
    }

    protected function _getElementHtml(AbstractElement $element): string
    {
        if (!$this->integrationStatus->isOfficialModuleInstalled()) {
            return (string) __(
                'Official module Smartmage_Inpost is not installed. Run: composer require smartmage/inpost'
            );
        }

        if (!$this->integrationStatus->isOfficialModuleEnabled()) {
            return (string) __(
                'Official module Smartmage_Inpost is installed but disabled. Enable it with bin/magento module:enable.'
            );
        }

        return (string) __('Official module Smartmage_Inpost is installed and enabled.');
    }
}

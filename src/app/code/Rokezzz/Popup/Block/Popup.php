<?php
namespace Rokezzz\Popup\Block;

use Magento\Framework\View\Element\Template;
use Magento\Framework\App\Config\ScopeConfigInterface;
use Magento\Store\Model\ScopeInterface;

class Popup extends Template
{
    private const XML_PATH_POPUP_ENABLE = 'rokezzz_settings/popup/enable';
    private const XML_PATH_POPUP_BLOCK = 'rokezzz_settings/popup/cms_block';
    private const XML_PATH_POPUP_DELAY = 'rokezzz_settings/popup/delay_time';

    protected $scopeConfig;

    public function __construct(
        Template\Context $context,
        ScopeConfigInterface $scopeConfig,
        array $data = []
    ) {
        $this->scopeConfig = $scopeConfig;
        parent::__construct($context, $data);
    }

    public function isEnabled(): bool
    {
        return $this->scopeConfig->isSetFlag(self::XML_PATH_POPUP_ENABLE, ScopeInterface::SCOPE_STORE);
    }

    public function getCmsBlockId(): ?string
    {
        return $this->scopeConfig->getValue(self::XML_PATH_POPUP_BLOCK, ScopeInterface::SCOPE_STORE);
    }

    /**
     * * @return int
     */
    public function getDelayTime(): int
    {
        $delay = $this->scopeConfig->getValue(self::XML_PATH_POPUP_DELAY, ScopeInterface::SCOPE_STORE);
        return $delay ? (int) $delay : 0;
    }
}

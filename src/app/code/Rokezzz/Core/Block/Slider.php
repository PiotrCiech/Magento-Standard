<?php
namespace Rokezzz\Core\Block;

use Magento\Framework\View\Element\Template;
use Magento\Framework\Serialize\Serializer\Json;

class Slider extends Template
{
    protected $serializer;

    public function __construct(
        Template\Context $context,
        Json $serializer,
        array $data = []
    ) {
        $this->serializer = $serializer;
        parent::__construct($context, $data);
    }

    public function isEnabled()
    {
        return $this->_scopeConfig->getValue('rokezzz_settings/slider/enable', \Magento\Store\Model\ScopeInterface::SCOPE_STORE);
    }
    public function getSlides()
    {
        $slidesJson = $this->_scopeConfig->getValue('rokezzz_settings/slider/slides', \Magento\Store\Model\ScopeInterface::SCOPE_STORE);

        if (!$slidesJson) {
            return [];
        }

        try {
            $slides = $this->serializer->unserialize($slidesJson);

            usort($slides, function ($a, $b) {
                return ($a['sort_order'] ?? 0) <=> ($b['sort_order'] ?? 0);
            });

            return $slides;
        } catch (\Exception $e) {
            return [];
        }
    }
}

<?php

namespace Furgonetka\Pickups\Block\Checkout;

class Scripts extends \Magento\Checkout\Block\Onepage
{

    /**
     * @var \Furgonetka\Pickups\Helper\HelperFunctions
     */
    protected $helperFunctions;

    /**
     * @param \Magento\Framework\View\Element\Template\Context $context
     * @param \Magento\Framework\Data\Form\FormKey             $formKey
     * @param \Magento\Checkout\Model\CompositeConfigProvider  $configProvider
     * @param \Furgonetka\Biznes\Helper\Helper                 $fBizHelper
     * @param array                                            $layoutProcessors
     * @param array                                            $data
     */
    public function __construct(
        \Magento\Framework\View\Element\Template\Context $context,
        \Magento\Framework\Data\Form\FormKey $formKey,
        \Magento\Checkout\Model\CompositeConfigProvider $configProvider,
        \Furgonetka\Pickups\Helper\HelperFunctions $helperFunctions,
        array $layoutProcessors = [],
        array $data = []
    ) {
        $this->helperFunctions = $helperFunctions;
        parent::__construct($context, $formKey, $configProvider, $layoutProcessors, $data);
    }

    /**
     * return array
     */
    public function getPoints()
    {
        $serviceTypes = $this->helperFunctions->getConfig(\Furgonetka\Pickups\Helper\Data::POINTS);
        return explode(',', $serviceTypes);
    }


}

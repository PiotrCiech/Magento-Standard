<?php

namespace Furgonetka\Pickups\Plugins\Checkout;

class ShippingInformationManagementPlugin
{

    /**
     *
     * @var \Magento\Quote\Model\QuoteRepository
     */
    protected $quoteRepository;

    /**
     *
     * @var \Psr\Log\LoggerInterface
     */
    protected $_logger;

    /**
     * @param \Magento\Quote\Model\QuoteRepository $quoteRepository
     * @param \Psr\Log\LoggerInterface             $logger
     */
    public function __construct(
        \Magento\Quote\Model\QuoteRepository $quoteRepository,
        \Psr\Log\LoggerInterface $logger
    ) {
        $this->quoteRepository = $quoteRepository;
        $this->_logger = $logger;
    }

    /**
     * @param \Magento\Checkout\Model\ShippingInformationManagement   $subject
     * @param                                                         $cartId
     * @param \Magento\Checkout\Api\Data\ShippingInformationInterface $addressInformation
     */
    public function beforeSaveAddressInformation(
        \Magento\Checkout\Model\ShippingInformationManagement $subject,
        $cartId,
        \Magento\Checkout\Api\Data\ShippingInformationInterface $addressInformation
    ) {
        $extAttributes = $addressInformation->getExtensionAttributes();
        $quote = $this->quoteRepository->getActive($cartId);

        if (!is_null($extAttributes) && ($extAttributes->getFurgonetkaPickupPoint())) {
            $quote->setFurgonetkaPickupPoint($extAttributes->getFurgonetkaPickupPoint());
        }
    }

}

<?php

namespace Furgonetka\Pickups\Observer;

class SalesModelServiceQuoteSubmitBefore implements \Magento\Framework\Event\ObserverInterface
{
    /**
     * @var \Magento\Framework\ObjectManagerInterface
     */
    protected $_objectManager;

    /**
     * @var \Psr\Log\LoggerInterface
     */
    protected $_logger;

    /**
     * @param \Magento\Framework\ObjectManagerInterface $objectmanager
     * @param \Psr\Log\LoggerInterface                  $logger
     */
    public function __construct(
        \Magento\Framework\ObjectManagerInterface $objectmanager,
        \Psr\Log\LoggerInterface $logger
    ) {
        $this->_objectManager = $objectmanager;
        $this->_logger = $logger;
    }

    /**
     * @param \Magento\Framework\Event\Observer $observer
     *
     * @return \Furgonetka\Biznes\Model\Observer\SalesModelServiceQuoteSubmitBefore
     */
    public function execute(\Magento\Framework\Event\Observer $observer)
    {
        $order = $observer->getOrder();
        $quoteRepository = $this->_objectManager->create('Magento\Quote\Model\QuoteRepository');
        $quote = $quoteRepository->get($order->getQuoteId());
        if ($quote->getFurgonetkaPickupPoint()) {
            $order->setFurgonetkaPickupPoint($quote->getFurgonetkaPickupPoint());
        }


        return $this;
    }

}
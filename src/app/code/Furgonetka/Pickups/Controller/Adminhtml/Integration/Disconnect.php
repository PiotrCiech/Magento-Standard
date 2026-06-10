<?php

namespace Furgonetka\Pickups\Controller\Adminhtml\Integration;

use Furgonetka\Pickups\Helper\HelperFunctions;
use Magento\Config\Model\ResourceModel\Config\Data;
use Magento\Config\Model\ResourceModel\Config\Data\CollectionFactory;

class Disconnect extends \Magento\Backend\App\Action
{

    /**
     * @var CollectionFactory
     */
    protected $collectionFactory;
    /**
     * @var Data
     */
    protected $configResource;

    protected $_resultPageFactory;
    protected $helperFunctions;

    public function __construct(
        \Magento\Backend\App\Action\Context $context,
        \Magento\Framework\View\Result\PageFactory $resultPageFactory,
        CollectionFactory $collectionFactory,
        Data $configResource,
        HelperFunctions $helperFunctions
    ) {
        $this->_resultPageFactory = $resultPageFactory;
        $this->collectionFactory = $collectionFactory;
        $this->configResource = $configResource;
        $this->helperFunctions = $helperFunctions;
        parent::__construct($context);
    }

    /**
     *
     */
    public function execute()
    {
        try {
            $collection = $this->collectionFactory->create()
                ->addPathFilter('furgonetka_pickups');
            foreach ($collection as $config) {
                $this->configResource->delete($config);
            }
            $this->helperFunctions->clearCache(['config']);
            $this->messageManager->addSuccess(__('Integracja z furgonetka.pl została odłączona'));
        } catch (\Exception $exception) {
            $this->messageManager->addError($exception->getMessage());
        }
        $this->_redirect($this->_redirect->getRefererUrl());
    }

}

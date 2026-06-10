<?php

namespace Furgonetka\Pickups\Controller\Adminhtml\Package;

class Iframe extends \Magento\Backend\App\Action
{


    protected $orderRepository;
    protected $furgonetkaApi;
    /**
     * @var \Magento\Framework\Controller\Result\JsonFactory
     */
    private $jsonResultFactory;

    /**
     * @param \Magento\Backend\App\Action\Context        $context
     * @param \Magento\Framework\View\Result\PageFactory $resultPageFactory
     * @param \Furgonetka\Biznes\Helper\Helper           $fBizHelper
     */
    public function __construct(
        \Magento\Backend\App\Action\Context $context,
        \Magento\Sales\Api\OrderRepositoryInterface $orderRepository,
        \Furgonetka\Pickups\Helper\FurgonetkaApi $furgonetkaApi,
        \Magento\Framework\Controller\Result\JsonFactory $jsonResultFactory
    ) {
        $this->orderRepository = $orderRepository;
        $this->furgonetkaApi = $furgonetkaApi;
        $this->jsonResultFactory = $jsonResultFactory;
        parent::__construct($context);
    }

    /**
     * @return \Magento\Framework\View\Result\Page
     */
    public function execute()
    {
        $order = $this->orderRepository->get($this->getRequest()->getParam('order_id'));
        $result = $this->jsonResultFactory->create();
        $result->setData(['link' => $this->furgonetkaApi->getIframeLink($order)]);
        return $result;
    }

}

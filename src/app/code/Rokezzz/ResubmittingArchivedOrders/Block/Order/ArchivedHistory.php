<?php

declare(strict_types=1);

namespace Rokezzz\ResubmittingArchivedOrders\Block\Order;

use Magento\Customer\Model\Session as CustomerSession;
use Magento\Framework\View\Element\Template;
use Magento\Framework\View\Element\Template\Context;
use Magento\Sales\Model\Order\Config as OrderConfig;
use Magento\Sales\Model\ResourceModel\Order\Collection;
use Magento\Sales\Model\ResourceModel\Order\CollectionFactory as OrderCollectionFactory;
use Rokezzz\ResubmittingArchivedOrders\Model\Config;

class ArchivedHistory extends Template
{
    private ?Collection $orders = null;

    public function __construct(
        Context $context,
        private readonly OrderCollectionFactory $orderCollectionFactory,
        private readonly CustomerSession $customerSession,
        private readonly OrderConfig $orderConfig,
        private readonly Config $config,
        array $data = []
    ) {
        parent::__construct($context, $data);
    }

    protected function _construct(): void
    {
        parent::_construct();
        $this->pageConfig->getTitle()->set(__('Archived Orders'));
    }

    public function isFeatureEnabled(): bool
    {
        return $this->config->isEnabled();
    }

    /**
     * @return Collection|false
     */
    public function getOrders()
    {
        if (!$this->isFeatureEnabled()) {
            return false;
        }

        $customerId = (int) $this->customerSession->getCustomerId();

        if ($customerId === 0) {
            return false;
        }

        if ($this->orders === null) {
            $this->orders = $this->orderCollectionFactory->create($customerId)
                ->addFieldToSelect('*')
                ->addFieldToFilter('rokezzz_is_archived', ['eq' => 1])
                ->addFieldToFilter('status', ['in' => $this->orderConfig->getVisibleOnFrontStatuses()])
                ->setOrder('created_at', 'desc');
        }

        return $this->orders;
    }

    protected function _prepareLayout()
    {
        parent::_prepareLayout();

        $orders = $this->getOrders();

        if ($orders) {
            $pager = $this->getLayout()->createBlock(
                \Magento\Theme\Block\Html\Pager::class,
                'rokezzz.archived.order.history.pager'
            )->setCollection($orders);
            $this->setChild('pager', $pager);
            $orders->load();
        }

        return $this;
    }

    public function getPagerHtml(): string
    {
        return (string) $this->getChildHtml('pager');
    }

    public function getResubmitUrl(int $orderId): string
    {
        return $this->getUrl('rokezzz_archived_order/order/resubmit', ['order_id' => $orderId]);
    }

    public function getBackUrl(): string
    {
        return $this->getUrl('sales/order/history');
    }

    public function getEmptyOrdersMessage(): \Magento\Framework\Phrase
    {
        return __('You have no archived orders.');
    }
}

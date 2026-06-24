<?php

declare(strict_types=1);

namespace Rokezzz\PromotionSlider\Controller\Adminhtml\Slide;

use Magento\Backend\App\Action;
use Magento\Framework\App\Action\HttpPostActionInterface;
use Magento\Framework\Controller\ResultFactory;
use Magento\Ui\Component\MassAction\Filter;
use Rokezzz\PromotionSlider\Model\ResourceModel\Slide\CollectionFactory;
use Rokezzz\PromotionSlider\Model\ResourceModel\Slide as SlideResource;

class MassDelete extends Action implements HttpPostActionInterface
{
    const ADMIN_RESOURCE = 'Rokezzz_PromotionSlider::slide';

    private Filter $filter;
    private CollectionFactory $collectionFactory;
    private SlideResource $slideResource;

    public function __construct(
        Action\Context $context,
        Filter $filter,
        CollectionFactory $collectionFactory,
        SlideResource $slideResource
    ) {
        parent::__construct($context);
        $this->filter = $filter;
        $this->collectionFactory = $collectionFactory;
        $this->slideResource = $slideResource;
    }

    public function execute()
    {
        $collection = $this->filter->getCollection($this->collectionFactory->create());
        $count = 0;

        foreach ($collection as $slide) {
            $this->slideResource->delete($slide);
            $count++;
        }

        $this->messageManager->addSuccessMessage(__('A total of %1 slide(s) have been deleted.', $count));

        return $this->resultFactory->create(ResultFactory::TYPE_REDIRECT)->setPath('*/*/');
    }
}

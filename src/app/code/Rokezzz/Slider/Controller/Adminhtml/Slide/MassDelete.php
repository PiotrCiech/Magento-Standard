<?php

declare(strict_types=1);

namespace Rokezzz\Slider\Controller\Adminhtml\Slide;

use Magento\Backend\App\Action;
use Magento\Framework\App\Action\HttpPostActionInterface;
use Magento\Framework\Controller\ResultFactory;
use Magento\Framework\Controller\Result\Redirect;
use Magento\Ui\Component\MassAction\Filter;
use Rokezzz\Slider\Model\ResourceModel\Slide as SlideResource;
use Rokezzz\Slider\Model\ResourceModel\Slide\CollectionCreator;

class MassDelete extends Action implements HttpPostActionInterface
{
    const ADMIN_RESOURCE = 'Rokezzz_Slider::slide';

    private Filter $filter;
    private CollectionCreator $collectionCreator;
    private SlideResource $slideResource;

    public function __construct(
        Action\Context $context,
        Filter $filter,
        CollectionCreator $collectionCreator,
        SlideResource $slideResource
    ) {
        parent::__construct($context);
        $this->filter = $filter;
        $this->collectionCreator = $collectionCreator;
        $this->slideResource = $slideResource;
    }

    public function execute()
    {
        $collection = $this->filter->getCollection($this->collectionCreator->create());
        $count = 0;

        foreach ($collection as $slide) {
            $this->slideResource->delete($slide);
            $count++;
        }

        $this->messageManager->addSuccessMessage(__('A total of %1 slide(s) have been deleted.', $count));

        /** @var Redirect $resultRedirect */
        $resultRedirect = $this->resultFactory->create(ResultFactory::TYPE_REDIRECT);

        return $resultRedirect->setPath('*/*/');
    }
}

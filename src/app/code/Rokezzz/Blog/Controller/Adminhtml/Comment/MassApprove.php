<?php

declare(strict_types=1);

namespace Rokezzz\Blog\Controller\Adminhtml\Comment;

use Magento\Backend\App\Action;
use Magento\Backend\App\Action\Context;
use Magento\Framework\App\Action\HttpPostActionInterface;
use Magento\Framework\Controller\Result\Redirect;
use Magento\Framework\Controller\ResultFactory;
use Magento\Ui\Component\MassAction\Filter;
use Rokezzz\Blog\Api\Data\CommentInterface;
use Rokezzz\Blog\Model\ResourceModel\Comment\CollectionCreator;
use Rokezzz\Blog\Model\ResourceModel\CommentResource;

class MassApprove extends Action implements HttpPostActionInterface
{
    public const ADMIN_RESOURCE = 'Rokezzz_Blog::comments';

    public function __construct(
        Context $context,
        private readonly Filter $filter,
        private readonly CollectionCreator $collectionCreator,
        private readonly CommentResource $commentResource
    ) {
        parent::__construct($context);
    }

    public function execute(): Redirect
    {
        $collection = $this->filter->getCollection($this->collectionCreator->create());
        $count = 0;

        foreach ($collection as $comment) {
            $comment->setStatus(CommentInterface::STATUS_APPROVED);
            $this->commentResource->save($comment);
            $count++;
        }

        $this->messageManager->addSuccessMessage(__('A total of %1 comment(s) have been approved.', $count));

        /** @var Redirect $resultRedirect */
        $resultRedirect = $this->resultFactory->create(ResultFactory::TYPE_REDIRECT);

        return $resultRedirect->setPath('*/*/');
    }
}

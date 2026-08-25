<?php

declare(strict_types=1);

namespace Rokezzz\Blog\Controller\Adminhtml\Comment;

use Magento\Backend\App\Action;
use Magento\Framework\App\Action\HttpPostActionInterface;
use Magento\Framework\Controller\Result\Redirect;
use Magento\Framework\Controller\ResultFactory;
use Magento\Ui\Component\MassAction\Filter;
use Rokezzz\Blog\Model\Comment;
use Rokezzz\Blog\Model\ResourceModel\Comment as CommentResource;
use Rokezzz\Blog\Model\ResourceModel\Comment\CollectionFactory;

class MassApprove extends Action implements HttpPostActionInterface
{
    public const ADMIN_RESOURCE = 'Rokezzz_Blog::comments';

    public function __construct(
        Action\Context $context,
        private readonly Filter $filter,
        private readonly CollectionFactory $collectionFactory,
        private readonly CommentResource $commentResource
    ) {
        parent::__construct($context);
    }

    public function execute()
    {
        $collection = $this->filter->getCollection($this->collectionFactory->create());
        $count = 0;

        foreach ($collection as $comment) {
            $comment->setData('status_id', Comment::STATUS_APPROVED);
            $this->commentResource->save($comment);
            $count++;
        }

        $this->messageManager->addSuccessMessage(__('A total of %1 comment(s) have been approved.', $count));

        /** @var Redirect $resultRedirect */
        $resultRedirect = $this->resultFactory->create(ResultFactory::TYPE_REDIRECT);

        return $resultRedirect->setPath('*/*/');
    }
}

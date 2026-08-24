<?php

declare(strict_types=1);

namespace Rokezzz\Blog\Controller\Adminhtml\Comment;

use Exception;
use Magento\Backend\App\Action;
use Magento\Framework\App\Action\HttpPostActionInterface;
use Rokezzz\Blog\Api\Data\CommentInterface;
use Rokezzz\Blog\Model\CommentCreator;
use Rokezzz\Blog\Model\ResourceModel\CommentResource;

class Reject extends Action implements HttpPostActionInterface
{
    public const ADMIN_RESOURCE = 'Rokezzz_Blog::comments';

    public function __construct(
        Action\Context $context,
        private readonly CommentCreator $commentCreator,
        private readonly CommentResource $commentResource
    ) {
        parent::__construct($context);
    }

    public function execute()
    {
        $commentId = (int) $this->getRequest()->getParam('comment_id');
        $resultRedirect = $this->resultRedirectFactory->create();

        if ($commentId <= 0) {
            $this->messageManager->addErrorMessage(__('We can\'t find a comment to reject.'));

            return $resultRedirect->setPath('*/*/');
        }

        try {
            $comment = $this->commentCreator->create();
            $this->commentResource->load($comment, $commentId);
            if (!$comment->getId()) {
                $this->messageManager->addErrorMessage(__('This comment no longer exists.'));

                return $resultRedirect->setPath('*/*/');
            }

            $comment->setStatus(CommentInterface::STATUS_REJECTED);
            $this->commentResource->save($comment);
            $this->messageManager->addSuccessMessage(__('The comment has been rejected.'));
        } catch (Exception $exception) {
            $this->messageManager->addErrorMessage($exception->getMessage());
        }

        return $resultRedirect->setPath('*/*/');
    }
}

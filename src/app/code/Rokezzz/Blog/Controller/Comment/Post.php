<?php

declare(strict_types=1);

namespace Rokezzz\Blog\Controller\Comment;

use Exception;
use Magento\Framework\App\Action\HttpPostActionInterface;
use Magento\Framework\App\RequestInterface;
use Magento\Framework\Controller\Result\RedirectFactory;
use Magento\Framework\Data\Form\FormKey\Validator as FormKeyValidator;
use Magento\Framework\Message\ManagerInterface as MessageManager;
use Rokezzz\Blog\Api\Data\CommentInterface;
use Rokezzz\Blog\Model\CommentCreator;
use Rokezzz\Blog\Model\ResourceModel\CommentResource;
use Rokezzz\Blog\Model\ResourceModel\Post\CollectionCreator;

class Post implements HttpPostActionInterface
{
    public function __construct(
        private readonly RequestInterface $request,
        private readonly RedirectFactory $redirectFactory,
        private readonly FormKeyValidator $formKeyValidator,
        private readonly MessageManager $messageManager,
        private readonly CommentCreator $commentCreator,
        private readonly CommentResource $commentResource,
        private readonly CollectionCreator $postCollectionCreator
    ) {
    }

    public function execute()
    {
        $resultRedirect = $this->redirectFactory->create();
        $identifier = trim((string) $this->request->getParam('identifier'), '/');
        $backUrl = $identifier ? 'blog/' . $identifier : 'blog';

        if (!$this->formKeyValidator->validate($this->request)) {
            $this->messageManager->addErrorMessage(__('We can\'t post your comment right now.'));

            return $resultRedirect->setPath($backUrl);
        }

        $postCollection = $this->postCollectionCreator->create();
        $postCollection->addFieldToFilter('identifier', $identifier)
            ->addFieldToFilter('is_active', 1);
        $post = $postCollection->getFirstItem();

        if (!$post->getId() || !$post->getData('allow_comments')) {
            $this->messageManager->addErrorMessage(__('We can\'t post your comment right now.'));

            return $resultRedirect->setPath($backUrl);
        }

        $authorName = trim((string) $this->request->getParam('author_name'));
        $authorEmail = trim((string) $this->request->getParam('author_email'));
        $content = trim((string) $this->request->getParam('content'));

        if ($authorName === '' || $authorEmail === '' || $content === '' || !filter_var($authorEmail, FILTER_VALIDATE_EMAIL)) {
            $this->messageManager->addErrorMessage(__('Please fill in your name, a valid email and a comment.'));

            return $resultRedirect->setPath($backUrl);
        }

        try {
            $comment = $this->commentCreator->create();
            $comment->setPostId((int) $post->getId())
                ->setAuthorName($authorName)
                ->setAuthorEmail($authorEmail)
                ->setContent($content)
                ->setStatus(CommentInterface::STATUS_PENDING);
            $this->commentResource->save($comment);

            $this->messageManager->addSuccessMessage(
                __('Your comment has been submitted and is awaiting approval.')
            );
        } catch (Exception $exception) {
            $this->messageManager->addErrorMessage(__('We can\'t post your comment right now.'));
        }

        return $resultRedirect->setPath($backUrl);
    }
}

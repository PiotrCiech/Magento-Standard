<?php

declare(strict_types=1);

namespace Rokezzz\Blog\Controller\Comment;

use Magento\Framework\App\Action\HttpPostActionInterface;
use Magento\Framework\App\RequestInterface;
use Magento\Framework\Controller\Result\RedirectFactory;
use Magento\Framework\Data\Form\FormKey\Validator as FormKeyValidator;
use Magento\Framework\Message\ManagerInterface as MessageManager;
use Rokezzz\Blog\Model\Comment;
use Rokezzz\Blog\Model\CommentFactory;
use Rokezzz\Blog\Model\ResourceModel\Comment as CommentResource;
use Rokezzz\Blog\Model\ResourceModel\Post\CollectionFactory as PostCollectionFactory;

class Post implements HttpPostActionInterface
{
    public function __construct(
        private readonly RequestInterface $request,
        private readonly RedirectFactory $redirectFactory,
        private readonly FormKeyValidator $formKeyValidator,
        private readonly MessageManager $messageManager,
        private readonly CommentFactory $commentFactory,
        private readonly CommentResource $commentResource,
        private readonly PostCollectionFactory $postCollectionFactory
    ) {
    }

    public function execute()
    {
        $resultRedirect = $this->redirectFactory->create();
        $identifier = trim((string)$this->request->getParam('identifier'), '/');
        $backUrl = $identifier ? 'blog/' . $identifier : 'blog';

        if (!$this->formKeyValidator->validate($this->request)) {
            $this->messageManager->addErrorMessage(__('We can\'t post your comment right now.'));
            return $resultRedirect->setPath($backUrl);
        }

        $postCollection = $this->postCollectionFactory->create();
        $postCollection->addFieldToFilter('identifier', $identifier)
            ->addFieldToFilter('is_active', 1);
        $post = $postCollection->getFirstItem();

        if (!$post->getId() || !$post->getData('allow_comments')) {
            $this->messageManager->addErrorMessage(__('We can\'t post your comment right now.'));
            return $resultRedirect->setPath($backUrl);
        }

        $nickname = trim((string)$this->request->getParam('nickname'));
        $email = trim((string)$this->request->getParam('email'));
        $detail = trim((string)$this->request->getParam('detail'));

        if ($nickname === '' || $email === '' || $detail === '' || !filter_var($email, FILTER_VALIDATE_EMAIL)) {
            $this->messageManager->addErrorMessage(__('Please fill in your name, a valid email and a comment.'));
            return $resultRedirect->setPath($backUrl);
        }

        try {
            /** @var Comment $comment */
            $comment = $this->commentFactory->create();
            $comment->setData('post_id', (int)$post->getId())
                ->setData('nickname', $nickname)
                ->setData('email', $email)
                ->setData('detail', $detail)
                ->setData('status_id', Comment::STATUS_PENDING);
            $this->commentResource->save($comment);

            $this->messageManager->addSuccessMessage(
                __('Your comment has been submitted and is awaiting approval.')
            );
        } catch (\Exception $e) {
            $this->messageManager->addErrorMessage(__('We can\'t post your comment right now.'));
        }

        return $resultRedirect->setPath($backUrl);
    }
}

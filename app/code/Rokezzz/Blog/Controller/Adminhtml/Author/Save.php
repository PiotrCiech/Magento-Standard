<?php

declare(strict_types=1);

namespace Rokezzz\Blog\Controller\Adminhtml\Author;

use Exception;
use Magento\Backend\App\Action;
use Magento\Backend\App\Action\Context;
use Magento\Framework\App\Action\HttpPostActionInterface;
use Magento\Framework\App\Request\DataPersistorInterface;
use Magento\Framework\App\Request\Http as HttpRequest;
use Magento\Framework\Controller\Result\Redirect;
use Rokezzz\Blog\Model\Author;
use Rokezzz\Blog\Model\Author\PostDataProcessor;
use Rokezzz\Blog\Model\AuthorCreator;
use Rokezzz\Blog\Model\ResourceModel\Author as AuthorResource;

class Save extends Action implements HttpPostActionInterface
{
    public const ADMIN_RESOURCE = 'Rokezzz_Blog::authors';

    public function __construct(
        Context $context,
        private readonly AuthorCreator $authorCreator,
        private readonly AuthorResource $authorResource,
        private readonly DataPersistorInterface $dataPersistor,
        private readonly PostDataProcessor $postDataProcessor
    ) {
        parent::__construct($context);
    }

    public function execute(): Redirect
    {
        $resultRedirect = $this->resultRedirectFactory->create();
        $data = $this->getPostData();
        if ($data === []) {
            return $resultRedirect->setPath('*/*/');
        }

        $authorId = (int) ($data['author_id'] ?? 0);
        $author = $this->loadAuthor($authorId);
        if (is_null($author)) {
            $this->messageManager->addErrorMessage(__('This author no longer exists.'));

            return $resultRedirect->setPath('*/*/');
        }

        $data = $this->postDataProcessor->prepare($data, $author);
        $author->addData($data);

        try {
            $this->authorResource->save($author);
            $this->dataPersistor->clear('rokezzz_blog_author');
            $this->messageManager->addSuccessMessage(__('The author has been saved.'));

            return $this->redirectAfterSave($resultRedirect, $author);
        } catch (Exception $exception) {
            $this->dataPersistor->set('rokezzz_blog_author', $data);
            $this->messageManager->addErrorMessage($exception->getMessage());

            return $resultRedirect->setPath('*/*/edit', ['author_id' => $authorId]);
        }
    }

    private function getPostData(): array
    {
        $request = $this->getRequest();
        if (!$request instanceof HttpRequest) {
            return [];
        }

        $data = $request->getPostValue();

        return is_array($data) ? $data : [];
    }

    private function loadAuthor(int $authorId): ?Author
    {
        $author = $this->authorCreator->create();
        if ($authorId <= 0) {
            return $author;
        }

        $this->authorResource->load($author, $authorId);
        if (is_null($author->getAuthorId())) {
            return null;
        }

        return $author;
    }

    private function redirectAfterSave(Redirect $resultRedirect, Author $author): Redirect
    {
        if ((string) $this->getRequest()->getParam('back') === 'edit') {
            return $resultRedirect->setPath('*/*/edit', ['author_id' => $author->getId()]);
        }

        return $resultRedirect->setPath('*/*/');
    }
}

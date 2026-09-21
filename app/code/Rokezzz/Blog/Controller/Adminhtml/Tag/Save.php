<?php

declare(strict_types=1);

namespace Rokezzz\Blog\Controller\Adminhtml\Tag;

use Exception;
use Magento\Backend\App\Action;
use Magento\Backend\App\Action\Context;
use Magento\Framework\App\Action\HttpPostActionInterface;
use Magento\Framework\App\Request\DataPersistorInterface;
use Magento\Framework\App\Request\Http as HttpRequest;
use Magento\Framework\Controller\Result\Redirect;
use Magento\Framework\Filter\FilterManager;
use Rokezzz\Blog\Model\ResourceModel\Tag as TagResource;
use Rokezzz\Blog\Model\TagCreator;

class Save extends Action implements HttpPostActionInterface
{
    public const ADMIN_RESOURCE = 'Rokezzz_Blog::tags';

    public function __construct(
        Context $context,
        private readonly TagCreator $tagCreator,
        private readonly TagResource $tagResource,
        private readonly DataPersistorInterface $dataPersistor,
        private readonly FilterManager $filterManager
    ) {
        parent::__construct($context);
    }

    public function execute(): Redirect
    {
        $resultRedirect = $this->resultRedirectFactory->create();
        $request = $this->getRequest();

        if (!$request instanceof HttpRequest) {
            return $resultRedirect->setPath('*/*/');
        }

        $data = $request->getPostValue();
        if (!is_array($data) || $data === []) {
            return $resultRedirect->setPath('*/*/');
        }

        unset($data['form_key']);

        $tagId = (int) ($data['tag_id'] ?? 0);
        $tag = $this->tagCreator->create();

        if ($tagId > 0) {
            $this->tagResource->load($tag, $tagId);
            if (!$tag->getId()) {
                $this->messageManager->addErrorMessage(__('This tag no longer exists.'));

                return $resultRedirect->setPath('*/*/');
            }
        }

        if (empty($data['tag_id'])) {
            unset($data['tag_id']);
        }

        if (empty($data['identifier'])) {
            if ($tagId > 0 && $tag->getIdentifier() !== '') {
                $data['identifier'] = $tag->getIdentifier();
            } elseif (!empty($data['name'])) {
                $data['identifier'] = $this->filterManager->translitUrl((string) $data['name']);
            }
        }

        if ($tagId <= 0) {
            $data['is_active'] = 1;
        }

        $tag->addData($data);

        try {
            $this->tagResource->save($tag);
            $this->dataPersistor->clear('rokezzz_blog_tag');
            $this->messageManager->addSuccessMessage(__('The tag has been saved.'));

            if ((string) $request->getParam('back') === 'edit') {
                return $resultRedirect->setPath('*/*/edit', ['tag_id' => $tag->getId()]);
            }

            return $resultRedirect->setPath('*/*/');
        } catch (Exception $exception) {
            $this->dataPersistor->set('rokezzz_blog_tag', $data);
            $this->messageManager->addErrorMessage($exception->getMessage());

            return $resultRedirect->setPath('*/*/edit', ['tag_id' => $tagId]);
        }
    }
}

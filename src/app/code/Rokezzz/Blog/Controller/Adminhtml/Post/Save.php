<?php

declare(strict_types=1);

namespace Rokezzz\Blog\Controller\Adminhtml\Post;

use Magento\Backend\App\Action;
use Magento\Backend\App\Action\Context;
use Magento\Framework\App\Request\DataPersistorInterface;
use Magento\Framework\Controller\ResultInterface;
use Magento\Framework\Exception\LocalizedException;
use Rokezzz\Blog\Model\PostFactory;
use Rokezzz\Blog\Model\ResourceModel\Post as PostResource;

class Save extends Action
{
    public const ADMIN_RESOURCE = 'Rokezzz_Blog::posts';

    private DataPersistorInterface $dataPersistor;
    private PostFactory $postFactory;
    private PostResource $postResource;

    public function __construct(
        Context $context,
        DataPersistorInterface $dataPersistor,
        PostFactory $postFactory,
        PostResource $postResource
    ) {
        parent::__construct($context);
        $this->dataPersistor = $dataPersistor;
        $this->postFactory = $postFactory;
        $this->postResource = $postResource;
    }

    public function execute(): ResultInterface
    {
        $resultRedirect = $this->resultRedirectFactory->create();
        $data = $this->getRequest()->getPostValue();

        if ($data) {
            $id = $this->getRequest()->getParam('post_id');
            $model = $this->postFactory->create();

            if ($id) {
                $this->postResource->load($model, $id);
                if (!$model->getId()) {
                    $this->messageManager->addErrorMessage(__('This post no longer exists.'));

                    return $resultRedirect->setPath('*/*/');
                }
            }

            if (isset($data['background_image'][0]['name'])) {
                $data['background_image'] = $data['background_image'][0]['name'];
            } elseif (isset($data['background_image'][0]['url'])) {
                $data['background_image'] = basename($data['background_image'][0]['url']);
            } else {
                $data['background_image'] = null;
            }

            $model->setData($data);

            try {
                $this->postResource->save($model);
                $this->messageManager->addSuccessMessage(__('You saved the post.'));
                $this->dataPersistor->clear('rokezzz_blog_post');

                if ($this->getRequest()->getParam('back')) {
                    return $resultRedirect->setPath('*/*/edit', ['post_id' => $model->getId()]);
                }

                return $resultRedirect->setPath('*/*/');
            } catch (LocalizedException $e) {
                $this->messageManager->addErrorMessage($e->getMessage());
            } catch (\Exception $e) {
                $this->messageManager->addExceptionMessage($e, __('Something went wrong while saving the post.'));
            }

            $this->dataPersistor->set('rokezzz_blog_post', $data);
            
            return $resultRedirect->setPath('*/*/edit', ['post_id' => $id]);
        }

        return $resultRedirect->setPath('*/*/');
    }
}

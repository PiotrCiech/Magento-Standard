<?php
declare(strict_types=1);

namespace Rokezzz\Blog\Controller\Adminhtml\Post;

use Magento\Backend\App\Action;
use Magento\Backend\App\Action\Context;
use Magento\Framework\App\Request\DataPersistorInterface;
use Magento\Framework\Controller\ResultInterface;
use Magento\Framework\Exception\LocalizedException;
use Exception;
use Rokezzz\Blog\Model\PostCreator;
use Rokezzz\Blog\Model\ResourceModel\PostResource;

class Save extends Action
{
    public const ADMIN_RESOURCE = 'Rokezzz_Blog::posts';

    public function __construct(
        Context $context,
        private readonly DataPersistorInterface $dataPersistor,
        private readonly PostCreator $postCreator,
        private readonly PostResource $postResource
    ) {
        parent::__construct($context);
    }

    public function execute(): ResultInterface
    {
        $resultRedirect = $this->resultRedirectFactory->create();
        $data = $this->getRequest()->getPostValue();

        if ($data) {
            $id = $this->getRequest()->getParam('post_id');
            $model = $this->postCreator->create();

            if ($id) {
                $this->postResource->load($model, $id);
                if (!$model->getId()) {
                    $this->messageManager->addErrorMessage(__('This post no longer exists.'));
                    return $resultRedirect->setPath('*/*/');
                }
            }

            if (!empty($data['background_image']) && is_array($data['background_image'])) {
                if (!empty($data['background_image'][0]['name'])) {
                    $data['background_image'] = $data['background_image'][0]['name'];
                } elseif (!empty($data['background_image'][0]['url'])) {
                    // Wyciągamy ścieżkę względną od katalogu /media/
                    $url = $data['background_image'][0]['url'];
                    $mediaPos = strpos($url, '/media/');
                    if ($mediaPos !== false) {
                        $data['background_image'] = substr($url, $mediaPos + 7);
                    } else {
                        $data['background_image'] = basename($url);
                    }
                }
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
            } catch (Exception $e) {
                $this->messageManager->addExceptionMessage($e, __('Something went wrong while saving the post.'));
            }

            $this->dataPersistor->set('rokezzz_blog_post', $data);

            return $resultRedirect->setPath('*/*/edit', ['post_id' => $id]);
        }

        return $resultRedirect->setPath('*/*/');
    }
}

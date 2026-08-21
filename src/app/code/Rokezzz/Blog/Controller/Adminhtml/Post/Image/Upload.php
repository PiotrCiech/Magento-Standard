<?php

declare(strict_types=1);

namespace Rokezzz\Blog\Controller\Adminhtml\Post\Image;

use Magento\Backend\App\Action;
use Magento\Backend\App\Action\Context;
use Magento\Framework\Controller\Result\Json;
use Magento\Framework\Controller\ResultFactory;
use Magento\Framework\Controller\ResultInterface;
use Rokezzz\Blog\Model\ImageUploader;

class Upload extends Action
{
    public const ADMIN_RESOURCE = 'Rokezzz_Blog::posts';

    private ImageUploader $imageUploader;

    public function __construct(
        Context $context,
        ImageUploader $imageUploader
    ) {
        parent::__construct($context);
        $this->imageUploader = $imageUploader;
    }

    public function execute(): ResultInterface
    {
        /** @var Json $jsonResult */
        $jsonResult = $this->resultFactory->create(ResultFactory::TYPE_JSON);

        try {
            $result = $this->imageUploader->saveFileToTmpDir('background_image');
        } catch (\Exception $e) {
            $result = [
                'error' => $e->getMessage(),
                'errorcode' => (int) $e->getCode()
            ];
        }

        return $jsonResult->setData($result);
    }
}

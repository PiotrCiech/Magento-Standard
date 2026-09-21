<?php

declare(strict_types=1);

namespace Rokezzz\Blog\Controller\Adminhtml\Author\Image;

use Exception;
use Magento\Backend\App\Action;
use Magento\Backend\App\Action\Context;
use Magento\Framework\Controller\Result\Json;
use Magento\Framework\Controller\ResultFactory;
use Magento\Framework\Controller\ResultInterface;
use Rokezzz\Blog\Model\ImageUploader;

class Upload extends Action
{
    public const ADMIN_RESOURCE = 'Rokezzz_Blog::authors';

    public function __construct(
        Context $context,
        private readonly ImageUploader $imageUploader
    ) {
        parent::__construct($context);
    }

    public function execute(): ResultInterface
    {
        /** @var Json $jsonResult */
        $jsonResult = $this->resultFactory->create(ResultFactory::TYPE_JSON);

        try {
            $result = $this->imageUploader->saveFileToTmpDir('avatar');
        } catch (Exception $exception) {
            $result = [
                'error' => $exception->getMessage(),
                'errorcode' => (int) $exception->getCode(),
            ];
        }

        return $jsonResult->setData($result);
    }
}

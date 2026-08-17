<?php

declare(strict_types=1);

namespace Rokezzz\Slider\Controller\Adminhtml\Slide;

use Exception;
use Magento\Backend\App\Action;
use Magento\Framework\App\Action\HttpPostActionInterface;
use Magento\Framework\App\Filesystem\DirectoryList;
use Magento\Framework\Controller\ResultFactory;
use Magento\Framework\Controller\Result\Json;
use Magento\Framework\Filesystem;
use Magento\Framework\UrlInterface;
use Rokezzz\Slider\Model\UploaderCreator;

class Upload extends Action implements HttpPostActionInterface
{
    const ADMIN_RESOURCE = 'Rokezzz_Slider::slide';

    private UploaderCreator $uploaderCreator;
    private Filesystem $filesystem;

    public function __construct(
        Action\Context $context,
        UploaderCreator $uploaderCreator,
        Filesystem $filesystem
    ) {
        parent::__construct($context);
        $this->uploaderCreator = $uploaderCreator;
        $this->filesystem = $filesystem;
    }

    public function execute()
    {
        $result = [];

        try {
            $uploader = $this->uploaderCreator->create(['fileId' => 'image']);
            $uploader->setAllowedExtensions(['jpg', 'jpeg', 'gif', 'png', 'webp']);
            $uploader->setAllowRenameFiles(true);
            $uploader->setFilesDispersion(false);

            $mediaDirectory = $this->filesystem->getDirectoryWrite(DirectoryList::MEDIA);
            $targetPath = $mediaDirectory->getAbsolutePath('rokezzz/promotionslider');

            $result = $uploader->save($targetPath);

            $result['url'] = $this->_url->getBaseUrl(['_type' => UrlInterface::URL_TYPE_MEDIA])
                . 'rokezzz/promotionslider/' . ltrim(str_replace('\\', '/', $result['file']), '/');
        } catch (Exception $e) {
            $result = ['error' => $e->getMessage(), 'errorcode' => $e->getCode()];
        }

        /** @var Json $resultJson */
        $resultJson = $this->resultFactory->create(ResultFactory::TYPE_JSON);

        return $resultJson->setData($result);
    }
}

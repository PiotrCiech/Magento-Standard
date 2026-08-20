<?php

declare(strict_types=1);

namespace Rokezzz\Blog\Model;

use Magento\Framework\App\Filesystem\DirectoryList;
use Magento\Framework\Exception\LocalizedException;
use Magento\Framework\Filesystem;
use Magento\Framework\Filesystem\Directory\WriteInterface;
use Magento\Framework\UrlInterface;
use Magento\MediaStorage\Model\File\UploaderFactory;
use Magento\Store\Model\StoreManagerInterface;

class ImageUploader
{
    private UploaderFactory $uploaderFactory;
    private WriteInterface $mediaDirectory;
    private StoreManagerInterface $storeManager;
    private string $baseTmpPath = 'blog/tmp/image';
    private string $basePath = 'blog/image';
    private array $allowedExtensions = ['jpg', 'jpeg', 'gif', 'png', 'webp'];

    public function __construct(
        UploaderFactory $uploaderFactory,
        Filesystem $filesystem,
        StoreManagerInterface $storeManager
    ) {
        $this->uploaderFactory = $uploaderFactory;
        $this->mediaDirectory = $filesystem->getDirectoryWrite(DirectoryList::MEDIA);
        $this->storeManager = $storeManager;
    }

    public function saveFileToTmpDir(string $fileId): array
    {
        $baseTmpPath = $this->baseTmpPath;
        $uploader = $this->uploaderFactory->create(['fileId' => $fileId]);
        $uploader->setAllowedExtensions($this->allowedExtensions);
        $uploader->setAllowRenameFiles(true);

        $result = $uploader->save($this->mediaDirectory->getAbsolutePath($baseTmpPath));

        if (!$result) {
            throw new LocalizedException(__('File cannot be saved to path: %1', $baseTmpPath));
        }

        $result['tmp_name'] = str_replace('\\', '/', $result['tmp_name']);
        $result['path'] = str_replace('\\', '/', $result['path']);

        /** @var \Magento\Store\Model\Store $store */
        $store = $this->storeManager->getStore();
        $result['url'] = $store->getBaseUrl(UrlInterface::URL_TYPE_MEDIA) . $baseTmpPath . '/' . $result['file'];
        $result['name'] = $result['file'];

        return $result;
    }
}
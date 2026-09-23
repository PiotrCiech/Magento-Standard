<?php

declare(strict_types=1);

namespace Rokezzz\Slider\Model;

use Magento\Framework\ObjectManagerInterface;
use Magento\MediaStorage\Model\File\Uploader;

class UploaderCreator
{
    private ObjectManagerInterface $objectManager;

    public function __construct(ObjectManagerInterface $objectManager)
    {
        $this->objectManager = $objectManager;
    }

    /**
     * @param array<string, mixed> $data
     */
    public function create(array $data = []): Uploader
    {
        return $this->objectManager->create(Uploader::class, $data);
    }
}

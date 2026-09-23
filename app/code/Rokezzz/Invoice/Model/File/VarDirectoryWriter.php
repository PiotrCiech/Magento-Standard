<?php

declare(strict_types=1);

namespace Rokezzz\Invoice\Model\File;

use Magento\Framework\App\Filesystem\DirectoryList;
use Magento\Framework\Filesystem;
use Magento\Framework\Filesystem\Directory\WriteInterface;

final class VarDirectoryWriter
{
    public function __construct(
        private readonly Filesystem $filesystem
    ) {
    }

    public function exists(string $relativePath): bool
    {
        return $this->getWriter()->isExist($relativePath);
    }

    public function read(string $relativePath): string
    {
        return $this->getWriter()->readFile($relativePath);
    }

    public function write(string $relativePath, string $content): void
    {
        $writer = $this->getWriter();
        $this->ensureParentDirectory($writer, $relativePath);
        $writer->writeFile($relativePath, $content);
    }

    public function delete(string $relativePath): void
    {
        $writer = $this->getWriter();

        if (!$writer->isExist($relativePath)) {
            return;
        }

        $writer->delete($relativePath);
    }

    private function getWriter(): WriteInterface
    {
        return $this->filesystem->getDirectoryWrite(DirectoryList::VAR_DIR);
    }

    private function ensureParentDirectory(WriteInterface $writer, string $relativePath): void
    {
        $directory = dirname($relativePath);

        if ($directory === '.' || $directory === '') {
            return;
        }

        if ($writer->isExist($directory)) {
            return;
        }

        $writer->create($directory);
    }
}

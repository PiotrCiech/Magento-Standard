<?php

declare(strict_types=1);

namespace Rokezzz\CmsData\Setup\Patch\Data;

use Magento\Cms\Model\BlockFactory;
use Magento\Cms\Model\BlockRepository;
use Magento\Framework\Setup\Patch\DataPatchInterface;
use Psr\Log\LoggerInterface;

class CreateHomepageBlocks implements DataPatchInterface
{
    private BlockFactory $blockFactory;
    private BlockRepository $blockRepository;
    private LoggerInterface $logger;

    public function __construct(
        BlockFactory $blockFactory,
        BlockRepository $blockRepository,
        LoggerInterface $logger
    ) {
        $this->blockFactory = $blockFactory;
        $this->blockRepository = $blockRepository;
        $this->logger = $logger;
    }

    public static function getDependencies(): array
    {
        return [];
    }

    public function getAliases(): array
    {
        return [];
    }

    public function apply(): self
    {
        $blocks = [
            [
                'title' => 'Rokezzz - About',
                'identifier' => 'rokezzz_about',
                'content' => '<div class="rokezzz-about-section">
    <h2>O nas</h2>
    <p>Treść sekcji o nas. Tutaj wpiszemy informacje o sklepie Rokezzz.</p>
</div>',
                'is_active' => 1,
                'stores' => [0]
            ],
        ];

        foreach ($blocks as $blockData) {
            try {
                $block = $this->blockFactory->create();

                $block->setData($blockData);

                $this->blockRepository->save($block);
            } catch (\Exception $e) {
                $this->logger->error(
                    sprintf('Error creating CMS block %s: %s', $blockData['identifier'], $e->getMessage())
                );
            }
        }

        return $this;
    }
}

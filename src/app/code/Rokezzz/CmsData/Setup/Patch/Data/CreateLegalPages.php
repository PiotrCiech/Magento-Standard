<?php

declare(strict_types=1);

namespace Rokezzz\CmsData\Setup\Patch\Data;

use Exception;
use Magento\Cms\Model\PageFactory;
use Magento\Cms\Model\PageRepository;
use Magento\Framework\Setup\Patch\DataPatchInterface;

class CreateLegalPages implements DataPatchInterface
{
    private PageFactory $pageFactory;
    private PageRepository $pageRepository;

    public function __construct(
        PageFactory $pageFactory,
        PageRepository $pageRepository
    ) {
        $this->pageFactory = $pageFactory;
        $this->pageRepository = $pageRepository;
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
        $pages = [
            [
                'title' => 'Regulamin',
                'identifier' => 'terms-and-conditions',
                'layout' => '1column',
                'content' => '<div class="document">
    <h1>Regulamin</h1>
    <p>Treść regulaminu / polityki prywatności / RODO...</p>

    <ol>
        <li>Punkt pierwszy...</li>
        <li>
            Punkt drugi z podpunktami:
            <ol>
                <li>Podpunkt A</li>
                <li>Podpunkt B</li>
            </ol>
        </li>
    </ol>

    <p>W razie pytań napisz do nas: <a href="mailto:kontakt@rokezzz.pl">kontakt@rokezzz.pl</a>.</p>
</div>',
            ],
            [
                'title' => 'Polityka prywatności',
                'identifier' => 'privacy-policy',
                'layout' => '1column',
                'content' => '<div class="document">
    <h1>Polityka prywatności</h1>
    <p>Treść polityki prywatności.</p>
</div>',
            ],
            [
                'title' => 'RODO',
                'identifier' => 'rodo',
                'layout' => '1column',
                'content' => '<div class="document">
    <h1>RODO</h1>
    <p>Treść informacji RODO.</p>
</div>',
            ],
        ];

        foreach ($pages as $pageData) {
            try {
                $page = $this->pageFactory->create();
                $page->setStoreId([0])
                    ->setTitle($pageData['title'])
                    ->setIdentifier($pageData['identifier'])
                    ->setContent($pageData['content'])
                    ->setPageLayout($pageData['layout'])
                    ->setIsActive(true);
                $this->pageRepository->save($page);
            } catch (Exception $e) {
                $this->logger->error(
                    sprintf('Error creating CMS page %s: %s', $pageData['identifier'], $e->getMessage())
                );
            }
        }

        return $this;
    }
}

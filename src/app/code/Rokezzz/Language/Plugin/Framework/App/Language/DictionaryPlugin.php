<?php

declare(strict_types=1);

namespace Rokezzz\Language\Plugin\Framework\App\Language;

use Magento\Framework\App\Language\Dictionary;
use Magento\Framework\Component\ComponentRegistrar;
use Magento\Framework\Component\ComponentRegistrarInterface;
use Magento\Framework\File\Csv;

final class DictionaryPlugin
{
    private const AREA_FOLDERS = [
        'adminhtml',
        'frontend',
    ];

    public function __construct(
        private readonly ComponentRegistrarInterface $componentRegistrar,
        private readonly Csv $csvReader
    ) {
    }

    /**
     * @param array<string, string> $result
     * @return array<string, string>
     */
    public function afterGetDictionary(Dictionary $subject, array $result, string $languageCode): array
    {
        if ($languageCode !== 'pl_PL') {
            return $result;
        }

        $modulePaths = $this->componentRegistrar->getPaths(ComponentRegistrar::MODULE);
        if (!isset($modulePaths['Rokezzz_Language']) || $modulePaths['Rokezzz_Language'] === '') {
            return $result;
        }
        $modulePath = $modulePaths['Rokezzz_Language'];

        foreach (self::AREA_FOLDERS as $areaFolder) {
            $filePath = $modulePath . DIRECTORY_SEPARATOR . $areaFolder . DIRECTORY_SEPARATOR
                . $languageCode . '.csv';
            if (!is_file($filePath)) {
                continue;
            }

            $rows = $this->csvReader->getData($filePath);
            foreach ($rows as $row) {
                if (!is_array($row) || count($row) < 2) {
                    continue;
                }
                $result[(string) $row[0]] = (string) $row[1];
            }
        }

        return $result;
    }
}

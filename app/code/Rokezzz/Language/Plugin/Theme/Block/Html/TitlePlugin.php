<?php

declare(strict_types=1);

namespace Rokezzz\Language\Plugin\Theme\Block\Html;

use Magento\Framework\Phrase;
use Magento\Theme\Block\Html\Title;

/**
 * Some admin controllers call setPageTitle() with a raw string (no __()).
 * Wrap those so Magento i18n can translate them.
 */
final class TitlePlugin
{
    /**
     * @param string|Phrase $title
     * @return array{0: string|Phrase}
     */
    public function beforeSetPageTitle(Title $subject, $title): array
    {
        if (is_string($title) && $title !== '') {
            return [__($title)];
        }

        return [$title];
    }
}

<?php

declare(strict_types=1);

namespace Rokezzz\Offers\Model\Quote;

use Magento\Quote\Model\Quote\Item\AbstractItem;

class QuoteRequestQuoteItemManager
{
    public const BUY_REQUEST_FLAG = 'rokezzz_quote_request';
    public const OPTION_QUOTE_REQUEST = 'rokezzz_quote_request';

    public function isQuoteRequestItem(AbstractItem $item): bool
    {
        return $this->getOptionValue($item) === '1';
    }

    public function persistMetadata(AbstractItem $item): void
    {
        $item->addOption([
            'code' => self::OPTION_QUOTE_REQUEST,
            'value' => '1',
        ]);
    }

    private function getOptionValue(AbstractItem $item): ?string
    {
        $option = $item->getOptionByCode(self::OPTION_QUOTE_REQUEST);

        if (is_null($option)) {
            return null;
        }

        $value = $option->getValue();

        return is_null($value) ? null : (string) $value;
    }
}

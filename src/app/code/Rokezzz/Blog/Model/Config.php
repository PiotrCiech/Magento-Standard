<?php

declare(strict_types=1);

namespace Rokezzz\Blog\Model;

use Magento\Framework\App\Config\ScopeConfigInterface;
use Magento\Store\Model\ScopeInterface;

class Config
{
    private const XML_PATH_ENABLE_LATEST_POSTS = 'rokezzz_blog/general/enable_latest_posts';
    private const XML_PATH_LATEST_POSTS_COUNT = 'rokezzz_blog/general/latest_posts_count';
    private const DEFAULT_LATEST_POSTS_COUNT = 3;

    public function __construct(
        private readonly ScopeConfigInterface $scopeConfig
    ) {
    }

    public function isLatestPostsEnabled(?int $storeId = null): bool
    {
        return $this->scopeConfig->isSetFlag(
            self::XML_PATH_ENABLE_LATEST_POSTS,
            ScopeInterface::SCOPE_STORE,
            $storeId
        );
    }

    public function getLatestPostsCount(?int $storeId = null): int
    {
        $count = (int) $this->scopeConfig->getValue(
            self::XML_PATH_LATEST_POSTS_COUNT,
            ScopeInterface::SCOPE_STORE,
            $storeId
        );

        return $count > 0 ? $count : self::DEFAULT_LATEST_POSTS_COUNT;
    }
}

<?php

declare(strict_types=1);

namespace Rokezzz\CatalogListing\Model;

class FragmentUrlRewriter
{
    public function rewrite(string $html, string $listingUrl, string $fragmentRoute): string
    {
        if ($html === '') {
            return $html;
        }

        $parsed = parse_url($listingUrl);
        $listingPath = $parsed['path'] ?? '/';
        $listingOrigin = ($parsed['scheme'] ?? 'http') . '://' . ($parsed['host'] ?? '');

        if (isset($parsed['port'])) {
            $listingOrigin .= ':' . $parsed['port'];
        }

        $routePattern = preg_quote($fragmentRoute, '#') . '(?:\\\\/|/)*';
        $pattern = '#https?:(?:(?:\\\\/|/)++)[^"\'\s>]*' . $routePattern . '\??([^"\'\s>]*)#';

        $rewritten = preg_replace_callback(
            $pattern,
            static function (array $matches) use ($listingOrigin, $listingPath): string {
                $query = html_entity_decode($matches[1] ?? '');
                $params = [];

                if ($query !== '') {
                    parse_str($query, $params);
                }

                unset($params['id'], $params['_']);
                $newQuery = http_build_query($params);
                $newUrl = $listingOrigin . $listingPath;

                return $newQuery === '' ? $newUrl : $newUrl . '?' . $newQuery;
            },
            $html
        );

        return is_string($rewritten) ? $rewritten : $html;
    }
}

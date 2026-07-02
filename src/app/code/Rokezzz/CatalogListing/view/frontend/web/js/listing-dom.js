define([
    'jquery',
    'mage/apply/main',
    './listing-state'
], function ($, mageApply, listingState) {
    'use strict';

    function encodeMagentoUenc(url) {
        return window.btoa(url).replace(/\+/g, '-').replace(/\//g, '_').replace(/=/g, ',');
    }

    function fixAddToCartReturnUrls(returnUrl) {
        const encodedReturnUrl = encodeMagentoUenc(returnUrl);

        $('#rokezzz-listing-root form[data-role="tocart-form"], .column.main form[data-role="tocart-form"]').each(function () {
            const $form = $(this),
                action = $form.attr('action') || '',
                uencInput = $form.find('input[name="uenc"]');

            if (uencInput.length) {
                uencInput.val(encodedReturnUrl);
            }

            if (action.indexOf('/uenc/') !== -1) {
                $form.attr('action', action.replace(/\/uenc\/[^/]+/, '/uenc/' + encodedReturnUrl));
            }
        });
    }

    function replaceSearchProducts($productsHtml) {
        const $searchResults = $productsHtml.find('.search.results'),
            $notice = $productsHtml.find('.message.notice').first();

        if ($searchResults.length) {
            $('.search.results').replaceWith($searchResults.first());
        } else if ($notice.length) {
            if ($('.search.results').length) {
                $('.search.results').replaceWith($notice);
            } else {
                $('.column.main .message.notice').replaceWith($notice);
            }
        }
    }

    function insertCategoryProducts($productsHtml) {
        const $categoryView = $('.column.main .category-view');
        
        let $target = $('.column.main .page-title-wrapper').last();

        if ($categoryView.length) {
            $target = $categoryView.last();
        }

        if ($target.length) {
            $target.after($productsHtml.children());
        } else {
            $('.column.main').append($productsHtml.children());
        }
    }

    function updateDom(response) {
        const options = listingState.getOptions(),
            $productsHtml = $('<div>').html(response.productsHtml || ''),
            $filtersHtml = $('<div>').html(response.filtersHtml || ''),
            $listingRoot = $('#rokezzz-listing-root'),
            hasProductItems = $productsHtml.find('.product-item').length > 0,
            hasEmptyMessage = $productsHtml.find('.message.info.empty, .message.notice').length > 0,
            hasSearchResults = $productsHtml.find('.search.results').length > 0;

        if (!hasProductItems && !hasEmptyMessage && !hasSearchResults) {
            return false;
        }

        if ($listingRoot.length) {
            $listingRoot.html(response.productsHtml || '');
        } else if (options.listingType === 'search') {
            replaceSearchProducts($productsHtml);
        } else {
            $(options.productsSelectors).remove();
            insertCategoryProducts($productsHtml);
        }

        if ($filtersHtml.find('.block.filter').length) {
            if ($('.sidebar-main .block.filter').length) {
                $('.sidebar-main .block.filter').replaceWith($filtersHtml.find('.block.filter').first());
            } else if ($('.sidebar-main').length) {
                $('.sidebar-main').prepend($filtersHtml.find('.block.filter').first());
            }
        } else if (response.filtersHtml === '') {
            $('.sidebar-main .block.filter').remove();
        }

        mageApply.apply();
        fixAddToCartReturnUrls(response.url || window.location.href);

        return true;
    }

    return {
        updateDom: updateDom
    };
});

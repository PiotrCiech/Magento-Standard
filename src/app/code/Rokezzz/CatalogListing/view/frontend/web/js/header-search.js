define([
    'jquery',
    './listing-state',
    './listing-navigation'
], function ($, listingState, listingNavigation) {
    'use strict';

    function ensureListingRoot() {
        if ($('#rokezzz-listing-root').length) {
            return;
        }

        const $main = $('.column.main');

        $main.children().not('.page.messages, .page-title-wrapper').addClass('rokezzz-pre-search-content').hide();
        $main.append('<div id="rokezzz-listing-root" class="rokezzz-listing-root"></div>');
    }

    function prepareSearchListing() {
        const options = listingState.getOptions();

        listingState.setOptions({
            listingType: 'search',
            fragmentUrl: options.searchFragmentUrl || options.fragmentUrl,
            categoryId: null
        });

        ensureListingRoot();
        listingNavigation.bindPopstate();
    }

    function buildSearchUrl($form) {
        const options = listingState.getOptions(),
            action = $form.attr('action') || options.searchResultUrl,
            query = $.trim($form.find('input[name="q"]').val()),
            url = new URL(action, window.location.origin);

        url.searchParams.set('q', query);
        url.searchParams.delete('p');

        return url.toString();
    }

    function performSearch($form) {
        listingState.mergeOptions();

        if (!listingState.getOptions().searchFragmentUrl) {
            $form.get(0).submit();

            return;
        }

        prepareSearchListing();

        const searchUrl = buildSearchUrl($form);

        if (!listingState.getOptions().fragmentUrl) {
            window.location.href = searchUrl;

            return;
        }

        listingNavigation.loadFromUrl(searchUrl);
    }

    function bindSearchFormOnElement(form, config) {
        if (!form || form.rokezzzAsyncSearchBound) {
            return;
        }

        listingState.mergeOptions(config || {});

        if (!listingState.getOptions().searchFragmentUrl) {
            return;
        }

        form.rokezzzAsyncSearchBound = true;

        form.addEventListener('submit', function (event) {
            const $form = $(form),
                query = $.trim($form.find('input[name="q"]').val());

            if (!query.length) {
                return;
            }

            event.preventDefault();
            event.stopImmediatePropagation();
            performSearch($form);
        }, true);
    }

    function isSearchFormElement(element) {
        if (!element) {
            return false;
        }

        if (element.nodeName && element.nodeName.toLowerCase() === 'form') {
            return element.id === 'search_mini_form' || element.getAttribute('id') === 'search_mini_form';
        }

        return element.id === 'search_mini_form';
    }

    return {
        performSearch: performSearch,
        bindSearchFormOnElement: bindSearchFormOnElement,
        isSearchFormElement: isSearchFormElement
    };
});

define([
    'jquery',
    './listing-state',
    './listing-dom'
], function ($, listingState, listingDom) {
    'use strict';

    let isPopstateBound = false;

    function isSearchResultsUrl(url) {
        try {
            return new URL(url, window.location.origin).pathname.indexOf('catalogsearch/result') !== -1;
        } catch (e) {
            return false;
        }
    }

    function buildFragmentUrl(pageUrl) {
        const options = listingState.getOptions(),
            fragment = new URL(options.fragmentUrl, window.location.origin),
            page = new URL(pageUrl, window.location.origin);

        page.searchParams.forEach(function (value, key) {
            fragment.searchParams.set(key, value);
        });

        if (options.listingType === 'category' && options.categoryId) {
            fragment.searchParams.set('id', String(options.categoryId));
        } else {
            fragment.searchParams.delete('id');
        }

        return fragment.toString();
    }

    function buildToolbarUrl(toolbar, paramName, paramValue, defaultValue) {
        const urlPaths = toolbar.options.url.split('?'),
            baseUrl = urlPaths[0];

        let paramData = toolbar.getUrlParams(),
            currentPage = toolbar.getCurrentPage(),
            newPage;

        if (currentPage > 1 && paramName === toolbar.options.mode) {
            delete paramData[toolbar.options.page];
        }

        if (currentPage > 1 && paramName === toolbar.options.limit) {
            newPage = Math.floor(toolbar.getCurrentLimit() * (currentPage - 1) / paramValue) + 1;

            if (newPage > 1) {
                paramData[toolbar.options.page] = newPage;
            } else {
                delete paramData[toolbar.options.page];
            }
        }

        paramData[paramName] = paramValue;

        if (paramValue == defaultValue) {
            delete paramData[paramName];
        }

        paramData = $.param(paramData);

        return baseUrl + (paramData.length ? '?' + paramData : '');
    }

    function updatePageTitle(title) {
        if (!title) {
            return;
        }

        document.title = title;

        const $title = $('.page-title-wrapper .page-title .base, .page-title-wrapper .page-title');

        if ($title.length) {
            $title.first().text(title);
        } else {
            $('.column.main').prepend(
                '<div class="page-title-wrapper">' +
                    '<h1 class="page-title"><span class="base"></span></h1>' +
                '</div>'
            );
            $('.page-title-wrapper .base').text(title);
        }
    }

    function loadFromUrl(pageUrl, pushState) {
        listingState.mergeOptions();

        const options = listingState.getOptions();

        if (!pageUrl || !options.fragmentUrl) {
            if (pageUrl) {
                window.location.href = pageUrl;
            }

            return;
        }

        if (typeof pushState === 'undefined') {
            pushState = true;
        }

        $('body').trigger('processStart');

        $.ajax({
            url: buildFragmentUrl(pageUrl),
            type: 'GET',
            dataType: 'json',
            cache: false,
            headers: {
                'X-Requested-With': 'XMLHttpRequest'
            }
        }).done(function (response) {
            if (!listingDom.updateDom(response)) {
                window.location.href = pageUrl;

                return;
            }

            if (response.pageTitle) {
                updatePageTitle(response.pageTitle);
            }

            if (pushState && response.url) {
                window.history.pushState({}, '', response.url);
            }
        }).fail(function () {
            window.location.href = pageUrl;
        }).always(function () {
            $('body').trigger('processStop');
        });
    }

    function isListingNavigationLink(link) {
        if (!link || !link.getAttribute('href')) {
            return false;
        }

        const href = link.getAttribute('href');

        if (href === '#' || href.indexOf('javascript:') === 0) {
            return false;
        }

        return Boolean(link.closest('.sidebar-main .filter-options, .sidebar-main .filter-actions'));
    }

    function isPagerLink(link) {
        if (!link || !link.getAttribute('href')) {
            return false;
        }

        const href = link.getAttribute('href');

        if (href === '#' || href.indexOf('javascript:') === 0) {
            return false;
        }

        return Boolean(link.closest('.pages-items')) &&
            Boolean(link.closest('#rokezzz-listing-root, .rokezzz-listing-root, .column.main'));
    }

    function handleListingNavigationClick(event) {
        listingState.mergeOptions();

        if (!listingState.getOptions().fragmentUrl) {
            return;
        }

        const link = event.target.closest('a');

        if (!isListingNavigationLink(link)) {
            return;
        }

        event.preventDefault();
        event.stopImmediatePropagation();
        loadFromUrl(link.getAttribute('href'));
    }

    function handlePagerClick(event) {
        const link = event.target.closest('.pages-items a');

        if (!isPagerLink(link)) {
            return;
        }

        listingState.mergeOptions();

        if (!listingState.getOptions().fragmentUrl) {
            return;
        }

        event.preventDefault();
        event.stopImmediatePropagation();
        loadFromUrl(link.getAttribute('href'));
    }

    function bindPopstate() {
        if (isPopstateBound) {
            return;
        }

        isPopstateBound = true;

        $(window).on('popstate.rokezzzCatalogListing', function () {
            listingState.mergeOptions();

            const options = listingState.getOptions();

            if (options.listingType === 'search' && !isSearchResultsUrl(window.location.href)) {
                window.location.reload();

                return;
            }

            if (isSearchResultsUrl(window.location.href)) {
                listingState.setOptions({
                    listingType: 'search',
                    fragmentUrl: options.searchFragmentUrl || options.fragmentUrl,
                    categoryId: null
                });
            }

            if (listingState.getOptions().fragmentUrl) {
                loadFromUrl(window.location.href, false);
            }
        });
    }

    function registerClickHandlers() {
        document.addEventListener('click', handleListingNavigationClick, true);
        document.addEventListener('click', handlePagerClick, true);
    }

    return {
        loadFromUrl: loadFromUrl,
        buildToolbarUrl: buildToolbarUrl,
        bindPopstate: bindPopstate,
        registerClickHandlers: registerClickHandlers
    };
});

define([
    'jquery',
    'Magento_Catalog/js/product/list/toolbar',
    'mage/apply/main',
    'mage/loader'
], function ($, toolbarModule, mageApply) {
    'use strict';

    // rokezzz-catalog-listing v5 — paginacja: capture na document + loadFromUrl(href)

    var options = {
        listingType: 'category',
        categoryId: null,
        fragmentUrl: '',
        searchFragmentUrl: '',
        searchResultUrl: '',
        filterLinkSelector: '.sidebar-main .filter-options a, .sidebar-main .filter-actions a',
        pagerLinkSelector: '.pages-items a',
        productsSelectors: '.column.main .toolbar-products, .column.main .products.wrapper, .column.main .message.info.empty'
    };

    var originalChangeUrl = $.mage.productListToolbarForm.prototype.changeUrl;
    var isBound = false;

    function mergeOptions(config) {
        if (config) {
            options = $.extend({}, options, config);
            window.rokezzzCatalogListingConfig = options;
        } else if (window.rokezzzCatalogListingConfig) {
            options = $.extend({}, options, window.rokezzzCatalogListingConfig);
        }
    }

    function isSearchResultsUrl(url) {
        try {
            return new URL(url, window.location.origin).pathname.indexOf('catalogsearch/result') !== -1;
        } catch (e) {
            return false;
        }
    }

    function buildFragmentUrl(pageUrl) {
        var fragment = new URL(options.fragmentUrl, window.location.origin),
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
        var urlPaths = toolbar.options.url.split('?'),
            baseUrl = urlPaths[0],
            paramData = toolbar.getUrlParams(),
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

        if (paramValue == defaultValue) { // eslint-disable-line eqeqeq
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

        var $title = $('.page-title-wrapper .page-title .base, .page-title-wrapper .page-title');

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

    function ensureListingRoot() {
        if ($('#rokezzz-listing-root').length) {
            return;
        }

        var $main = $('.column.main');

        $main.children().not('.page.messages, .page-title-wrapper').addClass('rokezzz-pre-search-content').hide();
        $main.append('<div id="rokezzz-listing-root" class="rokezzz-listing-root"></div>');
    }

    function prepareSearchListing() {
        options.listingType = 'search';
        options.fragmentUrl = options.searchFragmentUrl || options.fragmentUrl;
        options.categoryId = null;

        window.rokezzzCatalogListingConfig = Object.assign(window.rokezzzCatalogListingConfig || {}, {
            listingType: 'search',
            fragmentUrl: options.fragmentUrl,
            searchFragmentUrl: options.searchFragmentUrl,
            categoryId: null
        });

        ensureListingRoot();
        bindEvents();
    }

    function buildSearchUrl($form) {
        var action = $form.attr('action') || options.searchResultUrl,
            query = $.trim($form.find('input[name="q"]').val()),
            url = new URL(action, window.location.origin);

        url.searchParams.set('q', query);
        url.searchParams.delete('p');

        return url.toString();
    }

    function loadFromUrl(pageUrl, pushState) {
        mergeOptions();

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
            if (!updateDom(response)) {
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

    function updateDom(response) {
        var $productsHtml = $('<div>').html(response.productsHtml || ''),
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

    function encodeMagentoUenc(url) {
        return window.btoa(url).replace(/\+/g, '-').replace(/\//g, '_').replace(/=/g, ',');
    }

    function fixAddToCartReturnUrls(returnUrl) {
        var encodedReturnUrl = encodeMagentoUenc(returnUrl);

        $('#rokezzz-listing-root form[data-role="tocart-form"], .column.main form[data-role="tocart-form"]').each(function () {
            var $form = $(this),
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
        var $searchResults = $productsHtml.find('.search.results'),
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
        var $target = $('.column.main .page-title-wrapper').last(),
            $categoryView = $('.column.main .category-view');

        if ($categoryView.length) {
            $target = $categoryView.last();
        }

        if ($target.length) {
            $target.after($productsHtml.children());
        } else {
            $('.column.main').append($productsHtml.children());
        }
    }

    function performSearch($form) {
        mergeOptions();

        if (!options.searchFragmentUrl) {
            $form.get(0).submit();

            return;
        }

        prepareSearchListing();

        var searchUrl = buildSearchUrl($form);

        if (!options.fragmentUrl) {
            window.location.href = searchUrl;

            return;
        }

        loadFromUrl(searchUrl);
    }

    function bindSearchFormOnElement(form, config) {
        if (!form || form.rokezzzAsyncSearchBound) {
            return;
        }

        mergeOptions(config || {});

        if (!options.searchFragmentUrl) {
            return;
        }

        form.rokezzzAsyncSearchBound = true;

        form.addEventListener('submit', function (event) {
            var $form = $(form),
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

    function isListingNavigationLink(link) {
        if (!link || !link.getAttribute('href')) {
            return false;
        }

        var href = link.getAttribute('href');

        if (href === '#' || href.indexOf('javascript:') === 0) {
            return false;
        }

        return Boolean(link.closest('.sidebar-main .filter-options, .sidebar-main .filter-actions'));
    }

    function isPagerLink(link) {
        if (!link || !link.getAttribute('href')) {
            return false;
        }

        var href = link.getAttribute('href');

        if (href === '#' || href.indexOf('javascript:') === 0) {
            return false;
        }

        return Boolean(link.closest('.pages-items')) &&
            Boolean(link.closest('#rokezzz-listing-root, .rokezzz-listing-root, .column.main'));
    }

    function handleListingNavigationClick(event) {
        mergeOptions();

        if (!options.fragmentUrl) {
            return;
        }

        var link = event.target.closest('a');

        if (!isListingNavigationLink(link)) {
            return;
        }

        event.preventDefault();
        event.stopImmediatePropagation();
        loadFromUrl(link.getAttribute('href'));
    }

    function handlePagerClick(event) {
        var link = event.target.closest('.pages-items a');

        if (!isPagerLink(link)) {
            return;
        }

        mergeOptions();

        if (!options.fragmentUrl) {
            return;
        }

        event.preventDefault();
        event.stopImmediatePropagation();
        loadFromUrl(link.getAttribute('href'));
    }

    function bindEvents() {
        if (isBound) {
            return;
        }

        isBound = true;

        $(window).on('popstate.rokezzzCatalogListing', function () {
            mergeOptions();

            if (options.listingType === 'search' && !isSearchResultsUrl(window.location.href)) {
                window.location.reload();

                return;
            }

            if (isSearchResultsUrl(window.location.href)) {
                options.listingType = 'search';
                options.fragmentUrl = options.searchFragmentUrl || options.fragmentUrl;
                options.categoryId = null;
            }

            if (options.fragmentUrl) {
                loadFromUrl(window.location.href, false);
            }
        });
    }

    $.mage.productListToolbarForm.prototype.changeUrl = function (paramName, paramValue, defaultValue) {
        mergeOptions();

        if (!options.fragmentUrl) {
            return originalChangeUrl.apply(this, arguments);
        }

        loadFromUrl(buildToolbarUrl(this, paramName, paramValue, defaultValue));
    };

    var api = {
        init: function (config) {
            mergeOptions(config);
            bindEvents();
        },
        bindHeaderSearch: function (config) {
            mergeOptions(config);

            var form = document.getElementById('search_mini_form');

            if (form) {
                bindSearchFormOnElement(form, config);
            }
        },
        bindSearchFormOnElement: bindSearchFormOnElement,
        performSearch: function (form) {
            performSearch($(form));
        }
    };

    function catalogListingModule(config, element) {
        if (isSearchFormElement(element)) {
            mergeOptions(config);
            bindSearchFormOnElement(element, config);

            return api;
        }

        if (config) {
            api.init(config);
        }

        return api;
    }

    Object.keys(api).forEach(function (key) {
        catalogListingModule[key] = api[key];
    });

    mergeOptions();
    document.addEventListener('click', handleListingNavigationClick, true);
    document.addEventListener('click', handlePagerClick, true);

    return catalogListingModule;
});

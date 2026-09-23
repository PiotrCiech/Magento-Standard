define([], function () {
    'use strict';

    const defaults = {
        listingType: 'category',
        categoryId: null,
        fragmentUrl: '',
        searchFragmentUrl: '',
        searchResultUrl: '',
        filterLinkSelector: '.sidebar-main .filter-options a, .sidebar-main .filter-actions a',
        pagerLinkSelector: '.pages-items a',
        productsSelectors: '.column.main .toolbar-products, .column.main .products.wrapper, .column.main .message.info.empty'
    };

    let options = Object.assign({}, defaults);

    return {
        getOptions: function () {
            return options;
        },

        mergeOptions: function (config) {
            if (config) {
                options = Object.assign({}, options, config);
                window.rokezzzCatalogListingConfig = options;
            } else if (window.rokezzzCatalogListingConfig) {
                options = Object.assign({}, options, window.rokezzzCatalogListingConfig);
            }

            return options;
        },

        setOptions: function (patch) {
            options = Object.assign({}, options, patch);
            window.rokezzzCatalogListingConfig = Object.assign(
                window.rokezzzCatalogListingConfig || {},
                patch
            );

            return options;
        }
    };
});

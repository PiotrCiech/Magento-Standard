define([
    './listing-state',
    './listing-navigation',
    './header-search'
], function (listingState, listingNavigation, headerSearch) {
    'use strict';

    const api = {
        init: function (config) {
            listingState.mergeOptions(config);
            listingNavigation.bindPopstate();
        },
        bindHeaderSearch: function (config) {
            listingState.mergeOptions(config);

            const form = document.getElementById('search_mini_form');

            if (form) {
                headerSearch.bindSearchFormOnElement(form, config);
            }
        },
        bindSearchFormOnElement: headerSearch.bindSearchFormOnElement,
        performSearch: function (form) {
            headerSearch.performSearch($(form));
        }
    };

    function catalogListingModule(config, element) {
        if (headerSearch.isSearchFormElement(element)) {
            listingState.mergeOptions(config);
            headerSearch.bindSearchFormOnElement(element, config);

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

    listingState.mergeOptions();
    listingNavigation.registerClickHandlers();

    return catalogListingModule;
});

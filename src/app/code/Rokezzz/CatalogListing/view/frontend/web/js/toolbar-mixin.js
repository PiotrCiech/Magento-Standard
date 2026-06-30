define([
    'jquery',
    './listing-state',
    './listing-navigation'
], function ($, listingState, listingNavigation) {
    'use strict';

    return function (targetWidget) {
        $.widget('mage.productListToolbarForm', targetWidget, {
            changeUrl: function (paramName, paramValue, defaultValue) {
                listingState.mergeOptions();

                if (!listingState.getOptions().fragmentUrl) {
                    return this._super(paramName, paramValue, defaultValue);
                }

                listingNavigation.loadFromUrl(
                    listingNavigation.buildToolbarUrl(this, paramName, paramValue, defaultValue)
                );
            }
        });

        return $.mage.productListToolbarForm;
    };
});

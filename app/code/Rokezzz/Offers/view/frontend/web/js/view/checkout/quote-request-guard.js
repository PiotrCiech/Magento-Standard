define([
    'uiComponent',
    'mage/translate'
], function (Component, $t) {
    'use strict';

    return Component.extend({
        defaults: {
            template: 'Rokezzz_Offers/checkout/quote-request-guard'
        },

        isBlocked: function () {
            return !!(window.checkoutConfig.rokezzzOffers || {}).hasQuoteRequestItems;
        },

        getMessage: function () {
            var offersConfig = window.checkoutConfig.rokezzzOffers || {};

            return offersConfig.checkoutBlockedMessage
                || $t('Please submit a quote request for out-of-stock items before checkout.');
        },

        getCartUrl: function () {
            return (window.checkoutConfig.rokezzzOffers || {}).cartUrl || '';
        },

        getCartLinkLabel: function () {
            return (window.checkoutConfig.rokezzzOffers || {}).cartLinkLabel || $t('Return to cart');
        }
    });
});

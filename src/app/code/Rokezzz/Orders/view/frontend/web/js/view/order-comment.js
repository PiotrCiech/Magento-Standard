define([
    'uiComponent',
    'Magento_Checkout/js/model/quote',
    'Rokezzz_Orders/js/model/order-comment'
], function (Component, quote, orderComment) {
    'use strict';

    return Component.extend({
        defaults: {
            template: 'Rokezzz_Orders/order-comment',
            step: 'shipping'
        },

        /**
         * @inheritdoc
         */
        initialize: function () {
            this._super();

            this.orderComment = orderComment.value;

            return this;
        },

        isVisible: function () {
            var checkoutConfig = window.checkoutConfig.rokezzzOrderNotes || {};

            if (!checkoutConfig.enabled) {
                return false;
            }

            if (this.step === 'shipping') {
                return !quote.isVirtual();
            }

            return quote.isVirtual();
        },

        getLabel: function () {
            return (window.checkoutConfig.rokezzzOrderNotes || {}).label || '';
        },

        getPlaceholder: function () {
            return (window.checkoutConfig.rokezzzOrderNotes || {}).placeholder || '';
        },

        getMaxLength: function () {
            return (window.checkoutConfig.rokezzzOrderNotes || {}).maxLength || 1000;
        }
    });
});

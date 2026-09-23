define([
    'uiComponent',
    'Magento_Checkout/js/model/quote',
    'Rokezzz_Orders/js/model/order-comment'
], function (Component, quote, orderComment) {
    'use strict';

    return Component.extend({
        defaults: {
            template: 'Rokezzz_Orders/order-comment',
            step: 'shipping',
            label: '',
            placeholder: '',
            maxLength: 1000
        },

        /**
         * @inheritdoc
         */
        initConfig: function () {
            this._super();

            var notesConfig = window.checkoutConfig.rokezzzOrderNotes || {};

            this.label = this.label || notesConfig.label || '';
            this.placeholder = this.placeholder || notesConfig.placeholder || '';
            this.maxLength = this.maxLength || notesConfig.maxLength || 1000;

            return this;
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
        }
    });
});

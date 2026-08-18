define([
    'uiComponent',
    'Magento_Checkout/js/model/quote',
    'Rokezzz_OrderAdvance/js/model/order-number'
], function (Component, quote, orderNumber) {
    'use strict';

    return Component.extend({
        defaults: {
            template: 'Rokezzz_OrderAdvance/order-number',
            step: 'shipping',
            label: '',
            placeholder: '',
            maxLength: 64
        },

        /**
         * @inheritdoc
         */
        initConfig: function () {
            this._super();

            var numberConfig = window.checkoutConfig.rokezzzCustomerOrderNumber || {};

            this.label = this.label || numberConfig.label || '';
            this.placeholder = this.placeholder || numberConfig.placeholder || '';
            this.maxLength = this.maxLength || numberConfig.maxLength || 64;

            return this;
        },

        /**
         * @inheritdoc
         */
        initialize: function () {
            this._super();

            this.orderNumber = orderNumber.value;

            return this;
        },

        isVisible: function () {
            var checkoutConfig = window.checkoutConfig.rokezzzCustomerOrderNumber || {};

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

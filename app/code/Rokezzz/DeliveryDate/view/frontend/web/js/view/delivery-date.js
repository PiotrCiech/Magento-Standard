define([
    'uiComponent',
    'Magento_Checkout/js/model/quote',
    'Rokezzz_DeliveryDate/js/model/delivery-date',
    'mage/translate'
], function (Component, quote, deliveryDate, $t) {
    'use strict';

    return Component.extend({
        defaults: {
            template: 'Rokezzz_DeliveryDate/delivery-date',
            step: 'shipping',
            label: '',
            required: false,
            minDate: '',
            maxDate: '',
            excludeWeekends: false,
            validationMessage: ''
        },

        /**
         * @inheritdoc
         */
        initConfig: function () {
            this._super();

            var dateConfig = window.checkoutConfig.rokezzzDeliveryDate || {};

            this.label = this.label || dateConfig.label || '';
            this.required = this.required || !!dateConfig.required;
            this.minDate = this.minDate || dateConfig.minDate || '';
            this.maxDate = this.maxDate || dateConfig.maxDate || '';
            this.excludeWeekends = this.excludeWeekends || !!dateConfig.excludeWeekends;

            return this;
        },

        /**
         * @inheritdoc
         */
        initialize: function () {
            this._super();

            this.deliveryDate = deliveryDate.value;

            return this;
        },

        isVisible: function () {
            var checkoutConfig = window.checkoutConfig.rokezzzDeliveryDate || {};

            if (!checkoutConfig.enabled) {
                return false;
            }

            if (this.step === 'shipping') {
                return !quote.isVirtual();
            }

            return quote.isVirtual();
        },

        onDateChange: function () {
            this.validationMessage = this.validateDate(this.deliveryDate());
        },

        validateDate: function (value) {
            if (!value) {
                return this.required ? $t('Please select a preferred delivery date.') : '';
            }

            if (this.minDate && value < this.minDate) {
                return $t('The selected delivery date is outside the allowed range.');
            }

            if (this.maxDate && value > this.maxDate) {
                return $t('The selected delivery date is outside the allowed range.');
            }

            if (this.excludeWeekends && this.isWeekend(value)) {
                return $t('Weekend dates cannot be selected as the delivery date.');
            }

            return '';
        },

        isWeekend: function (value) {
            var parts = value.split('-'),
                date = new Date(parseInt(parts[0], 10), parseInt(parts[1], 10) - 1, parseInt(parts[2], 10)),
                day = date.getDay();

            return day === 0 || day === 6;
        }
    });
});

define([
    'Magento_Checkout/js/view/summary/abstract-total',
    'Magento_Checkout/js/model/totals'
], function (Component, totals) {
    'use strict';

    return Component.extend({
        defaults: {
            template: 'Rokezzz_LoyaltyPoints/summary/loyalty-points-discount'
        },

        /**
         * @return {*|null}
         */
        getSegment: function () {
            return totals.getSegment('rokezzz_loyalty_points');
        },

        /**
         * @return {boolean}
         */
        isDisplayed: function () {
            return this.isFullMode() && this.getPureValue() !== 0;
        },

        /**
         * @return {string}
         */
        getTitle: function () {
            var segment = this.getSegment();

            return segment ? segment.title : '';
        },

        /**
         * @return {number}
         */
        getPureValue: function () {
            var segment = this.getSegment();

            return segment ? parseFloat(segment.value) : 0;
        },

        /**
         * @return {*|String}
         */
        getValue: function () {
            return this.getFormattedPrice(this.getPureValue());
        }
    });
});

define([
    'uiComponent',
    'Magento_Customer/js/customer-data'
], function (Component, customerData) {
    'use strict';

    return Component.extend({
        initialize: function () {
            this._super();

            this.latestOrderStatus = customerData.get('rokezzz-latest-order-status');

            return this;
        },

        isVisible: function () {
            return !!(this.latestOrderStatus() || {}).visible;
        },

        incrementId: function () {
            return (this.latestOrderStatus() || {}).increment_id || '';
        },

        statusLabel: function () {
            return (this.latestOrderStatus() || {}).status_label || '';
        },

        orderUrl: function () {
            return (this.latestOrderStatus() || {}).order_url || '';
        }
    });
});

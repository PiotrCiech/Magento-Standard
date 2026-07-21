define([
    'Rokezzz_OrderAdvance/js/model/order-number'
], function (orderNumber) {
    'use strict';

    return function (placeOrderAction) {
        return function (paymentData, messageContainer) {
            if (!(window.checkoutConfig.rokezzzCustomerOrderNumber || {}).enabled) {
                return placeOrderAction(paymentData, messageContainer);
            }

            var number = orderNumber.value();

            if (number) {
                paymentData.extension_attributes = paymentData.extension_attributes || {};
                paymentData.extension_attributes.customer_order_number = number;
            }

            return placeOrderAction(paymentData, messageContainer);
        };
    };
});

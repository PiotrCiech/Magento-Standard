define([
    'Rokezzz_OrderAdvance/js/model/order-number'
], function (orderNumber) {
    'use strict';

    return function (payloadExtender) {
        return function (payload) {
            payloadExtender(payload);

            if (!(window.checkoutConfig.rokezzzCustomerOrderNumber || {}).enabled) {
                return payload;
            }

            var number = orderNumber.value();

            if (!number) {
                return payload;
            }

            payload.addressInformation.extension_attributes =
                payload.addressInformation.extension_attributes || {};
            payload.addressInformation.extension_attributes.customer_order_number = number;

            return payload;
        };
    };
});

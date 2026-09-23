define([
    'Rokezzz_DeliveryDate/js/model/delivery-date'
], function (deliveryDate) {
    'use strict';

    return function (placeOrderAction) {
        return function (paymentData, messageContainer) {
            if (!(window.checkoutConfig.rokezzzDeliveryDate || {}).enabled) {
                return placeOrderAction(paymentData, messageContainer);
            }

            var date = deliveryDate.value();

            if (date) {
                paymentData.extension_attributes = paymentData.extension_attributes || {};
                paymentData.extension_attributes.preferred_delivery_date = date;
            }

            return placeOrderAction(paymentData, messageContainer);
        };
    };
});

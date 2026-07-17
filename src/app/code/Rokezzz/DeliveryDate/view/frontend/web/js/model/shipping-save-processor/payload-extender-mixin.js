define([
    'Rokezzz_DeliveryDate/js/model/delivery-date'
], function (deliveryDate) {
    'use strict';

    return function (payloadExtender) {
        return function (payload) {
            payloadExtender(payload);

            if (!(window.checkoutConfig.rokezzzDeliveryDate || {}).enabled) {
                return payload;
            }

            var date = deliveryDate.value();

            if (!date) {
                return payload;
            }

            payload.addressInformation.extension_attributes =
                payload.addressInformation.extension_attributes || {};
            payload.addressInformation.extension_attributes.preferred_delivery_date = date;

            return payload;
        };
    };
});

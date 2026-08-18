define([
    'Rokezzz_Orders/js/model/order-comment'
], function (orderComment) {
    'use strict';

    return function (payloadExtender) {
        return function (payload) {
            payloadExtender(payload);

            if (!(window.checkoutConfig.rokezzzOrderNotes || {}).enabled) {
                return payload;
            }

            var comment = orderComment.value();

            if (!comment) {
                return payload;
            }

            payload.addressInformation.extension_attributes =
                payload.addressInformation.extension_attributes || {};
            payload.addressInformation.extension_attributes.order_comment = comment;

            return payload;
        };
    };
});

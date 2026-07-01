define([
    'Rokezzz_Orders/js/model/order-comment'
], function (orderComment) {
    'use strict';

    return function (placeOrderAction) {
        return function (paymentData, messageContainer) {
            var comment = orderComment.value();

            if (comment) {
                paymentData.extension_attributes = paymentData.extension_attributes || {};
                paymentData.extension_attributes.order_comment = comment;
            }

            return placeOrderAction(paymentData, messageContainer);
        };
    };
});

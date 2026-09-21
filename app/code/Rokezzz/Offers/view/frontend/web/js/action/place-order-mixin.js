define([
    'jquery',
    'Magento_Ui/js/model/messageList',
    'mage/translate'
], function ($, messageList, $t) {
    'use strict';

    return function (placeOrderAction) {
        return function (paymentData, messageContainer) {
            var offersConfig = window.checkoutConfig.rokezzzOffers || {};

            if (offersConfig.hasQuoteRequestItems) {
                messageList.addErrorMessage({
                    message: offersConfig.checkoutBlockedMessage
                        || $t('Please submit a quote request for out-of-stock items before checkout.')
                });

                return $.Deferred().reject().promise();
            }

            return placeOrderAction(paymentData, messageContainer);
        };
    };
});

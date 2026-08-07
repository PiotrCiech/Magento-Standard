define([
    'jquery',
    'mage/translate'
], function ($, $t) {
    'use strict';

    return function (config) {
        if (!config.hasQuoteRequestItems) {
            return;
        }

        var selectors = [
            '.checkout-methods-items .action.primary.checkout',
            'button[data-role="proceed-to-checkout"]',
            '.cart-summary .checkout-methods-items .action.primary.checkout'
        ].join(', ');

        var $buttons = $(selectors);
        var disabledClass = 'rokezzz-checkout-disabled';

        $buttons.prop('disabled', true).addClass('disabled ' + disabledClass);

        if ($('.rokezzz-cart-checkout-notice').length === 0 && config.checkoutBlockedMessage) {
            $('<div/>', {
                'class': 'message message-notice notice rokezzz-cart-checkout-notice',
                text: config.checkoutBlockedMessage
            }).insertBefore($('.checkout-methods-items').first());
        }
    };
});

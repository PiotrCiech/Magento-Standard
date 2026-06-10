define(
	[
		'mage/translate',
		'Magento_Checkout/js/model/quote',
		'Magento_Ui/js/model/messageList',
		'jquery'
	], function ($t, quote, messageList, $) {
		'use strict';
		return {
			validate: function () {
				var isValid = true;

				if (
					(quote.shippingMethod().method_code == 'furgonetka') && (!$('input[name="furgonetka_pickup_point"]').val())
				) {
					messageList.addErrorMessage({message: $t('Proszę wybrać punkt odbioru przesyłki.')});
					isValid = false;
				}

				return isValid;
			}
		}
	}
);
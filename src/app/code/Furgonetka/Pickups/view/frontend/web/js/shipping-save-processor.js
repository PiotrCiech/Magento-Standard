define([
		'jquery',
		'Magento_Checkout/js/model/quote',
		'mage/storage',
		'Magento_Checkout/js/model/resource-url-manager',
		'Magento_Checkout/js/model/full-screen-loader',
		'Magento_Checkout/js/model/error-processor'
	],
	function ($, quote, storage, resourceUrlManager, fullScreenLoader, errorProcessor) {
		'use strict';
		return function (target) {

			var saveShippingInformationParent = target.saveShippingInformation;

			target.saveShippingInformation = function () {

				var result = saveShippingInformationParent.apply();

				fullScreenLoader.startLoader();

				var payload = {
					addressInformation: {
						shipping_address: quote.shippingAddress(),
						billing_address: quote.billingAddress(),
						shipping_method_code: quote.shippingMethod().method_code,
						shipping_carrier_code: quote.shippingMethod().carrier_code,
						extension_attributes: {
							furgonetka_pickup_point: $('[name="furgonetka_pickup_point"]').val()
						}
					}
				};

				return storage.post(
					resourceUrlManager.getUrlForSetShippingInformation(quote),
					JSON.stringify(payload)
				).done(
					function (response) {
						fullScreenLoader.stopLoader();
					}
				).fail(
					function (response) {
						errorProcessor.process(response);
						fullScreenLoader.stopLoader();
					}
				);

				return result;
			}
			return target;
		};
	});
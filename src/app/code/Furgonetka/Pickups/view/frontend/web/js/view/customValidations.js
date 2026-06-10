define(
	[
		'uiComponent',
		'Magento_Checkout/js/model/payment/additional-validators',
		'Furgonetka_Pickups/js/model/validatePoint'
	],
	function (Component, additionalValidators, validatePoint) {
		'use strict';
		additionalValidators.registerValidator(validatePoint);
		return Component.extend({});
	}
);
var config = {
    config: {
        mixins: {
            'Magento_Checkout/js/model/shipping-save-processor/payload-extender': {
                'Rokezzz_OrderAdvance/js/model/shipping-save-processor/payload-extender-mixin': true
            },
            'Magento_Checkout/js/action/place-order': {
                'Rokezzz_OrderAdvance/js/action/place-order-mixin': true
            }
        }
    }
};

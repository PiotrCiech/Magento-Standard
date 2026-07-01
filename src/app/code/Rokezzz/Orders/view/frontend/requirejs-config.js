var config = {
    map: {
        '*': {
            'Magento_Checkout/shipping': 'Rokezzz_Orders/shipping'
        }
    },
    config: {
        mixins: {
            'Magento_Checkout/js/model/shipping-save-processor/payload-extender': {
                'Rokezzz_Orders/js/model/shipping-save-processor/payload-extender-mixin': true
            },
            'Magento_Checkout/js/action/place-order': {
                'Rokezzz_Orders/js/action/place-order-mixin': true
            }
        }
    }
};

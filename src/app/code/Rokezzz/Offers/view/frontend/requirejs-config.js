var config = {
    map: {
        '*': {
            'Magento_Checkout/minicart/item/default': 'Rokezzz_Offers/minicart/item/default'
        }
    },
    config: {
        mixins: {
            'Magento_Checkout/js/action/place-order': {
                'Rokezzz_Offers/js/action/place-order-mixin': true
            }
        }
    }
};

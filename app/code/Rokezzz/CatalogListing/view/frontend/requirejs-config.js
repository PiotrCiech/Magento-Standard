var config = {
    map: {
        '*': {
            rokezzzCatalogListing: 'Rokezzz_CatalogListing/js/catalog-listing'
        }
    },
    config: {
        mixins: {
            'Magento_Catalog/js/product/list/toolbar': {
                'Rokezzz_CatalogListing/js/toolbar-mixin': true
            }
        }
    }
};

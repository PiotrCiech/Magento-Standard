define([
    'jquery',
    'Magento_Ui/js/modal/confirm'
], function ($, confirm) {
    'use strict';

    return function (config, element) {
        $(element).on('click', function () {
            confirm({
                title: config.title,
                content: config.content,
                buttons: [{
                    text: config.cancelText,
                    class: 'action-secondary action-dismiss',
                    click: function (event) {
                        this.closeModal(event);
                    }
                }, {
                    text: config.confirmText,
                    class: 'action-primary action-accept',
                    click: function () {
                        $(config.formSelector).trigger('submit');
                    }
                }]
            });
        });
    };
});

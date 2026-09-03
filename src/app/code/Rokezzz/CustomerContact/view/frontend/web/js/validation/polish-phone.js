define([
    'jquery',
    'mage/translate'
], function ($, $t) {
    'use strict';

    $.validator.addMethod(
        'validate-rokezzz-polish-phone',
        function (value) {
            if (!value || $.trim(value) === '') {
                return true;
            }

            var digits = value.replace(/\D/g, '');

            if (digits.indexOf('48') === 0 && digits.length === 11) {
                digits = digits.substring(2);
            }

            return /^\d{9}$/.test(digits);
        },
        $t('Please enter a valid phone number.')
    );
});

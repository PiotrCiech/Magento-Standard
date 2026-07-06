define([
    'jquery',
    'ko',
    'uiComponent',
    'Magento_Checkout/js/action/get-totals',
    'Magento_Checkout/js/model/full-screen-loader',
    'mage/url',
    'Rokezzz_LoyaltyPoints/js/model/loyalty-points'
], function ($, ko, Component, getTotalsAction, fullScreenLoader, urlBuilder, loyaltyPointsModel) {
    'use strict';

    return Component.extend({
        defaults: {
            template: 'Rokezzz_LoyaltyPoints/loyalty-points',
            showComponent: false,
            balanceLabel: '',
            maxRedeemablePoints: 0
        },

        initialize: function () {
            this._super();

            this.config = window.checkoutConfig.rokezzzLoyaltyPoints || {};
            this.pointsToApply = loyaltyPointsModel.pointsToApply;
            this.appliedPoints = loyaltyPointsModel.appliedPoints;
            this.isApplied = loyaltyPointsModel.isApplied;
            this.statusMessage = ko.observable('');

            this.appliedPoints(this.config.appliedPoints || 0);
            this.isApplied((this.config.appliedPoints || 0) > 0);
            this.pointsToApply(this.config.appliedPoints || 0);

            this.showComponent = !!this.config.enabled && (this.config.balance || 0) > 0;
            this.balanceLabel = $.mage.__('Available balance: %1 points.')
                .replace('%1', String(this.config.balance || 0));
            this.maxRedeemablePoints = this.config.maxRedeemablePoints || 0;

            return this;
        },

        canApply: function () {
            var points = parseInt(this.pointsToApply(), 10) || 0;

            return points > 0 && points >= (this.config.minPoints || 0);
        },

        applyPoints: function () {
            var self = this,
                points = parseInt(this.pointsToApply(), 10) || 0;

            fullScreenLoader.startLoader();

            $.ajax({
                url: urlBuilder.build(this.config.applyUrl),
                type: 'POST',
                dataType: 'json',
                data: {
                    points: points,
                    form_key: window.checkoutConfig.formKey
                }
            }).done(function (response) {
                self.statusMessage(response.message || '');
                self.appliedPoints(response.appliedPoints || 0);
                self.isApplied((response.appliedPoints || 0) > 0);
                self.config.appliedPoints = response.appliedPoints || 0;

                if (response.success) {
                    getTotalsAction([]);
                }
            }).always(function () {
                fullScreenLoader.stopLoader();
            });
        },

        cancelPoints: function () {
            var self = this;

            fullScreenLoader.startLoader();

            $.ajax({
                url: urlBuilder.build(this.config.cancelUrl),
                type: 'POST',
                dataType: 'json',
                data: {
                    form_key: window.checkoutConfig.formKey
                }
            }).done(function (response) {
                self.statusMessage(response.message || '');
                self.appliedPoints(0);
                self.isApplied(false);
                self.pointsToApply(0);
                self.config.appliedPoints = 0;

                if (response.success) {
                    getTotalsAction([]);
                }
            }).always(function () {
                fullScreenLoader.stopLoader();
            });
        }
    });
});

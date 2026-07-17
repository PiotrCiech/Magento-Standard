define([
    'ko'
], function (ko) {
    'use strict';

    return {
        pointsToApply: ko.observable(0),
        appliedPoints: ko.observable(0),
        isApplied: ko.observable(false)
    };
});

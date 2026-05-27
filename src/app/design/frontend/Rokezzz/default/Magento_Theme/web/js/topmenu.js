define([
    'jquery'
], function ($) {
    'use strict';

    return function (config, element) {

        console.log('✅ Skrypt załadowany. Czas kompilacji: ' + new Date().toLocaleTimeString());
        const body = $('body');
        const $el = $(element);

        $(document).on('click', '.action.nav-toggle', function (e) {
            e.preventDefault();
            body.addClass('nav-open');
        });


        $el.find('.nav-close, .mobile-nav-overlay').on('click', function () {
            body.removeClass('nav-open'); 
        });

        $el.find('.mobile-nav-list').on('click', 'li.parent > a', function (e) {
            if ($(window).width() < 992) {
                e.preventDefault();
                e.stopImmediatePropagation();
                
                $(this).toggleClass('is-open'); 
                $(this).siblings('.submenu').slideToggle(200); 
            }
        });

        $el.find('.desktop-nav-list .level0.parent > a').on('click', function(e) {
            if ($(window).width() >= 992 && ('ontouchstart' in window || navigator.maxTouchPoints > 0)) {
                const $parent = $(this).parent();
                
                if (!$parent.hasClass('touch-active')) {
                    e.preventDefault(); 
                    e.stopImmediatePropagation();
                    
                    $('.desktop-nav-list .level0').removeClass('touch-active'); 
                    $parent.addClass('touch-active');
                }
            }
        });

        $(document).on('click', function(e) {
            if ($(window).width() >= 992 && !$(e.target).closest('.desktop-nav-list').length) {
                $('.desktop-nav-list .level0').removeClass('touch-active');
            }
        });
    };
});

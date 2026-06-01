define([
    'jquery'
], function ($) {
    'use strict';

    return function (config, element) {

        const body = $('body');
        const $el = $(element);
        const isDesktop = window.matchMedia('(min-width: 992px)');

        function closeMobileMenu() {
            body.removeClass('nav-open');
            $el.find('.mobile-nav-list .submenu').hide();
            $el.find('.mobile-nav-list a.is-open')
                .removeClass('is-open')
                .attr('aria-expanded', 'false');
        }

        $(document).on('click', '.action.nav-toggle', function (e) {
            e.preventDefault();
            body.addClass('nav-open');
        });

        $el.find('.nav-close, .mobile-nav-overlay').on('click', function () {
            closeMobileMenu();
        });

        $(document).on('keydown', function (e) {
            if (e.key === 'Escape') {
                closeMobileMenu();
                $('.desktop-nav-list .touch-active')
                    .removeClass('touch-active');
            }
        });

        $el.find('.mobile-nav-list')
            .on('click', 'li.parent > a', function (e) {
                if (isDesktop.matches) {
                    return;
                }

                e.preventDefault();

                const $link = $(this);
                const isOpen = $link.hasClass('is-open');

                $link.toggleClass('is-open');
                $link.attr(
                    'aria-expanded',
                    isOpen ? 'false' : 'true'
                );

                $link.siblings('.submenu')
                    .stop(true, true)
                    .slideToggle(200);
            });

        $el.find('.desktop-nav-list .level0.parent > a')
            .on('click', function (e) {
                if (
                    !isDesktop.matches ||
                    !('ontouchstart' in window || navigator.maxTouchPoints > 0)
                ) {
                    return;
                }

                const $parent = $(this).parent();

                if (!$parent.hasClass('touch-active')) {
                    e.preventDefault();
                    $('.desktop-nav-list .level0')
                        .removeClass('touch-active');
                    $parent.addClass('touch-active');
                }
            });

        $(document).on('click', function (e) {
            if (
                isDesktop.matches && // Zmiana na matchMedia
                !$(e.target).closest('.desktop-nav-list').length
            ) {
                $('.desktop-nav-list .level0')
                    .removeClass('touch-active');
            }
        });

        let resizeTimer;

        $(window).on('resize', function () {
            clearTimeout(resizeTimer);
            resizeTimer = setTimeout(function () {
                if (isDesktop.matches) {
                    closeMobileMenu();
                }
            }, 150);
        });
    };
});

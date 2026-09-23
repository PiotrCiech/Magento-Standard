define(["jquery"], function ($) {
    "use strict";

    return function (config, element) {
        const options = {
                autoplay: config.autoplay || false,
                autoplaySpeed: config.autoplaySpeed || 5000,
                animationSpeed: config.animationSpeed || 500,
                showArrows: config.showArrows || true,
                showDots: config.showDots || true,
            },
            $container = $(element),
            $wrapper = $container.find(".slider-wrapper"),
            $dots = $container.find(".slider-dots .dot");

        let currentSlide = 0,
            autoplayTimeout,
            touchStartX = 0,
            touchEndX = 0,
            isFirstLoad = true,
            $slides = $wrapper.find(".slide");

        const totalSlides = $slides.length;

        function showSlide(index) {
            if (totalSlides === 0) return;

            currentSlide = index;

            const offset = -currentSlide * 100,
                transitionStyle = isFirstLoad
                    ? "none"
                    : `transform ${options.animationSpeed}ms ease-in-out`;

            $wrapper.css({
                transform: `translateX(${offset}%)`,
                transition: transitionStyle,
            });

            isFirstLoad = false;

            const actualIndex =
                (((currentSlide - 1) % totalSlides) + totalSlides) %
                totalSlides;

            $dots.removeClass("active");
            $dots.eq(actualIndex).addClass("active");

            resetAutoplay();

            if (currentSlide === 0) {
                setTimeout(() => {
                    $wrapper.css({
                        transition: "none",
                    });
                    currentSlide = totalSlides;
                    $wrapper.css({
                        transform: `translateX(${-currentSlide * 100}%)`,
                    });
                }, options.animationSpeed);
            } else if (currentSlide === totalSlides + 1) {
                setTimeout(() => {
                    $wrapper.css({
                        transition: "none",
                    });
                    currentSlide = 1;
                    $wrapper.css({
                        transform: `translateX(${-currentSlide * 100}%)`,
                    });
                }, options.animationSpeed);
            }
        }

        function nextSlide() {
            showSlide(currentSlide + 1);
        }

        function prevSlide() {
            showSlide(currentSlide - 1);
        }

        function autoplay() {
            if (!options.autoplay) return;

            autoplayTimeout = setTimeout(() => {
                nextSlide();
                autoplay();
            }, options.autoplaySpeed);
        }

        function resetAutoplay() {
            if (autoplayTimeout) {
                clearTimeout(autoplayTimeout);
            }
            if (options.autoplay) {
                autoplay();
            }
        }

        function handleTouchStart(e) {
            touchStartX = e.touches[0].clientX;
        }

        function handleTouchEnd(e) {
            touchEndX = e.changedTouches[0].clientX;
            handleSwipe();
        }

        function handleSwipe() {
            const threshold = 50,
                diff = touchStartX - touchEndX;

            if (Math.abs(diff) > threshold) {
                if (diff > 0) {
                    nextSlide();
                } else {
                    prevSlide();
                }
            }
        }

        function init() {
            const $firstSlide = $slides.first().clone(),
                $lastSlide = $slides.last().clone();

            $wrapper.append($firstSlide);
            $wrapper.prepend($lastSlide);

            $slides = $wrapper.find(".slide");

            $wrapper.css({
                display: "flex",
                transition: "none",
            });

            $slides.css({
                width: `${100 / totalSlides}%`,
                "flex-shrink": 0,
            });

            currentSlide = 1;

            $wrapper.css({
                transform: `translateX(${-currentSlide * 100}%)`,
            });

            if (options.showArrows) {
                $container.find(".slider-arrow-prev").on("click", (e) => {
                    e.preventDefault();
                    prevSlide();
                });

                $container.find(".slider-arrow-next").on("click", (e) => {
                    e.preventDefault();
                    nextSlide();
                });
            }

            if (options.showDots) {
                $dots.on("click", function (e) {
                    e.preventDefault();

                    const index = $(this).data("slide-index");
                    
                    showSlide(index + 1);
                });
            }

            $wrapper.on("touchstart", handleTouchStart);
            $wrapper.on("touchend", handleTouchEnd);

            $container.on("mouseenter", () => {
                if (autoplayTimeout) {
                    clearTimeout(autoplayTimeout);
                }
            });

            $container.on("mouseleave", () => {
                resetAutoplay();
            });

            if (options.autoplay) {
                autoplay();
            }

            showSlide(1);
        }

        init();
    };
});

(function ($) {
    "use strict";
    /*********************************
     * Table of Context
     * *******************************/

    /*********************************
    /* Sticky Navbar
    *********************************/
    $(window).scroll(function () {
        var scrolling = $(this).scrollTop();
        var stikey = $(".header");

        if (scrolling >= 50) {
            $(stikey).addClass("nav-bg");
        } else {
            $(stikey).removeClass("nav-bg");
        }
    });

    /*********************************
    /* Click Scroll Action
    ********************************/
    // $(window).on('resize', function() {
    if ($(window).width() > 767) {
        $(".header__menu .main__menu li a").on("click", function (e) {
            var target = this.hash,
                $target = $(target);

            $("html, body")
                .stop()
                .animate(
                    {
                        scrollTop: $target.offset().top - 170,
                    },
                    100,
                    "swing",
                    function () {
                        window.location.hash = target;
                    }
                );
        });
    } else {
        $(".header__menu .main__menu li a").on("click", function (e) {
            var target = this.hash,
                $target = $(target);

            $("html, body")
                .stop()
                .animate(
                    {
                        scrollTop: $target.offset().top - 100,
                    },
                    100,
                    "swing",
                    function () {
                        window.location.hash = target;
                    }
                );
        });
    }
    //   });

    /*********************************
    /*  Mobile Menu
    *********************************/
    $(".header__toggle").on("click", function (event) {
        // event.preventDefault();
        $(".toggle__bar").toggleClass("active");
        $(".header__menu").toggleClass("mblMenu__open");
    });

    $(".header__menu ul li").on("click", function (event) {
        // event.preventDefault();
        $(".toggle__bar").removeClass("active");
        $(".header__menu").removeClass("mblMenu__open");
    });

    /*********************************
    /*  Partner Slider Carousel
    *********************************/
    if ($(".partner__slider").length > 0) {
        const isRTL = document.documentElement.getAttribute("dir") === "rtl";
        var partnerSlider = new Swiper(".partner__slider", {
            direction: "horizontal",
            loop: true,
            grabCursor: true,
            slidesPerView: "auto",
            rtl: isRTL,
            spaceBetween: 30,
            speed: 500,
            centeredSlides: false,
            freeMode: false,
            autoplay: {
                enabled: true,
                // delay: 1,
                // pauseOnMouseEnter: true,
                // disableOnInteraction: false,
            },
            // centerInsufficientSlides: true,

            breakpoints: {
                300: {
                    slidesPerView: 2,
                },
                375: {
                    slidesPerView: 2.5,
                    spaceBetween: 20,
                },
                479: {
                    slidesPerView: 3,
                },
                575: {
                    slidesPerView: 3,
                    spaceBetween: 20,
                },
                767: {
                    slidesPerView: 3.5,
                    spaceBetween: 20,
                },
                992: {
                    slidesPerView: 4,
                    spaceBetween: 20,
                },
                1199: {
                    slidesPerView: 5,
                },
            },
        });
    }

    /*********************************
    /*  Testimonial Slider Carousel
    *********************************/
    if ($(".testimonial__slider").length > 0) {
        const isRTL = document.documentElement.getAttribute("dir") === "rtl";
        var testimonialSlider = new Swiper(".testimonial__slider", {
            // direction: 'vertical',
            effect: "slide",
            slidesPerView: "1",
            spaceBetween: 30,
            rtl: isRTL,
            centeredSlides: false,
            grabCursor: true,
            loop: true,
            autoplay: {
                enabled: true,
                delay: 2000,
                reverseDirection: true,
                disableOnInteraction: false,
            },
            navigation: {
                nextEl: ".testimonial-swipe-next",
                prevEl: ".testimonial-swipe-prev",
            },
            pagination: {
                //pagination(dots)
                el: ".swiper-pagination",
            },
        });
    }

    /********************************
     * Custom Dropdown
     ********************************/
    $(".language__dropdown .selected").on("click", function (e) {
        e.preventDefault();
        $(".language__dropdown .list").toggleClass("active");
    });

    $(document).on("click", function (e) {
        if (
            $(e.target).closest(".header__meta").length === 0 &&
            $(e.target).closest(".language__dropdown .selected").length === 0
        ) {
            $(".language__dropdown .list").removeClass("active");
        }
    });

    /*********************************
    /*   Select2 Start
    *********************************/
    if ($(".dropdown-select-item").length > 0) {
        $(".dropdown-select-item").select2();
    }

    /**********************************
     *  Odometer Conter
     **********************************/
    $(".counter__count").appear(function (e) {
        var odo = $(".counter__count");
        odo.each(function () {
            var countNumber = $(this).attr("data-count");
            $(this).html(countNumber);
        });
    });

    /**********************************
     *  Wow animation
     **********************************/
    const wow = new WOW({
        animateClass: "animated",
        offset: -100,
    });
    wow.init();
})(jQuery);

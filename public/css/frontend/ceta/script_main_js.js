function formatMoney(number, format) {
    return number
        .toFixed(0) // always two decimal digits
        .replace(",", ".") // replace decimal point character with ,
        .replace(/(\d)(?=(\d{3})+(?!\d))/g, "$1,") + "" + format // use , as a separator
};

function fixSidebarBlog() {
    if ($(window).width() > 767) {
        if ($('.sidebar-fixed-blogs').length > 0) {
            var el = $('.sidebar-fixed-blogs');
            var blog_info = $('.blogs-content-left');
            var stickyTop = (el.offset().top) + 0; // returns number
            var stickyLeft = (el.offset().left) - 0;
            var stickyWidth = (el.width()) + 0;
            var stickyHeight = (el.height()) + 0;
            var sidebarHeight = ($('.sidebar-blog').height()) + 0;
            var footerTop = ($('.mainFooter-hrv').offset().top) - 100; // returns number
            var limit = footerTop - stickyHeight - 50;
            var height_info = blog_info.height();
            $(window).scroll(function () {
                var windowTop = $(window).scrollTop(); // returns number
                if (sidebarHeight <= height_info) {
                    if (stickyTop < windowTop) {
                        el.css({
                            position: 'fixed',
                            top: 60,
                            width: stickyWidth,
                            left: stickyLeft,
                        })
                    }
                    else {
                        el.css({
                            position: 'static',
                            top: 0,
                            width: stickyWidth,
                            left: stickyLeft,
                        })
                    }
                    if (limit < windowTop) {
                        var diff = limit - windowTop;
                        el.css({top: diff});
                    }
                }
            });
        }
    }
}

/****** slider index *****/
if ($('#slider_home_customers').length > 0) {
    var new_examples_carousel = $('#slider_home_customers');
    new_examples_carousel.owlCarousel({
        items: 1,
        nav: true,
        margin: 0,
        loop: true,
        dots: true,
        /*autoHeight:true,touchDrag:false,
        /*animateIn: 'fadeIn',animateOut: 'fadeOut',*/
        //autoplay:true,
        smartSpeed: 1000,
        autoplayTimeout: 5500,

    });
    new_examples_carousel.find('.owl-next').html("<span class='navinext'>Xem tiếp</span>");
    new_examples_carousel.find('.owl-prev').html("<span class='naviprev'>Quay về</span>");
}
$(document).ready(function () {
    if (window.template == 'blog' || window.template == 'article') {
        fixSidebarBlog();
        /*fixed sidebar Blog - article*/
    }
    $('.scroll a[href*="#"],a.scroll').click(function (e) {
        e.preventDefault();
        if (jQuery('.mainHeader-hrv').hasClass('nav-sticky')) {
            var hea = $($.attr(this, 'href'));
            var headerHeight = $(".mainHeader-hrv.nav-sticky").height();
            if (hea.length) {
                $('html, body').animate({
                    scrollTop: hea.offset().top + headerHeight - 70
                }, 600);
            }
        }
        else {
            var nav = $($.attr(this, 'href'));
            if (nav.length) {
                $('html, body').animate({
                    scrollTop: nav.offset().top - 70
                }, 600);
            }
        }
        $("#showmenu-mobile").removeClass("active-icon");
        $(".mainHeader-hrv").removeClass("fixed-nav");
        $('.overlay-mobile').removeClass("show-rgb");
        $('body').removeClass('overflow-hidden');
        $("#navHeader").removeClass("show-menu");

    });
    $(window).scroll(function () {
        if (jQuery(window).scrollTop() >= 200) {
            jQuery('.mainHeader-hrv').addClass('nav-sticky');
        } else {
            jQuery('.mainHeader-hrv').removeClass('nav-sticky').removeClass('nofade');
        }
    });
    $("#showmenu-mobile").click(function (e) {
        e.preventDefault();
        if ($('#showmenu-mobile').hasClass("active-icon")) {
            $(this).removeClass("active-icon");
            $(".mainHeader-hrv").removeClass("fixed-nav").addClass('nofade');
            $("#navHeader").removeClass("show-menu");
            $('.overlay-mobile').removeClass("show-rgb");
            $('body').removeClass('overflow-hidden');
        }
        else {
            $("#showmenu-mobile").addClass("active-icon");
            $(".mainHeader-hrv").toggleClass("fixed-nav");
            $("#navHeader").toggleClass("show-menu");
            $('.overlay-mobile').addClass("show-rgb")
            $('body').addClass('overflow-hidden');
        }
    });
    $('body').on('touchstart', '.overlay-mobile', function (e) {
        $("#showmenu-mobile").removeClass("active-icon");
        $(".mainHeader-hrv").removeClass("fixed-nav").addClass('nofade');
        $("#navHeader").removeClass("show-menu");
        $('.overlay-mobile').removeClass("show-rgb");
        $('body').removeClass('overflow-hidden');
    });

    /* megamenu mobile */
    if (jQuery(window).width() <= 991) {
        jQuery('.has-dropdown>a').click(function (e) {
            e.preventDefault();
            if ($(this).parents('li.has-dropdown').hasClass('open-menu')) {
                $(this).parents('li.has-dropdown').removeClass('open-menu');
                $(this).parents('li.has-dropdown').first().find('.dropdown-wrapper').slideUp();
            } else {
                $('.dropdown-wrapper').slideUp();
                $('li.has-dropdown').removeClass('open-menu');
                $(this).parents('li.has-dropdown').addClass('open-menu');
                $(this).parents('li.has-dropdown').first().find('.dropdown-wrapper').slideDown();
            }
        });
    }
    /* footer-togged-mobile */
    if (jQuery(window).width() <= 767) {
        jQuery('.widget-footer-mb .togged-footer').on('click', function () {
            jQuery(this).toggleClass('opened').parent().find('.footer-collapse').stop().slideToggle('');
        });
    }

    /****** POPUP REGISTRY *****/
    var parseQueryString = function () {
        var str = window.location.search.toLowerCase();
        var objURL = {};
        str.replace(new RegExp("([^?=&]+)(=([^&]*))?", "g"),
            function ($0, $1, $2, $3) {
                objURL[$1] = $3;
            });
        return objURL;
    };
    var params = parseQueryString();
    if (params['registry'] == 'true') {
        if ($('.hrv-btn-register').length > 0) {
            $('.hrv-btn-register').first().click();
        }
        if (window.template == 'index') {
            $('body').addClass('hrv-modal-open');
            $('#modal-register').addClass('hrv-modal-active');
        }
    }
});


jQuery(document).ready(function () {
    "use strict";
    // home text slider 
    $('.main-slider').owlCarousel({
        items: 1,
        loop: true,
        dots: true,
        autoplay: false,
        nav: false,
        autoplayHoverPause: false,
        margin: 0,
        smartSpeed: 1000,
        autoplayTimeout: 5000,
        mouseDrag: true,
        animateIn: 'slideInRight',
        animateOut: 'fadeOutLeft',
    });

    var owl = $('.main-slider');
    owl.owlCarousel({
        items: 1,
        loop: true,
        autoplay: true,
        autoplayTimeout: 5000,

    });
    owl.on('changed.owl.carousel', function (event) {
        var item = event.item.index - 2; // Position of the current item
        $('h1').removeClass('animated fadeInDown');
        $('p').removeClass('animated fadeInUp');
        $('a').removeClass('animated fadeInUp');
        $('.slide-image').removeClass('animated zoomIn');
        $('.owl-item').not('.cloned').eq(item).find('h1').addClass('animated fadeInDown');
        $('.owl-item').not('.cloned').eq(item).find('p').addClass('animated fadeInUp');
        $('.owl-item').not('.cloned').eq(item).find('a').addClass('animated fadeInUp');
        $('.owl-item').not('.cloned').eq(item).find('.slide-image').addClass('animated zoomIn');
    });
    // home text slider
    $('.select2').select2();

     


})





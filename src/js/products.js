$(document).ready(function(){
    $('.product-carousel').slick({
        slidesToShow: 3,  // Number of slides visible at once
        slidesToScroll: 3, // Number of slides to scroll at a time
        // autoplay: true,    // Auto-play enabled
        // autoplaySpeed: 2000, // Auto-play speed in milliseconds
        dots: false,        // Show navigation dots
        arrows: true,      // Show navigation arrows
        // useCSS: false,
        responsive: [
            {
                breakpoint: 800,
                settings: {
                    slidesToShow: 2,
                    slidesToScroll: 2
                }
            },
            {
                breakpoint: 550,
                settings: {
                    slidesToShow: 1,
                    slidesToScroll: 1
                }
            }
        ]
    });
});
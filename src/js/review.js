// ==========================================================================
//  Review JavaScript 
// ==========================================================================

////////
// Slick Carousel Plugin
////////

$(document).ready(function(){
    $('.slides').slick({
        slidesToShow: 1,
        dots: true,
        infinite: true,
        speed: 500,
        fade: false,
        cssEase: 'ease',
        arrows: false,
        adaptiveHeight: true
    });
});

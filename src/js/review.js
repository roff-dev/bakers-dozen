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
        fade: true,
        cssEase: 'linear',
        arrows: false,
    });
});

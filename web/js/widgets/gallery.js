$(function () {
    var thumbsGallery = new Swiper(".thumbs-gallery", {
        spaceBetween: 10,
        slidesPerView: 4,
        freeMode: true,
        watchSlidesProgress: true,
        breakpoints: {
            450: {
                slidesPerView: 5,
            },
            650: {
                slidesPerView: 6,
            }
        }
    });

    new Swiper(".thumbs-full", {
        spaceBetween: 0,
        slidesPerView: 1,
        autoHeight: true,
        thumbs: {
            autoScrollOffset: 1,
            swiper: thumbsGallery,
        },
    });

    $('a[data-fancybox="gallery"]').fancybox({
        buttons: ['close']
    });
});
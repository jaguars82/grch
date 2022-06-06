function toggleEntrance(entrance) {
    $(`#entrance-${entrance}`).toggleClass('active');
    $(`#chess-entrance-${entrance}`).slideToggle(100);
}

$('.flat-chess__item .info').on('click', function () {
    $(this).closest('.flat-chess__item').toggleClass('marked').find('.content').stop().slideToggle(300, function () {
        $(this).closest('.flat-chess__item').toggleClass('open');
    });
});
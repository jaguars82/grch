$(function (){
    $('.profile-menu-container .iconed-menu-item').on('click', function () {
        $('.left-sidebar').removeClass('open');
        bodyOverflow.unset();
    });

    $('.left-sidebar-button').on('click', function () {
        $('.left-sidebar').toggleClass('open');
        bodyOverflow.toggle();
    });
});
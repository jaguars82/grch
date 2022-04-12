$(function (){
    function onShow(e) {
        $('#profile-button').addClass('active');
    }

    function onHide(e) {
        $('#profile-button').removeClass('active');
    }

    $("#profile-button").kendoPopover({
        showOn: "click",
        width: "180px",
        position: "bottom",
        body: kendo.template($("#profile-menu").html()),
        show: onShow,
        hide: onHide
    });
});
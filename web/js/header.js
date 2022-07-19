/** Location select */
/* Location list items expand/collapse */
function locationItemToggle(level, id) {
    $(`#${level}-${id}-content`).slideToggle();
    $(`#${level}-${id}-title .arrow`).toggleClass('down');
}

$(function (){
    /** Profile button status */
    function onShow(e) {
        $('#profile-button').addClass('active');
    }

    function onHide(e) {
        $('#profile-button').removeClass('active');
    }

    /** Profile button popover menu */
    $("#profile-button").kendoPopover({
        showOn: "click",
        width: "180px",
        position: "bottom",
        body: kendo.template($("#profile-menu").html()),
        show: onShow,
        hide: onHide
    });
});
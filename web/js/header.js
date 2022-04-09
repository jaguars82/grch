$(function (){
    /*$('#profile-button .avatar').kendoAvatar({
        type: 'image',
        // image: "https://demos.telerik.com/kendo-ui/content/web/Customers/GOURL.jpg"
        // image: "/img/user-nofoto.jpg",
        size: 'large'
    }).removeClass('hidden');*/

    function onShow(e) {
        $('#profile-button').addClass('active');
    }

    function onHide(e) {
        $('#profile-button').removeClass('active');
    }

    $("#profile-button").kendoPopover({
        showOn: "click",
        width: "220px",
        position: "bottom",
        body: kendo.template($("#profile-menu").html()),
        show: onShow,
        hide: onHide
    });
});
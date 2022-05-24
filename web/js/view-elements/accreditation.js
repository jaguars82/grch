$(function (){

    $('.bank-logo-container').each(function(indx, element){
        $(`#${element.id}`).kendoPopover({
            showOn: "click",
            delay: 300,
            width: "180px",
            position: "top",
            body: kendo.template($(`#${element.id}-menu`).html()),
            //show: onShow,
            //hide: onHide
        });
    });
});
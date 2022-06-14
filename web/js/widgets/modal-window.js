/*function buildModal (content_Id, expandButton_Id, closeButton_Id) {
    // init
    $(`#${content_Id}`).kendoWindow({
        actions: ["Close"],
        title: false,
        modal: true,
        height: '98%',
        width: '700px',
        pinned: true,
        visible: false
    });
    // open
    $(`#${expandButton_Id}`).click(function () {
        let win = $(`#${content_Id}`).data("kendoWindow");
        $(`#${content_Id}`).removeClass('hidden');
        win.center().open();
        console.log('rewre');
    });
    // close
    $(`#${closeButton_Id}`).click(function () {
        let win = $(`#${content_Id}`).data("kendoWindow");
        win.close();
    });
}*/

$(document).ready(function () {

    let modals = $('.modal-window');

    modals.each(function(indx, element){
         //console.log(element.dataset.idprefix);
         const prefix = element.dataset.idprefix;

        // init
        $(`#${prefix}-modal`).kendoWindow({
            actions: ["Close"],
            title: false,
            modal: true,
            height: '98%',
            width: '85%',
            pinned: true,
            visible: false
        });

        // open
        $(`#${prefix}-expand`).click(function () {
            let win = $(`#${prefix}-modal`).data("kendoWindow");
            $(`#${prefix}-modal`).removeClass('hidden');
            win.center().open();
        });

        // close
        $(`#${prefix}-close`).click(function () {
            let win = $(`#${prefix}-modal`).data("kendoWindow");
            win.close();
        });

    });

    /*for (let modal in modals) {
        console.log(modal);
    }*/

    /*
    // init
    $("#floor-layout").kendoWindow({
        actions: ["Close"],
        title: false,
        modal: true,
        height: '98%',
        width: '700px',
        pinned: true,
        visible: false
    });

    // open
    $('#expand-floor-layout').click(function () {
        let win = $("#floor-layout").data("kendoWindow");
        $("#floor-layout").removeClass('hidden');
        win.center().open();
    });

    // close
    $('#close-modal-window').click(function () {
        let win = $("#floor-layout").data("kendoWindow");
        win.close();
    });
    */
});
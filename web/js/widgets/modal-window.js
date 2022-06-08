$(document).ready(function () {
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
});
$(function() {
    var index = 0,
        currentEffect;

    $("#thumbs a").click(function(e) {
        e.preventDefault();
        var target = parseInt($(this).data("index"));
        if (target === index) {
            return;
        }

        if (currentEffect) {
            currentEffect.stop();
        }
        $("#area" + target).show();
        currentEffect = kendo.fx("#area" + target).replace("#area" + index, "swap");
        currentEffect.run();
        index = target;
    });
});
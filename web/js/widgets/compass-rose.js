function rotateImage(selector, deg) {
    $(selector).css({
        'transition': 'transform 1s',
        'transform': 'rotate('+ deg +'deg)'
    });
}

$(document).ready(function(){
    // initial state
    let isOrientedToNorth = false;
    let azimuthFlat = $('#compass-rose-flat').attr('data-azimuth');
    azimuthFlat = azimuthFlat < 180 ? azimuthFlat : -(360 - azimuthFlat);

    $('#compass-rose-flat').css({'transform': 'rotate('+ azimuthFlat +'deg)'});

    // align on ccompass rose click
    $('#compass-rose-flat').click(function() {
        if(isOrientedToNorth === false) {
            rotateImage("#flat-layout", azimuthFlat);
            rotateImage("#compass-rose-flat", 0);           
        } else {
            rotateImage("#flat-layout", 0);
            rotateImage("#compass-rose-flat", azimuthFlat);           
        }
        isOrientedToNorth = !isOrientedToNorth;
    });
});
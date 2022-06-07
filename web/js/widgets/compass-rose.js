function rotateImage(selector, deg) {
    $(selector).css({
        'transition': 'transform 1s',
        'transform': 'rotate('+ deg +'deg)'
    });
}

function compassRose(rosetkaID, imageID) {
    let isOrientedToNorth = false;
    
    let azimuthFlat = $(`#${rosetkaID}`).attr('data-azimuth');
    azimuthFlat = azimuthFlat < 180 ? azimuthFlat : -(360 - azimuthFlat);

    $(`#${rosetkaID}`).css({'transform': 'rotate('+ azimuthFlat +'deg)'});

    // change orientation on compass rose click
    $(`#${rosetkaID}`).click(function() {
        if(isOrientedToNorth === false) {
            rotateImage(`#${imageID}`, -azimuthFlat);
            rotateImage(`#${rosetkaID}`, 0);           
        } else {
            rotateImage(`#${imageID}`, 0);
            rotateImage(`#${rosetkaID}`, azimuthFlat);           
        }
        isOrientedToNorth = !isOrientedToNorth;
    });
}

$(document).ready(function(){

    compassRose('compass-rose-flat', 'flat-layout');
    compassRose('compass-rose-entrance', 'entrance-layout');

    /*
    // initial state
    let isOrientedToNorth = false;
    
    let azimuthFlat = $('#compass-rose-flat').attr('data-azimuth');
    azimuthFlat = azimuthFlat < 180 ? azimuthFlat : -(360 - azimuthFlat);

    $('#compass-rose-flat').css({'transform': 'rotate('+ azimuthFlat +'deg)'});

    // change orientation on compass rose click
    $('#compass-rose-flat').click(function() {
        if(isOrientedToNorth === false) {
            rotateImage("#flat-layout", -azimuthFlat);
            rotateImage("#compass-rose-flat", 0);           
        } else {
            rotateImage("#flat-layout", 0);
            rotateImage("#compass-rose-flat", azimuthFlat);           
        }
        isOrientedToNorth = !isOrientedToNorth;
    });
    */
});
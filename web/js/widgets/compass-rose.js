function rotateImage(selector, deg, scale) {
    $(selector).css({
        'transition': 'transform 1s',
        'transform': 'rotate('+ deg +'deg) scale('+ scale +')'
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
            rotateImage(`#${imageID}`, -azimuthFlat, 0.75);
            rotateImage(`#${rosetkaID}`, 0, 1);           
        } else {
            rotateImage(`#${imageID}`, 0, 1);
            rotateImage(`#${rosetkaID}`, azimuthFlat, 1);           
        }
        isOrientedToNorth = !isOrientedToNorth;
    });
}

$(document).ready(function(){

    compassRose('compass-rose-flat', 'flat-layout');
    compassRose('compass-rose-entrance', 'entrance-layout');

});
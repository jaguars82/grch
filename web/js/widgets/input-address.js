$(function () {
    address = $('input[data-form-name]');
    
    $('#' + address.data('form-name')).submit(function() {
        longitudeInput = $('#longitude');
        latitudeInput = $('#latitude');
        if (longitudeInput.val() === '' && latitudeInput.val() === '' && typeof ymaps !== 'undefined') {
            address = $('input[data-form-name]').val();
            if (address !== '') {
                ymaps.geocode(address, {
                    results: 1
                }).then(function (res) { 
                    var coords = res.geoObjects.get(0).geometry.getCoordinates();
                    longitudeInput.val(coords[0]);
                    latitudeInput.val(coords[1]);
                });
            }            
        }
    });
    
    address.change(function () {
        if (typeof ymaps !== 'undefined') {
            $('#longitude').val('');
            $('#latitude').val('');
        }        
    });
});
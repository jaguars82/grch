$(function () {
    $('#region-select').on('change', function (e) {
        fillCities(e.target.value, function () {
            $('#city-select > option').click(function(e) {
                fillDistricts(e.target.value);
            });
        });
    });

    function fillCities(region_id, afterDone = null)
    {
        $.ajaxSetup({
            beforeSend: function(xhr, settings) {
                xhr.setRequestHeader("X-CSRF-Token", $('meta[name="csrf-token"]').attr('content'));
            }
        });

        $.post( "/admin/city/get-for-region?id=" + region_id, function(answer) {
            var citySelect = $('#city-select');
            citySelect.find('option').remove();
            answer.forEach(function (currentValue, index, array) {
                var districtsSelect = $('#district-select');
                districtsSelect.find('option').remove();

                citySelect.append(new Option(currentValue['name'], currentValue['id']));
            });
            
            if (afterDone != null) {
                afterDone();
            }
        })
        .fail(function(answer) {
            alert = $('.alert-template').clone().removeClass('alert-template').addClass('alert-danger');
            processAlert(alert, 'Произошла ошибка. Обратитесь в службу поддержки');
        });
    }
    
    $('#city-select > option').click(function(e) {
        fillDistricts(e.target.value);
    });
    
    selectedCity = $('#city-select > option[selected]');
    if (selectedCity.length) {
        districts = String(selectedCity.data('districts')).split(",");
        fillDistricts(selectedCity.val(), function() {
            $('#district-select > option').each(function (index, option) {
                if (districts.includes($(option).val())) {
                    $(option).attr('selected', '');
                }
            });
        });
    }
    
    function fillDistricts(city_id, afterDone = null) {
        $.ajaxSetup({
            beforeSend: function(xhr, settings) {
                xhr.setRequestHeader("X-CSRF-Token", $('meta[name="csrf-token"]').attr('content'));
            }
        });

        $.post( "/admin/district/get-for-city?id=" + city_id, function(answer) {
            var districtsSelect = $('#district-select');
            districtsSelect.find('option').remove();
            answer.forEach(function (currentValue, index, array) {
                districtsSelect.append(new Option(currentValue['name'], currentValue['id']));
            });
            
            if (afterDone != null) {
                afterDone();
            }
        })
        .fail(function(answer) {
            alert = $('.alert-template').clone().removeClass('alert-template').addClass('alert-danger');
            processAlert(alert, 'Произошла ошибка. Обратитесь в службу поддержки');
        });
    }
});
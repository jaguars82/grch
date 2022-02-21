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

    $('#project-declaration-remove').click(function(e) {
        $(e.target).remove();
        $('#remove_project_declaration').val(0);
    });
    
    $('#newbuildingcomplexform-project_declaration').change(function(e) {
        $('#remove_project_declaration').val(1);
    });
    
    if (typeof ymaps !== 'undefined') {
        ymaps.ready(init);
    }
    var myMap;
    var placemark;

    function init() { 
        var map = $('#map');
        if (map.data("longitude") != "" && map.data("latitude") != "") {
            var longitude = map.data("longitude");
            var latitude = map.data("latitude");
            var zoom = 16;
            var isAddPlacemark = true;
        } else {
            var longitude = 51.6708;
            var latitude = 39.2112;
            var zoom = 9;
            var isAddPlacemark = false;
        }

        myMap = new ymaps.Map('map', {
            center: [longitude, latitude],
            zoom: zoom,
            controls: ['smallMapDefaultSet']
        }, {
            searchControlProvider: 'yandex#search'
        });    

        if (isAddPlacemark) {
            placemark = addPlacemark([longitude, latitude]);
        }

        myMap.events.add('click', function (e) {
            var coords = e.get('coords');

            if (placemark) {
                placemark.geometry.setCoordinates(coords);
            } else {
                placemark = addPlacemark(coords);
            }

            setCoords(coords);
        });

        function addPlacemark(coords)
        {
            placemark = new ymaps.Placemark(coords, {}, {
                preset: 'islands#violetDotIconWithCaption',
                draggable: true
            });
            myMap.geoObjects.add(placemark);
            placemark.events.add('dragend', function () {
                setCoords(placemark.geometry.getCoordinates());
            });

            return placemark;
        }

        function setCoords(coords) {
            ymaps.geocode(coords).then(function (res) {
                var firstGeoObject = res.geoObjects.get(0);
                geoCoords = firstGeoObject.geometry.getCoordinates();
                $('#longitude').val(geoCoords[0]);
                $('#latitude').val(geoCoords[1]);
            });
        }
    }
    
    function changeCoords() {
        var longitude = $('#longitude').val();
        var latitude = $('#latitude').val();
        
        if (longitude != "" && latitude != "") {
            myMap.setCenter([longitude, latitude]);
        } else if (longitude == "" && latitude == "") {
            myMap.setCenter([51.6708, 39.2112]);
            myMap.setZoom(9);
            myMap.geoObjects.remove(placemark);
            placemark = null;
        }
    }
    
    $('#longitude').change(function () {
        changeCoords();
    });
    
    $('#latitude').change(function () {
        changeCoords();
    });


    var bankSelect = $('#banks_select');

    function fillTariffValuesToRow($row, checkEmptyRow) {
        var bankId = $($row).closest('.bank-tariff').data('bank-id');

        var tariffId = $row.find('.list-cell__tariff_id .form-control').val();
        var tariffName = $row.find('input[type="hidden"]');
        var yearlyRateAsPercent = $row.find('.list-cell__yearlyRateAsPercent .form-control');
        var initialFeeRateAsPercent = $row.find('.list-cell__initialFeeRateAsPercent .form-control');
        var payment_period = $row.find('.list-cell__payment_period .form-control');

        if(checkEmptyRow && yearlyRateAsPercent.val() && initialFeeRateAsPercent.val() && payment_period.val()) {
            return;
        }
        
        if(bankTariffs[bankId]) {
            var tariff = getTariff(bankId, tariffId);

            tariffName.val(tariff['name']);
            yearlyRateAsPercent.val(tariff['yearly_rate'] * 100);
            initialFeeRateAsPercent.val(tariff['initial_fee_rate'] * 100);
            payment_period.val(tariff['payment_period']);
        }
    }

    function getTariff(bankId, tariffId) {
        if(typeof bankTariffs[bankId] == 'undefined') {
            return false;
        }

        var tariff = false;

        bankTariffs[bankId].forEach(function (item) {
            if(item.id == tariffId) {
                tariff = item;
            }
        });

        return tariff;
    }

    function showSelectedBankTariffs() {
        if(!bankTariffs) {
            return;
        }
        var selectedBanks = bankSelect.val();

        $('.bank-tariff').each(function () {
            bankId = $(this).data('bank-id').toString();

            if(selectedBanks.indexOf(bankId) === -1) {
                $(this).hide();
            } else {
                $(this).fadeIn(300);
            }
        });
    }

    $('.bank-tariff .multiple-input').on('afterInit', function () {
        var items = $(this).find('.multiple-input-list__item');
        
        if(items.length == 1) {
            item = items.eq(0);

            fillTariffValuesToRow(item, true);
        }
    }).on('afterAddRow', function (e, row, currentIndex) {
        fillTariffValuesToRow(row, false);
    });

    $(document).on('change', '.bank-tariff .list-cell__tariff_id .form-control', function () {
        fillTariffValuesToRow($(this).closest('.multiple-input-list__item'), false);
    });
    
    bankSelect.on('change', function () {
        showSelectedBankTariffs();
    });

    showSelectedBankTariffs();

    $(document).on('click', '.project-declaration-remove', function (e) {
        $.post($(this).data('target'), function( data ) {
            alert = $('.alert-template').clone().removeClass('alert-template').addClass('alert-success');
            processAlert(alert, data['message']);                
            $.pjax.reload({container: '#project-declaration-block'});
        }).fail(function(data) {                
            alert = $('.alert-template').clone().removeClass('alert-template').addClass('alert-danger');
            processAlert(alert, data.responseJSON.message);
        });
    });
});
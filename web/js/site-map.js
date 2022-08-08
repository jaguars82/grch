$(function () {
    function init() {
        myMap = new ymaps.Map('map-content', {
            center: initCoords,
            zoom: initZoom,            
            controls: []//'smallMapDefaultSet']
        }, {
            searchControlProvider: 'yandex#search',
            maxZoom: 17,
            searchControlVisible: false,
        });
        balloonLayout = ymaps.templateLayoutFactory.createClass(
            ' <div class="flat-list-map">' +
                '<div class="scrollbar">' +
                    '$[[options.contentLayout observeSize minWidth=350 maxWidth=350 minHeight=100 maxHeight=400]]' +
                '</div>' +
            ' </div>', 
            {
                build: function () {
                    this.constructor.superclass.build.call(this);
                },
                clear: function () {
                    this.constructor.superclass.clear.call(this);
                },
                onSublayoutSizeChange: function () {
                    balloonLayout.superclass.onSublayoutSizeChange.apply(this, arguments);

                    if(!this._isElement(this._$element)) {
                        return;
                    }
                    this.applyElementOffset();
                    this.events.fire('shapechange');
                },
                applyElementOffset: function () {
                },
                onCloseClick: function (e) {
                    e.preventDefault();
                    this.events.fire('userclose');
                },
                getShape: function () {
                    if(!this._isElement(this._$element)) {
                        return balloonLayout.superclass.getShape.call(this);
                    }
                    var position = this._$element.position();
                    return new ymaps.shape.Rectangle(new ymaps.geometry.pixel.Rectangle([
                        [position.left, position.top], [
                        position.left + this._$element[0].offsetWidth,
                        position.top + this._$element[0].offsetHeight + this._$element.find('.arrow')[0].offsetHeight]
                    ]));
                },
                _isElement: function (element) {
                    return element && element[0] && element.find('.arrow')[0];
                }
            }
        );
        balloonContentLayout = ymaps.templateLayoutFactory.createClass(
            '$[properties.balloonHeader]' +
            '$[properties.balloonContent]'
        );
        
        searchFlats(window.location.href);
    }

    function searchFlats(url) {
        $.get(url, function(data) {
            myMap.geoObjects.removeAll();

            if (data.result && data.result.length) {
                $('.alert-block').fadeOut(200, function () {
                    $(this).remove();
                });

                data.result.forEach((object) => {
                    placemark = new ymaps.Placemark(
                        [object.longitude, object.latitude], {
                            balloonHeader: '',
                            balloonContent: object.html
                        }, {
                            iconLayout: 'default#imageWithContent',
                            iconImageHref: '/img/icons/placemark.svg',
                            iconImageSize: [35, 35],
                            iconImageOffset: [-17.5, -35],
                            balloonShadow: false,
                            balloonLayout: balloonLayout,
                            balloonContentLayout: balloonContentLayout,
                            balloonPanelMaxMapArea: 0,
                            hideIconOnBalloonOpen: false,
                            balloonOffset: [35, 35]
                        }
                    );

                    placemark.events.add('balloonopen', function (e) {
                        myMap.events.add('click', function (e) {
                            if(e.get('target') === myMap) {
                                placemark.balloon.close();
                            }
                        });
                    });

                    myMap.geoObjects.add(placemark);
                });

                boundRect = myMap.geoObjects.getBounds();
                
                myMap.setBounds(boundRect, {
                    checkZoomRange: true, 
                    zoomMargin: 17
                });
            } else {
                myMap.setCenter(initCoords, initZoom);
                isSearch = window.location.href.split('?').length !== 1;
                
                oldAlertBlock = $('.alert-block');
                if (oldAlertBlock.length) {
                    oldAlertBlock.fadeOut(200, function () {
                        oldAlertBlock.remove();
                        if (isSearch) {
                            alertTemplate = $('.not-found-alert-template');
                            alertBlock = alertTemplate.clone().removeClass('not-found-alert-template').addClass('alert-block');
                            alertTemplate.parent().append(alertBlock);
                            alertBlock.fadeIn(200);
                        }
                    });
                } else {
                    if (isSearch) {
                        alertTemplate = $('.not-found-alert-template');
                        alertBlock = alertTemplate.clone().removeClass('not-found-alert-template').addClass('alert-block');
                        alertTemplate.parent().append(alertBlock);
                        alertBlock.fadeIn(200);
                    }
                }
            }

            $('.js-search-filter').removeClass('open');
            bodyOverflow.unset();
        });
    }

    var myMap, balloonLayout, balloonContentLayout;
    var initZoom = 12;

    if($('#map-content').data('longitude') && $('#map-content').data('latitude')) {
        var initCoords = [$('#map-content').data('longitude'), $('#map-content').data('latitude')];
    } else {
        var initCoords = [51.6708, 39.2112];
    }

    if (typeof ymaps !== 'undefined') {
        ymaps.ready(init);
    }

    /** Hide and show filfers panel */
    $('#toggle-mapfilter-panel-button').click(function() {
        $('.search-map--content, .search-map--buttons').toggleClass('fullwidth');
        if ($('.search-map--buttons').hasClass('fullwidth')) {
           $('#toggle-mapfilter-panel-button span').text('arrow_back'); 
        } else {
            $('#toggle-mapfilter-panel-button span').text('arrow_forward');
        }
        
        myMap.container.fitToViewport();
        $('.js-search-filter').toggleClass('collapsed');
    });


    /**
     * FIELDS WITH RANGE OF VALUES
     */

    /** Price Range */

    // initial values ('min' and 'max') for range (TO DO: get these values from DB)
    const rangeForAllMin = 1000000;
    const rangeForAllMax = 35000000;
    const rangeForM2Min = 25000;
    const rangeForM2Max = 250000;

    // 'price for all' range slider
    $("#price-range-for-all").kendoRangeSlider({
        min: rangeForAllMin,
        selectionStart: $('#mapflatsearch-pricefrom').val() && $("#mapflatsearch-pricetype input:checked").val() == 0 ? $('#mapflatsearch-pricefrom').val() : rangeForAllMin,
        max: rangeForAllMax,
        selectionEnd: $('#mapflatsearch-priceto').val() && $("#mapflatsearch-pricetype input:checked").val() == 0 ? $('#mapflatsearch-priceto').val() : rangeForAllMax,
        smallStep: 10000,
        largeStep: 50000,
        tooltip: {
            enabled: false
        },
        change: function (e) {
            $('#mapflatsearch-pricefrom').val(e.value[0]);
            $('#price-from-label').text(kendo.toString(e.value[0], 'n0').replaceAll(',', ' ') + ' ₽');
            $('#mapflatsearch-priceto').val(e.value[1]);
            $('#price-to-label').text(kendo.toString(e.value[1], 'n0').replaceAll(',', ' ') + ' ₽');
        },
        slide: function (e) {
            $('#mapflatsearch-pricefrom').val(e.value[0]);
            $('#price-from-label').text(kendo.toString(e.value[0], 'n0').replaceAll(',', ' ') + ' ₽');
            $('#mapflatsearch-priceto').val(e.value[1]);
            $('#price-to-label').text(kendo.toString(e.value[1], 'n0').replaceAll(',', ' ') + ' ₽');
        },
    });
    let priceRangeForAll = $('#price-range-for-all').getKendoRangeSlider();

    // 'price for m2' range slider 
    $("#price-range-for-m2").kendoRangeSlider({
        min: rangeForM2Min,
        selectionStart: $('#mapflatsearch-pricefrom').val() && $("#mapflatsearch-pricetype input:checked").val() == 1 ? $('#mapflatsearch-pricefrom').val() : rangeForM2Min,
        max: rangeForM2Max,
        selectionEnd: $('#mapflatsearch-priceto').val() && $("#mapflatsearch-pricetype input:checked").val() == 1 ? $('#mapflatsearch-priceto').val() : rangeForM2Max,
        smallStep: 1000,
        largeStep: 5000,
        tooltip: {
            enabled: false
        },
        change: function (e) {
            $('#mapflatsearch-pricefrom').val(e.value[0]);
            $('#price-from-label').text(kendo.toString(e.value[0], 'n0').replaceAll(',', ' ') + ' ₽');
            $('#mapflatsearch-priceto').val(e.value[1]);
            $('#price-to-label').text(kendo.toString(e.value[1], 'n0').replaceAll(',', ' ') + ' ₽');
        },
        slide: function (e) {
            $('#mapflatsearch-pricefrom').val(e.value[0]);
            $('#price-from-label').text(kendo.toString(e.value[0], 'n0').replaceAll(',', ' ') + ' ₽');
            $('#mapflatsearch-priceto').val(e.value[1]);
            $('#price-to-label').text(kendo.toString(e.value[1], 'n0').replaceAll(',', ' ') + ' ₽');
        },
    });
    let priceRangeForM2 = $('#price-range-for-m2').getKendoRangeSlider();

    // hide one of the price sliders according to flat price
    if ($("#mapflatsearch-pricetype input:checked").val() == 0) {
        $("#price-range-for-m2-container").hide();
    } else {
        $("#price-range-for-all-container").hide();
    }

    // 'price for all' - 'price for m2' switch
    $("#mapflatsearch-pricetype input[name='MapFlatSearch[priceType]']").change(function (e) {
        const priceRangeForAllValues = priceRangeForAll.values();
        let minForAllValue = priceRangeForAllValues[0];
        let maxForAllValue = priceRangeForAllValues[1];
        let minForAllLabel = kendo.toString(priceRangeForAllValues[0], 'n0').replaceAll(',', ' ') + ' ₽';
        let maxForAllLabel = kendo.toString(priceRangeForAllValues[1], 'n0').replaceAll(',', ' ') + ' ₽';
        // correct values if all range is used
        if (priceRangeForAllValues[0] == rangeForAllMin && priceRangeForAllValues[1] == rangeForAllMax) {
            minForAllValue = maxForAllValue = '';
            minForAllLabel = maxForAllLabel = '-';
        }

        const priceRangeForM2Values = priceRangeForM2.values();
        let minForM2Value = priceRangeForM2Values[0];
        let maxForM2Value = priceRangeForM2Values[1];
        let minForM2Label = kendo.toString(priceRangeForM2Values[0], 'n0').replaceAll(',', ' ') + ' ₽';
        let maxForM2Label = kendo.toString(priceRangeForM2Values[1], 'n0').replaceAll(',', ' ') + ' ₽';
        // correct values if all range is used
        if (priceRangeForM2Values[0] == rangeForM2Min && priceRangeForM2Values[1] == rangeForM2Max) {
            minForM2Value = maxForM2Value = '';
            minForM2Label = maxForM2Label = '-';
        }

        if(e.target.value == 0) {
            $('#mapflatsearch-pricefrom').val(minForAllValue);
            $('#mapflatsearch-priceto').val(maxForAllValue);
            $('#price-from-label').text(minForAllLabel);
            $('#price-to-label').text(maxForAllLabel);
            $("#price-range-for-m2-container").hide();
            $("#price-range-for-all-container").show();
        }
        if (e.target.value == 1) {
            $('#mapflatsearch-pricefrom').val(minForM2Value);
            $('#mapflatsearch-priceto').val(maxForM2Value);
            $('#price-from-label').text(minForM2Label);
            $('#price-to-label').text(maxForM2Label);
            $("#price-range-for-all-container").hide();
            $("#price-range-for-m2-container").show();
        }
    });

    // reset values
    $('#price-range-for-all-reset').click(function(){
        priceRangeForAll.values(rangeForAllMin, rangeForAllMax);
        $('#price-from-label, #price-to-label').text('-');
        $('#mapflatsearch-pricefrom, #mapflatsearch-priceto').val('');
    });
    $('#price-range-for-m2-reset').click(function(){
        priceRangeForM2.values(rangeForM2Min, rangeForM2Max);
        $('#price-from-label, #price-to-label').text('-');
        $('#mapflatsearch-pricefrom, #mapflatsearch-priceto').val('');
    });

    /** END OF Price Range */


    /** Area Range */

    // initial values ('min' and 'max') for range (TO DO: get these values from DB)
    const rangeAreaMin = 1;
    const rangeAreaMax = 400;

    // 'area' range slider
    $("#area-range").kendoRangeSlider({
        min: rangeAreaMin,
        selectionStart: $('#mapflatsearch-areafrom').val() ? $('#mapflatsearch-areafrom').val() : rangeAreaMin,
        max: rangeAreaMax,
        selectionEnd: $('#mapflatsearch-areato').val() ? $('#mapflatsearch-areato').val() : rangeAreaMax,
        tickPlacement: 'none',
        smallStep: 1,
        largeStep: 5,
        tooltip: {
            enabled: false
        },
        change: function (e) {
            $('#mapflatsearch-areafrom').val(e.value[0]);
            $('#area-from-label').text(kendo.toString(e.value[0], 'n0').replaceAll(',', ' ') + ' м²');
            $('#mapflatsearch-areato').val(e.value[1]);
            $('#area-to-label').text(kendo.toString(e.value[1], 'n0').replaceAll(',', ' ') + ' м²');
        },
        slide: function (e) {
            $('#mapflatsearch-areafrom').val(e.value[0]);
            $('#area-from-label').text(kendo.toString(e.value[0], 'n0').replaceAll(',', ' ') + ' м²');
            $('#mapflatsearch-areato').val(e.value[1]);
            $('#area-to-label').text(kendo.toString(e.value[1], 'n0').replaceAll(',', ' ') + ' м²');
        },
    });
    let areaRange = $('#area-range').getKendoRangeSlider();

    // reset values
    $('#area-range-reset').click(function(){
        areaRange.values(rangeAreaMin, rangeAreaMax);
        $('#area-from-label, #area-to-label').text('-');
        $('#mapflatsearch-areafrom, #mapflatsearch-areato').val('');
    });

    /** END OF Area Range */


    /** Floor Range */

    // initial values ('min' and 'max') for ranges 'floor' and 'total-floor' (TO DO: get these values from DB)
    const rangeFloorMin = 1;
    const rangeFloorMax = 30;

    // 'floor' range slider
    $("#floor-range").kendoRangeSlider({
        min: rangeFloorMin,
        selectionStart: $('#mapflatsearch-floorfrom').val() ? $('#mapflatsearch-floorfrom').val() : rangeFloorMin,
        max: rangeFloorMax,
        selectionEnd: $('#mapflatsearch-floorto').val() ? $('#mapflatsearch-floorto').val() : rangeFloorMax,
        tickPlacement: 'none',
        smallStep: 1,
        largeStep: false,
        tooltip: {
            enabled: false
        },
        change: function (e) {
            $('#mapflatsearch-floorfrom').val(e.value[0]);
            $('#floor-from-label').text(e.value[0]);
            $('#mapflatsearch-floorto').val(e.value[1]);
            $('#floor-to-label').text(e.value[1]);
        },
        slide: function (e) {
            $('#mapflatsearch-floorfrom').val(e.value[0]);
            $('#floor-from-label').text(e.value[0]);
            $('#mapflatsearch-floorto').val(e.value[1]);
            $('#floor-to-label').text(e.value[1]);
        },
    });
    let floorRange = $('#floor-range').getKendoRangeSlider();

    // reset values
    $('#floor-range-reset').click(function(){
        floorRange.values(rangeFloorMin, rangeFloorMax);
        $('#floor-from-label, #floor-to-label').text('-');
        $('#mapflatsearch-floorfrom, #mapflatsearch-floorto').val('');
    });

    /** END OF Floor Range */


    /** Total-Floor Range */

    // 'total-floor' range slider
    $("#total-floor-range").kendoRangeSlider({
        min: rangeFloorMin,
        selectionStart: $('#mapflatsearch-totalfloorfrom').val() ? $('#mapflatsearch-totalfloorfrom').val() : rangeFloorMin,
        max: rangeFloorMax,
        selectionEnd: $('#mapflatsearch-totalfloorto').val() ? $('#mapflatsearch-totalfloorto').val() : rangeFloorMax,
        tickPlacement: 'none',
        smallStep: 1,
        largeStep: false,
        tooltip: {
            enabled: false
        },
        change: function (e) {
            $('#mapflatsearch-totalfloorfrom').val(e.value[0]);
            $('#total-floor-from-label').text(e.value[0]);
            $('#mapflatsearch-totalfloorto').val(e.value[1]);
            $('#total-floor-to-label').text(e.value[1]);
        },
        slide: function (e) {
            $('#mapflatsearch-totalfloorfrom').val(e.value[0]);
            $('#total-floor-from-label').text(e.value[0]);
            $('#mapflatsearch-totalfloorto').val(e.value[1]);
            $('#total-floor-to-label').text(e.value[1]);
        },
    });
    let totaFloorRange = $('#total-floor-range').getKendoRangeSlider();

    // reset values
    $('#total-floor-range-reset').click(function(){
        totaFloorRange.values(rangeFloorMin, rangeFloorMax);
        $('#total-floor-from-label, #total-floor-to-label').text('-');
        $('#mapflatsearch-totalfloorfrom, #mapflatsearch-totalfloorto').val('');
    });

    /** END OF Total-Floor Range */


    /**
     * END OFF FIELDS WITH RANGE OF VALUES
     */


    $('#map-search').submit(function (e) {

        e.preventDefault();

        url = window.location.href.split('?')[0] + '?' + $("#map-search").serialize();

        searchFlats(url);
        window.history.replaceState({}, window.location.title, url);
    });

    $('.js-main-search').on('click', function (e) {
        e.preventDefault();

        var query = $('#map-search')
                        .serialize()
                        .replace(/\MapFlatSearch/g, 'AdvancedFlatSearch');

        location.href = '/site/search?' + query;
    });

    // $('select[name="MapFlatSearch[developer]"]').change(function(e) {
    $('#mapflatsearch-developer').change(function(e) {
        $.post("/newbuilding-complex/get-for-developer?id=" + $(e.target).val(), function(answer) {
            // var newbuildingComplexSelect = $('select[name="MapFlatSearch[newbuilding_complex]"]');
            let newbuildingComplexSelect = $('#mapflatsearch-newbuilding_complex');
            newbuildingComplexSelect.find('option').remove();
            newbuildingComplexSelect.append(new Option('Жилой комплекс', ''));
            answer.forEach(function (currentValue, index, array) {
                newbuildingComplexSelect.append(new Option(currentValue['name'], currentValue['id']));
            });
            // $('select[name="MapFlatSearch[newbuilding_array][]"] option').remove();
            $('#mapflatsearch-newbuilding_array option').remove();
            
            // $('select[name="MapFlatSearch[newbuilding_complex]"]').change(function(e) {
            $('#mapflatsearch-newbuilding_complex').change(function(e) {
                $.post("/newbuilding/get-for-newbuilding-complex?id=" + $(e.target).val(), function(answer) {
                    // var newbuildingSelect = $('select[name="MapFlatSearch[newbuilding_array][]"]');
                    let newbuildingSelect = $('#mapflatsearch-newbuilding_array');
                    newbuildingSelect.find('option').remove();
                    answer.forEach(function (currentValue, index, array) {
                        newbuildingSelect.append(new Option(currentValue['name'], currentValue['id']));
                    });
                })
                .fail(function(answer) {
                    alert = $('.alert-template').clone().removeClass('alert-template').addClass('alert-danger');
                    processAlert(alert, 'Произошла ошибка. Обратитесь в службу поддержки');
                });
            });
        })
        .fail(function(answer) {
            alert = $('.alert-template').clone().removeClass('alert-template').addClass('alert-danger');
            processAlert(alert, 'Произошла ошибка. Обратитесь в службу поддержки');
        });
    });

    $('#region-select').on('change', function (e) {
        fillCities(e.target.value, function () {
            $('#city-select > option').on('click change', function(e) {
                fillDistricts(e.target.value);
            });
            
            $('#city-select').trigger('change');
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
            citySelect.find('option').each(function (index, item) {
                if($(item).val() != '') {
                    $(item).remove();
                }
            });
            answer.forEach(function (currentValue, index, array) {
                var districtsSelect = $('#district-select');
                districtsSelect.find('option').each(function () {
                    if($(this).val() != '') {
                        $(this).remove();
                    }
                });

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

    $('#city-select').on('click change', function(e) {
        fillDistricts(e.target.value);
    });

    function fillDistricts(city_id, afterDone = null) {
        $.ajaxSetup({
            beforeSend: function(xhr, settings) {
                xhr.setRequestHeader("X-CSRF-Token", $('meta[name="csrf-token"]').attr('content'));
            }
        });

        $.post( "/admin/district/get-for-city?id=" + city_id, function(answer) {
            var districtsSelect = $('#district-select');
            districtsSelect.find('option').each(function () {
                if($(this).val() != '') {
                    $(this).remove();
                }
            });
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
    
    $('.js-flat-search').on('click', function () {
        $('.js-search-filter').toggleClass('open');
        bodyOverflow.toggle();
    });
});
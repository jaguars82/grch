$(function () {

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
        selectionStart: $('#advancedflatsearch-pricefrom').val() && $("#advancedflatsearch-pricetype input:checked").val() == 0 ? $('#advancedflatsearch-pricefrom').val() : rangeForAllMin,
        max: rangeForAllMax,
        selectionEnd: $('#advancedflatsearch-priceto').val() && $("#advancedflatsearch-pricetype input:checked").val() == 0 ? $('#advancedflatsearch-priceto').val() : rangeForAllMax,
        tickPlacement: 'none',
        smallStep: 10000,
        largeStep: 50000,
        tooltip: {
            enabled: false
        },
        change: function (e) {
            $('#advancedflatsearch-pricefrom').val(e.value[0]);
            $('#price-from-label').text(kendo.toString(e.value[0], 'n0').replaceAll(',', ' ') + ' ₽');
            $('#advancedflatsearch-priceto').val(e.value[1]);
            $('#price-to-label').text(kendo.toString(e.value[1], 'n0').replaceAll(',', ' ') + ' ₽');
        },
        slide: function (e) {
            $('#advancedflatsearch-pricefrom').val(e.value[0]);
            $('#price-from-label').text(kendo.toString(e.value[0], 'n0').replaceAll(',', ' ') + ' ₽');
            $('#advancedflatsearch-priceto').val(e.value[1]);
            $('#price-to-label').text(kendo.toString(e.value[1], 'n0').replaceAll(',', ' ') + ' ₽');
        },
    });
    let priceRangeForAll = $('#price-range-for-all').getKendoRangeSlider();

    // 'price for m2' range slider 
    $("#price-range-for-m2").kendoRangeSlider({
        min: rangeForM2Min,
        selectionStart: $('#advancedflatsearch-pricefrom').val() && $("#advancedflatsearch-pricetype input:checked").val() == 1 ? $('#advancedflatsearch-pricefrom').val() : rangeForM2Min,
        max: rangeForM2Max,
        selectionEnd: $('#advancedflatsearch-priceto').val() && $("#advancedflatsearch-pricetype input:checked").val() == 1 ? $('#advancedflatsearch-priceto').val() : rangeForM2Max,
        tickPlacement: 'none',
        smallStep: 1000,
        largeStep: 5000,
        tooltip: {
            enabled: false
        },
        change: function (e) {
            $('#advancedflatsearch-pricefrom').val(e.value[0]);
            $('#price-from-label').text(kendo.toString(e.value[0], 'n0').replaceAll(',', ' ') + ' ₽');
            $('#advancedflatsearch-priceto').val(e.value[1]);
            $('#price-to-label').text(kendo.toString(e.value[1], 'n0').replaceAll(',', ' ') + ' ₽');
        },
        slide: function (e) {
            $('#advancedflatsearch-pricefrom').val(e.value[0]);
            $('#price-from-label').text(kendo.toString(e.value[0], 'n0').replaceAll(',', ' ') + ' ₽');
            $('#advancedflatsearch-priceto').val(e.value[1]);
            $('#price-to-label').text(kendo.toString(e.value[1], 'n0').replaceAll(',', ' ') + ' ₽');
        },
    });
    let priceRangeForM2 = $('#price-range-for-m2').getKendoRangeSlider();


    // hide one of the price sliders according to flat price
    if ($("#advancedflatsearch-pricetype input:checked").val() == 0) {
        $("#price-range-for-m2-container").hide();
    } else {
        $("#price-range-for-all-container").hide();
    }

    // 'price for all' - 'price for m2' switch
    $("#advancedflatsearch-pricetype input[name='AdvancedFlatSearch[priceType]']").change(function (e) {
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
        $('#advancedflatsearch-pricefrom, #advancedflatsearch-priceto').val('');
    });
    $('#price-range-for-m2-reset').click(function(){
        priceRangeForM2.values(rangeForM2Min, rangeForM2Max);
        $('#price-from-label, #price-to-label').text('-');
        $('#advancedflatsearch-pricefrom, #advancedflatsearch-priceto').val('');
    });

    /** END OF Price Range */


    /** Area Range */

    // initial values ('min' and 'max') for range (TO DO: get these values from DB)
    const rangeAreaMin = 1;
    const rangeAreaMax = 400;

    // 'area' range slider
    $("#area-range").kendoRangeSlider({
        min: rangeAreaMin,
        selectionStart: $('#advancedflatsearch-areafrom').val() ? $('#advancedflatsearch-areafrom').val() : rangeAreaMin,
        max: rangeAreaMax,
        selectionEnd: $('#advancedflatsearch-areato').val() ? $('#advancedflatsearch-areato').val() : rangeAreaMax,
        tickPlacement: 'none',
        smallStep: 1,
        largeStep: 5,
        tooltip: {
            enabled: false
        },
        change: function (e) {
            $('#advancedflatsearch-areafrom').val(e.value[0]);
            $('#area-from-label').text(kendo.toString(e.value[0], 'n0').replaceAll(',', ' ') + ' м²');
            $('#advancedflatsearch-areato').val(e.value[1]);
            $('#area-to-label').text(kendo.toString(e.value[1], 'n0').replaceAll(',', ' ') + ' м²');
        },
        slide: function (e) {
            $('#advancedflatsearch-areafrom').val(e.value[0]);
            $('#area-from-label').text(kendo.toString(e.value[0], 'n0').replaceAll(',', ' ') + ' м²');
            $('#advancedflatsearch-areato').val(e.value[1]);
            $('#area-to-label').text(kendo.toString(e.value[1], 'n0').replaceAll(',', ' ') + ' м²');
        },
    });
    let areaRange = $('#area-range').getKendoRangeSlider();

    // reset values
    $('#area-range-reset').click(function(){
        areaRange.values(rangeAreaMin, rangeAreaMax);
        $('#area-from-label, #area-to-label').text('-');
        $('#advancedflatsearch-areafrom, #advancedflatsearch-areato').val('');
    });

    /** END OF Area Range */


    /** Floor Range */

    // initial values ('min' and 'max') for ranges 'floor' and 'total-floor' (TO DO: get these values from DB)
    const rangeFloorMin = 1;
    const rangeFloorMax = 30;

    // 'floor' range slider
    $("#floor-range").kendoRangeSlider({
        min: rangeFloorMin,
        selectionStart: $('#advancedflatsearch-floorfrom').val() ? $('#advancedflatsearch-floorfrom').val() : rangeFloorMin,
        max: rangeFloorMax,
        selectionEnd: $('#advancedflatsearch-floorto').val() ? $('#advancedflatsearch-floorto').val() : rangeFloorMax,
        tickPlacement: 'none',
        smallStep: 1,
        largeStep: false,
        tooltip: {
            enabled: false
        },
        change: function (e) {
            $('#advancedflatsearch-floorfrom').val(e.value[0]);
            $('#floor-from-label').text(e.value[0]);
            $('#advancedflatsearch-floorto').val(e.value[1]);
            $('#floor-to-label').text(e.value[1]);
        },
        slide: function (e) {
            $('#advancedflatsearch-floorfrom').val(e.value[0]);
            $('#floor-from-label').text(e.value[0]);
            $('#advancedflatsearch-floorto').val(e.value[1]);
            $('#floor-to-label').text(e.value[1]);
        },
    });
    let floorRange = $('#floor-range').getKendoRangeSlider();

    // reset values
    $('#floor-range-reset').click(function(){
        floorRange.values(rangeFloorMin, rangeFloorMax);
        $('#floor-from-label, #floor-to-label').text('-');
        $('#advancedflatsearch-floorfrom, #advancedflatsearch-floorto').val('');
    });

    /** END OF Floor Range */


    /** Total-Floor Range */

    // 'total-floor' range slider
    $("#total-floor-range").kendoRangeSlider({
        min: rangeFloorMin,
        selectionStart: $('#advancedflatsearch-totalfloorfrom').val() ? $('#advancedflatsearch-totalfloorfrom').val() : rangeFloorMin,
        max: rangeFloorMax,
        selectionEnd: $('#advancedflatsearch-totalfloorto').val() ? $('#advancedflatsearch-totalfloorto').val() : rangeFloorMax,
        tickPlacement: 'none',
        smallStep: 1,
        largeStep: false,
        tooltip: {
            enabled: false
        },
        change: function (e) {
            $('#advancedflatsearch-totalfloorfrom').val(e.value[0]);
            $('#total-floor-from-label').text(e.value[0]);
            $('#advancedflatsearch-totalfloorto').val(e.value[1]);
            $('#total-floor-to-label').text(e.value[1]);
        },
        slide: function (e) {
            $('#advancedflatsearch-totalfloorfrom').val(e.value[0]);
            $('#total-floor-from-label').text(e.value[0]);
            $('#advancedflatsearch-totalfloorto').val(e.value[1]);
            $('#total-floor-to-label').text(e.value[1]);
        },
    });
    let totaFloorRange = $('#total-floor-range').getKendoRangeSlider();

    // reset values
    $('#total-floor-range-reset').click(function(){
        totaFloorRange.values(rangeFloorMin, rangeFloorMax);
        $('#total-floor-from-label, #total-floor-to-label').text('-');
        $('#advancedflatsearch-totalfloorfrom, #advancedflatsearch-totalfloorto').val('');
    });

    /** END OF Total-Floor Range */

    /**
     * END OF FIELDS WITH RANGE OF VALUES
     */


    $('#advanced-search').submit(function (e) {
        e.preventDefault();
        form = $(e.target);

        url = window.location.href.split('?')[0] 
                + '?' 
                + ($('a[href="#list"]').parent().hasClass('active') ? 'list_tab=1' : 'table_tab=1')
                + '&'
                + form.serialize();

      
        $.pjax.reload({url, container: '#search-result'});
        $('html').animate({
            scrollTop: 9 //$('.flat-list-buttons').offset().top
        }, 300);
        
        $('.js-search-filter').removeClass('open');
        bodyOverflow.unset();
    });

    $('.js-map-search').on('click', function (e) {
        e.preventDefault();

        var query = $('#advanced-search')
                        .serialize()
                        .replace(/\AdvancedFlatSearch/g, 'MapFlatSearch');

        location.href = '/site/map?' + query;
    });

    // $('select[name="AdvancedFlatSearch[developer]"]').change(function(e) {
    $('#advancedflatsearch-developer').change(function(e) {
        $.post("/newbuilding-complex/get-for-developer?id=" + $(e.target).val(), function(answer) {
            // var newbuildingComplexSelect = $('select[name="AdvancedFlatSearch[newbuilding_complex]"]');
            let newbuildingComplexSelect = $('#advancedflatsearch-newbuilding_complex');
            newbuildingComplexSelect.find('option').remove();
            newbuildingComplexSelect.append(new Option('Жилой комплекс', ''));
            // console.log(answer);
            answer.forEach(function (currentValue, index, array) {
                newbuildingComplexSelect.append(new Option(currentValue['name'], currentValue['id']));
            });
            // $('select[name="AdvancedFlatSearch[newbuilding_array][]"] option').remove();
            $('#advancedflatsearch-newbuilding_array option').remove();
            
            // $('select[name="AdvancedFlatSearch[newbuilding_complex]"]').change(function(e) {
            $('#advancedflatsearch-newbuilding_complex').change(function(e) {
                $.post("/newbuilding/get-for-newbuilding-complex?id=" + $(e.target).val(), function(answer) {
                    // var newbuildingSelect = $('select[name="AdvancedFlatSearch[newbuilding_array][]"]');
                    let newbuildingSelect = $('#advancedflatsearch-newbuilding_array');
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
    
    $(document).on('pjax:complete', function() {
        if (window.location.href.indexOf('AdvancedFlatSearch') !== -1) {
            $('.site-search-result-count').fadeIn(200);
        }
    });
    
    // $('select[name="AdvancedFlatSearch[newbuilding_array][]"] option').each(function() {
    $('#advancedflatsearch-newbuilding_array option').each(function() {
        if ((window.location.href.indexOf('AdvancedFlatSearch%5Bnewbuilding_array%5D%5B%5D=' + $(this).val()) !== -1)
            || (window.location.href.indexOf('AdvancedFlatSearch[newbuilding_array][]=' + $(this).val()) !== -1)) {
            $(this).attr('selected', true);
        }        
    });
    
    if (window.location.href.indexOf('AdvancedFlatSearch') !== -1) {
        $('.site-search-result-count').fadeIn(200);
    }
    
    $('a[data-toggle="tab"]').on('shown.bs.tab', function (e) {
        queryString = window.location.href.split('?')[1];

        if (e.target.href.indexOf('#list') !== -1) {
            queryString = queryString.replace(/table_tab/, 'list_tab');
            $('#list .pagination a').each(function () {
                $(this).attr('href', $(this).attr('href').replace(/table_tab/, 'list_tab'));
            });
            $('#data-wrap ul.sorter a').each(function () {
                $(this).attr('href', $(this).attr('href').replace(/table_tab/, 'list_tab'));
            });
        } else {
            queryString = queryString.replace(/list_tab/, 'table_tab');
            $('#table .pagination a').each(function () {
                $(this).attr('href', $(this).attr('href').replace(/list_tab/, 'table_tab'));
            });
            $('.grid-view th a').each(function () {
                $(this).attr('href', $(this).attr('href').replace(/list_tab/, 'table_tab'));
            });
        }

        url = window.location.href.split('?')[0] + '?' + queryString;
        
        window.history.replaceState({}, window.location.title, url);
        
        //$.pjax.reload({url, container: '#search-result'});
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

    function updateDistricts()
    {
        selectedCity = $('#city-select > option[selected]');

        if (selectedCity.length) {
            fillDistricts(selectedCity.val());
        }
    } 
    
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
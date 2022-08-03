$(function () {

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
            $('#advancedflatsearch-pricefrom').val(minForAllValue);
            $('#advancedflatsearch-priceto').val(maxForAllValue);
            $('#price-from-label').text(minForAllLabel);
            $('#price-to-label').text(maxForAllLabel);
            $("#price-range-for-m2-container").hide();
            $("#price-range-for-all-container").show();
        }
        if (e.target.value == 1) {
            $('#advancedflatsearch-pricefrom').val(minForM2Value);
            $('#advancedflatsearch-priceto').val(maxForM2Value);
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


    function getNewbuildingComplexes(developer) {
        $.post("/newbuilding-complex/get-for-developer?id=" + developer, function(answer) {
            let newbuildingComplexSelect = $('#advancedflatsearch-newbuilding_complex');
            console.log(newbuildingComplexSelect);
            newbuildingComplexSelect.find('option').remove();
            answer.forEach(function (currentValue, index, array) {
                newbuildingComplexSelect.append(new Option(currentValue['name'], currentValue['id']));
            });
            $('select[name="AdvancedFlatSearch[newbuilding_array][]"] option').remove();
            
            $('select[name="AdvancedFlatSearch[newbuilding_complex]"] > option').click(function(e) {
                $.post("/newbuilding/get-for-newbuilding-complex?id=" + $(e.target).val(), function(answer) {
                    var newbuildingSelect = $('select[name="AdvancedFlatSearch[newbuilding_array][]"]');
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
    }

    $('#advancedflatsearch-developer').change(function(e) {
        getNewbuildingComplexes($(e.target).val());
    });

    $('.js-map-search').on('click', function (e) {
        e.preventDefault();

        var query = $('#main-search')
                        .serialize()
                        .replace(/\AdvancedFlatSearch/g, 'MapFlatSearch');

        location.href = '/site/map?' + query;
    });

    
    $('.hover-accent .hover-accent').removeClass('hover-accent');


    /** News Slider */

    $("#news-slider").kendoScrollView({
        contentHeight: "100%",
    });

    $('.k-scrollview-prev, .k-scrollview-next').remove();

    $('.bage-action').kendoBadge({
        themeColor: 'warning',
        text: 'Акция'
    });

    $('.bage-news').kendoBadge({
        themeColor: 'info',
        text: 'Новость'
    });

    /** END OF News Slider */

});
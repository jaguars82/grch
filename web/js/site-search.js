$(function () {
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

    $('select[name="AdvancedFlatSearch[developer]"]').change(function(e) {
        $.post("/newbuilding-complex/get-for-developer?id=" + $(e.target).val(), function(answer) {
            var newbuildingComplexSelect = $('select[name="AdvancedFlatSearch[newbuilding_complex]"]');
            newbuildingComplexSelect.find('option').remove();
            newbuildingComplexSelect.append(new Option('Жилой комплекс', ''));
            answer.forEach(function (currentValue, index, array) {
                newbuildingComplexSelect.append(new Option(currentValue['name'], currentValue['id']));
            });
            $('select[name="AdvancedFlatSearch[newbuilding_array][]"] option').remove();
            
            $('select[name="AdvancedFlatSearch[newbuilding_complex]"]').change(function(e) {
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
    });
    
    $(document).on('pjax:complete', function() {
        if (window.location.href.indexOf('AdvancedFlatSearch') !== -1) {
            $('.site-search-result-count').fadeIn(200);
        }
    });
    
    $('select[name="AdvancedFlatSearch[newbuilding_array][]"] option').each(function() {
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
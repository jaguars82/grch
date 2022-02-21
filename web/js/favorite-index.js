$(function () {
    function clickHandle(firstContainer, secondContainer = null) {
        return function (e, targetUrl = null) {
            url = (targetUrl) ? targetUrl : $(e.target).closest('a').data('target');

            $.post(url, function( data ) {
                alert = $('.alert-template').clone().removeClass('alert-template').addClass('alert-success');
                processAlert(alert, data['message']);            

                $(firstContainer).fadeOut(200, function () {
                    $.pjax.reload({container: firstContainer});
                });

                if (secondContainer) {
                    $.pjax.reload({container: secondContainer});
                }
            }).fail(function(data) {                
                alert = $('.alert-template').clone().removeClass('alert-template').addClass('alert-danger');
                processAlert(alert, data.responseJSON.message);
            });
        };
    }

    $(document).on('change', '.comment-field', function (e) {
        form = $(e.target).closest('form');
        $.post(form.attr('action'), form.serialize(), function(data) {
            console.log(data);
            
            alert = $('.alert-template').clone().removeClass('alert-template').addClass('alert-success');
            processAlert(alert, data['message']);
        }).fail(function(data) {                
            alert = $('.alert-template').clone().removeClass('alert-template').addClass('alert-danger');
            processAlert(alert, data.responseJSON.message);
        });
    });

    $(document).on('click', '.activate-favorite', clickHandle('#archive-favorites', '#active-favorites'));
    $(document).on('click', '.archive-favorite', clickHandle('#active-favorites', '#archive-favorites'));
    
    $(document).on('click', '.delete-all-archived', function (e) {
        clickHandle('#archive-favorites', '#active-favorites')(e, $(e.target).data('target'));
    });
    
    $(document).on('click', '.delete-favorite', function (e) {
        container = ($("ul.favorite-switch li.active a").attr('href') === '#active') ? '#active-favorites' : '#archive-favorites';
        clickHandle(container)(e);
    });
    
    function setPagination(paginationClass, favorites) {
        $(document)
            .on('click', paginationClass + ' a', function (event) {
                $(favorites).fadeOut(200, function () {
                    $.pjax.reload({'url': $(event.target).attr('href'), container: favorites});
                });

                return false;
            })
            .on('pjax:complete', function() { $(favorites).fadeIn(200); })
    }
    
    setPagination('.active-favorite-pagination', '#active-favorites');
    setPagination('.archive-favorite-pagination', '#archive-favorites');
    
    $('a[data-toggle="tab"]').on('shown.bs.tab', function (e) {
        if (e.target.href.indexOf('#active') !== -1) {
            container = '#active-favorites';
        } else if (e.target.href.indexOf('#archive') !== -1) {
            container = '#archive-favorites';
        }
        
        $.pjax.reload({url: window.location.href.split('?')[0], container: container});
    });
    
    $('#favorite-index-search').submit(function (e) {
        e.preventDefault();
        form = $(e.target);        

        url = form.attr('action') + '?' + form.serialize();
        $('#data-wrap').fadeOut(200, function () {
            if ($('li.active a[data-toggle="tab"]').attr('href').indexOf('#active') !== -1) {
                $.pjax.reload({url, container: '#active-favorites'});
            } else {
                $.pjax.reload({url, container: '#archive-favorites'});
            }
        });
    });
    
    $(document).on('pjax:complete', function() {
        if (window.location.href.indexOf('FavoriteFlatSearch') !== -1) {
            $('.favorite-search-result-count').fadeIn(200);
        }

        $('.js-search-filter').removeClass('open');
        bodyOverflow.unset();
    });

    $('select[name="FavoriteFlatSearch[developer]"]').change(function(e) {
        $.post("/newbuilding-complex/get-for-developer?id=" + $(e.target).val(), function(answer) {
            var newbuildingComplexSelect = $('select[name="FavoriteFlatSearch[newbuilding_complex]"]');
            newbuildingComplexSelect.find('option').remove();
            newbuildingComplexSelect.append(new Option('Жилой комплекс', ''));
            answer.forEach(function (currentValue, index, array) {
                newbuildingComplexSelect.append(new Option(currentValue['name'], currentValue['id']));
            });
        })
        .fail(function(answer) {
            alert = $('.alert-template').clone().removeClass('alert-template').addClass('alert-danger');
            processAlert(alert, 'Произошла ошибка. Обратитесь в службу поддержки');
        });
    });
    
    $('a[data-toggle="tab"]').on('shown.bs.tab', function (e) {
        form = $('#favorite-index-search');
        form.find('input[type=text]').val('');
        form.find('input[type=number]').val('');
        form.find('select').prop('selectedIndex',0);
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
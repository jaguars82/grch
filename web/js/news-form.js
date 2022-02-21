$(function () {        
    $('#developer-select > option').click(function(e) {
        console.log(e);

        fillNewbuildingComplexes(e.target.value);
    });
    
    selectedDeveloper = $('#developer-select > option[selected]');
    console.log(selectedDeveloper);
    
    if (selectedDeveloper.length) {
        newbuildingComplexes = String(selectedDeveloper.data('newbuilding-complexes')).split(",");
        
        fillNewbuildingComplexes(selectedDeveloper.val(), function() {
            $('#newbuilding-complex-select1 > option').each(function (index, option) {
                if (newbuildingComplexes.includes($(option).val())) {
                    $(option).attr('selected', '');
                }
            });
        });
    }    
    
    function fillNewbuildingComplexes(developerId, afterDone = null) {
        $.ajaxSetup({
            beforeSend: function(xhr, settings) {
                xhr.setRequestHeader("X-CSRF-Token", $('meta[name="csrf-token"]').attr('content'));
            }
        });

        $.post( "/newbuilding-complex/get-for-developer?id=" + developerId, function(answer) {
            var newbuildingComplexSelect = $('#newbuilding-complex-select1');
            newbuildingComplexSelect.find('option').remove();
            answer.forEach(function (currentValue, index, array) {
                newbuildingComplexSelect.append(new Option(currentValue['name'], currentValue['id']));
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
    
    function updateActionDataVisibility()
    {
        if($('#newsform-category option:selected').val() == 1) {
            $('#actions-data').css('display', 'block');
            $('.action-is-enabled').prop('checked', true);
        } else {
            $('#actions-data').css('display', 'none');
            $('.action-is-enabled').prop('checked', false);
        }
    }
    
    $('#newsform-category').change(function () {
        updateActionDataVisibility()
    });
       
    //updateActionDataVisibility()
    function searchFlats(e) {
        var $form = $(e.target).parents('.action-flat-search-form');

        var searchData = $form.find('input, select').serialize();

        $.ajax({
            url: '/admin/news/search-flats',
            method: 'POST',
            dataType: 'html',
            data: searchData,
            success: function(data) {
                $('.action-flat-search-result').empty();
                $('.action-flat-search-result').append(data);
            },
            error: function() {
                console.log('error');
            }
        });
    }

    $('.js-search-flats').click(searchFlats);

    $('.action-flat-search-show').click(function(e) {
        e.preventDefault();

        $('.action-flat-search-form').toggle();
    });

    $('.action-flat-search-form #developer-select').on('change', function() {
        fillNewbuildingComplexesActions($(this).val());
    });

    function fillNewbuildingComplexesActions(developerId, afterDone = null) {
        $.ajaxSetup({
            beforeSend: function(xhr, settings) {
                xhr.setRequestHeader("X-CSRF-Token", $('meta[name="csrf-token"]').attr('content'));
            }
        });

        $.post( "/newbuilding-complex/get-for-developer?id=" + developerId, function(answer) {
            var newbuildingComplexSelect = $('#newbuilding-complex-select2');
            newbuildingComplexSelect.find('option').remove();
            answer.forEach(function (currentValue, index, array) {
                newbuildingComplexSelect.append(new Option(currentValue['name'], currentValue['id']));
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

    if ($('.action-flat-search-form #developer-select').val()) {
        fillNewbuildingComplexesActions($('.action-flat-search-form #developer-select').val());
    }
});
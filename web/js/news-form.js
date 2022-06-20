$(function () {        
    
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


    function fillNewbuildingsActions(newbuidingComplexId) {
        $.ajaxSetup({
            beforeSend: function(xhr, settings) {
                xhr.setRequestHeader("X-CSRF-Token", $('meta[name="csrf-token"]').attr('content'));
            }
        });

        $.post("/newbuilding/get-for-newbuilding-complex?id=" + newbuidingComplexId, function(answear) {
            answear.forEach(function (currentValue, index, array) {
                $('#newbuildings-select2').append(new Option(currentValue['name'], currentValue['id']));
            });
        });
    }

    function fillEntrancesActions(MewbuildingId) {
        $.ajaxSetup({
            beforeSend: function(xhr, settings) {
                xhr.setRequestHeader("X-CSRF-Token", $('meta[name="csrf-token"]').attr('content'));
            }
        });

        $.post("/entrance/get-for-newbuilding?id=" + MewbuildingId, function(answear) {
            // console.log(answear);
            answear.forEach(function (currentValue, index, array) {
                $('#entrance-select2').append(new Option(currentValue['name'], currentValue['id']));
            });
        });
    }

    function switchField (activeFieldId, fields) {
        $(`#${activeFieldId}`).change(function(){
            fields.forEach(function(fieldId){
                if (fieldId !== activeFieldId) {
                    $(`#${fieldId}`).val('');
                }
            });
        });
    }
    

    /** switch discount fields */
    const discountFields = ['discount_percent', 'discount_amount', 'discount_price'];
    discountFields.forEach(function(field){
        switchField(field, discountFields);
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

        // fillFloorsForDeveloper(selectedDeveloper.val());
    }


    $('#newsform-category').change(function () {
        updateActionDataVisibility()
    });

    $('.js-search-flats').click(searchFlats);

    $('.action-flat-search-show').click(function(e) {
        e.preventDefault();

        $('.action-flat-search-form').toggle();
    });

    $('.action-flat-search-form #developer-select').on('change', function() {
        $('#newbuildings-select2').find('option').remove();
        $('#entrance-select2').find('option').remove();
        fillNewbuildingComplexesActions($(this).val());
    });

    if ($('.action-flat-search-form #developer-select').val()) {
        fillNewbuildingComplexesActions($('.action-flat-search-form #developer-select').val());
    }

    $('.action-flat-search-form #newbuilding-complex-select2').on('change', function() {
        $('#newbuildings-select2').find('option').remove();
        $('#entrance-select2').find('option').remove();
        $(this).val().forEach(function(newbuldingComplexId) {
            fillNewbuildingsActions(newbuldingComplexId);
        });
    });

    if ($('.action-flat-search-form #newbuilding-complex-select2').val()) {
        fillNewbuildingsActions($('.action-flat-search-form #newbuilding-complex-select2').val());
    }

    $('.action-flat-search-form #newbuildings-select2').on('change', function() {
        $('#entrance-select2').find('option').remove();
        $(this).val().forEach(function(newbuldingId) {
            fillEntrancesActions(newbuldingId);
        });
    });

    if ($('.action-flat-search-form #newbuildings-select2').val()) {
        fillEntrancesActions($('.action-flat-search-form #newbuildings-select2').val());
    }
});
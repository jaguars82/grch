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

    function updateActionDataVisibility() {
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

    function fillEntrancesActions(newbuildingId) {
        $.ajaxSetup({
            beforeSend: function(xhr, settings) {
                xhr.setRequestHeader("X-CSRF-Token", $('meta[name="csrf-token"]').attr('content'));
            }
        });

        $.post("/entrance/get-for-newbuilding?id=" + newbuildingId, function(answear) {
            answear.forEach(function (currentValue, index, array) {
                $('#entrance-select2').append(new Option(currentValue['name'], currentValue['id']));
            });
        });
    }

    function fillRisersActions(entranceId) {
        $.ajaxSetup({
            beforeSend: function(xhr, settings) {
                xhr.setRequestHeader("X-CSRF-Token", $('meta[name="csrf-token"]').attr('content'));
            }
        });

        $.post("/entrance/get-risers-by-entrances?entranceId=" + entranceId, function(answear) {
            const risers = [];
            $("#index-select2 option").each(function() {
                risers.push(Number($(this).val()));
            });
            answear.forEach(function (currentValue, index, array) {
                if (!risers.includes(currentValue)) {
                    $('#index-select2').append(new Option(currentValue));
                }
            });
        });
    }

    function fillFlatsActions(id, handlerUrl) {
        $.ajaxSetup({
            beforeSend: function(xhr, settings) {
                xhr.setRequestHeader("X-CSRF-Token", $('meta[name="csrf-token"]').attr('content'));
            }
        });

        $.post(handlerUrl + id, function(answear) {
            answear.forEach(function (currentValue, index, array) {
                $('#number-select2').append(new Option(currentValue['number']));
            });
        });
    }

    function switchField (activeFieldId, fields) {
        $(`#${activeFieldId}`).change(function(){
            fields.forEach(function(fieldId, index){
                if (fieldId === activeFieldId) {
                    $('#discount_type').val(index);
                } else {
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


    /** switch area fields */
    $('#area-value').change(function(){
        $('#area-from, #area-to').val('');
    });
    $('#area-from, #area-to').change(function(){
        $('#area-value').val('');
    });


    selectedDeveloper = $('#news-developer-select > option[selected]');
    // console.log(selectedDeveloper);
    
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

    $('#news-developer-select').change(function () {
        fillNewbuildingComplexes($(this).val());
    });

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

    /*if ($('.action-flat-search-form #developer-select').val()) {
        fillNewbuildingComplexesActions($('.action-flat-search-form #developer-select').val());
    }*/

    $('.action-flat-search-form #newbuilding-complex-select2').on('change', function() {
        $('#newbuildings-select2').find('option').remove();
        $('#entrance-select2').find('option').remove();
        $(this).val().forEach(function(newbuldingComplexId) {
            fillNewbuildingsActions(newbuldingComplexId);
        });
    });

    /*if ($('.action-flat-search-form #newbuilding-complex-select2').val()) {
        fillNewbuildingsActions($('.action-flat-search-form #newbuilding-complex-select2').val());
    }*/

    $('.action-flat-search-form #newbuildings-select2').on('change', function() {
        $('#entrance-select2, #index-select2, #number-select2').find('option').remove();
        $(this).val().forEach(function(newbuldingId) {
            fillEntrancesActions(newbuldingId);
            fillFlatsActions(newbuldingId, '/newbuilding/get-flats-by-newbuilding?id=');
        });
    });

    /*if ($('.action-flat-search-form #newbuildings-select2').val()) {
        fillEntrancesActions($('.action-flat-search-form #newbuildings-select2').val());
    }*/

    $('.action-flat-search-form #entrance-select2').on('change', function() {
        $('#index-select2, #number-select2').find('option').remove();
        $(this).val().forEach(function(entranceId) {
            fillRisersActions(entranceId);
            fillFlatsActions(entranceId, '/entrance/get-flats-by-entrance?id=');
        });
    });
});
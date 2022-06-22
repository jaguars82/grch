$(function () {
    function getNewbuildingComplexes(developer) {
        $.post("/newbuilding-complex/get-for-developer?id=" + developer, function(answer) {
            var newbuildingComplexSelect = $('select[name="AdvancedFlatSearch[newbuilding_complex]"]');
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

    $('select[name="AdvancedFlatSearch[developer]"]').change(function(e) {
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

});
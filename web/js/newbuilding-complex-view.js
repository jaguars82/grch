$(function () {    
    function clickHandle() {
        return function (e, targetUrl = null) {
            url = (targetUrl) ? targetUrl : $(e.target).parent().data('target');
            $.post(url, function( data ) {
                alert = $('.alert-template').clone().removeClass('alert-template').addClass('alert-success');
                processAlert(alert, data['message']);                
                $.pjax.reload({container: '#newbuilding-complex-view-block'});
            }).fail(function(data) {                
                alert = $('.alert-template').clone().removeClass('alert-template').addClass('alert-danger');
                processAlert(alert, data.responseJSON.message);
            });
        };
    }
    
    function setLoadMoreBtnLink() {
        form = $('#search-newbuilding-flats');
        url = '/site/search?AdvancedFlatSearch[developer]=' + form.data('developer_id') 
            + '&AdvancedFlatSearch[newbuilding_complex]=' + form.data('newbuilding_complex_id');
        queryString = window.location.href.split('?')[1];
        queryString = queryString.replace(/^id=[0-9]+&*/, '');
        
        if (queryString.length) {
            queryString = queryString.replace(/NewbuildingComplexFlatSearch/g, 'AdvancedFlatSearch');
            url = url + '&' + queryString;
        }
        
        $('.all-data-btn').attr('href', url);
    }

    $(document).on('click', '.import-auto', function (e) {
        clickHandle()(e, $(e.target).data('target'));
    });
    
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
    
    $('#search-newbuilding-flats').submit(function (e) {
        e.preventDefault();
        form = $(e.target);
        id = form.data('newbuilding_complex_id');
        url = window.location.href.split('?')[0] + '?id=' + id + '&' + form.serialize();

        $('#flats-data-wrap').fadeOut(200, function () {
            $.pjax.reload({url, container: '#flat-search-result'});
        });
    });
    
    $('#flat-search-clear').click(function (e) {
        e.preventDefault();
        form = $('#search-newbuilding-flats');
        id = form.data('newbuilding_complex_id');
        url = window.location.href.split('?')[0] + '?id=' + id;
        
        form.find('select:not([size])').prop('selectedIndex',0);
        form.find('select[size]').val([]);
        form.find('input[type=text]').val('');
        form.find('input[type=number]').val('');
        form.find('input[type=checkbox]').prop('checked', false);

        $('#flats-data-wrap').fadeOut(200, function () {
            $.pjax.reload({url, container: '#flat-search-result'});
        });
    });
    
    $(document).on('pjax:complete', function() {
        setLoadMoreBtnLink();
    });
});
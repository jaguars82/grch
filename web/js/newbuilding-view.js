$(function () {    
    function setLoadMoreBtnLink() {
        form = $('#search-newbuilding-flats');
        url = '/site/search?AdvancedFlatSearch[developer]=' + [form.data('developer_id')]
            + '&AdvancedFlatSearch[newbuilding_complex]=' + [form.data('newbuilding_complex_id')]
            + '&AdvancedFlatSearch[newbuilding_array]=&AdvancedFlatSearch[newbuilding_array][]=' + form.data('newbuilding_id');
        queryString = window.location.href.split('?')[1];
        queryString = queryString.replace(/^id=[0-9]+&*/, '');
        
        if (queryString.length) {
            queryString = queryString.replace(/NewbuildingFlatSearch/g, 'AdvancedFlatSearch');
            url = url + '&' + queryString;
        }
        
        $('.all-data-btn').attr('href', url);
    }
    
    $('#search-newbuilding-flats').submit(function (e) {
        e.preventDefault();
        form = $(e.target);
        id = form.data('newbuilding_id');
        url = window.location.href.split('?')[0] + '?id=' + id + '&' + form.serialize();

        $('#flats-data-wrap').fadeOut(200, function () {
            $.pjax.reload({url, container: '#flat-search-result'});
        });
    });
    
    $('#flat-search-clear').click(function (e) {
        e.preventDefault();
        form = $('#search-newbuilding-flats');
        id = form.data('newbuilding_id');
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
    
    $(document).on('click', '.floor-layout-view-btn', function (e) {
        e.preventDefault();
    });
    
    $(document).on('click', '.delete-floor-layout', function (e) {
        e.preventDefault();
        target = $(e.target).parent();
        url = target.data('target');
        $.post(url, function( data ) {
            alert = $('.alert-template').clone().removeClass('alert-template').addClass('alert-success');
            processAlert(alert, data['message']);
            
            $('#floor-layouts-wrap').fadeOut(200, function () {
                $.pjax.reload({container: '#floor-layouts-container'});
            });
        }).fail(function(data) {                
            alert = $('.alert-template').clone().removeClass('alert-template').addClass('alert-danger');
            processAlert(alert, data.responseJSON.message);
        });
    });

    $('.layouts-tab--name').on('click', function () {
        var parent = $(this).closest('.layouts-tab');
        parent.toggleClass('open');
        parent.find('.layouts-tab--content').slideToggle(300);
    });
});
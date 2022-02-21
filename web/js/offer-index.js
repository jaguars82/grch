$(document).on('click', '.delete-offer', function (e) {
    url = $(e.target).parent().data('target');
    $.post(url, function( data ) {
        alert = $('.alert-template').clone().removeClass('alert-template').addClass('alert-success');
        processAlert(alert, data['message']);
        
        $('#data-wrap').fadeOut(200, function () {
            $.pjax.reload({container: '#search-result'});
        });
    }).fail(function(data) {                
        alert = $('.alert-template').clone().removeClass('alert-template').addClass('alert-danger');
        processAlert(alert, data.responseJSON.message);
    });
});

$('#form-offer-index').submit(function (e) {
    e.preventDefault();
    form = $(e.target);        

    url = window.location.href.split('?')[0] + '?' + form.serialize();

    $('#data-wrap').fadeOut(200, function () {
        $.pjax.reload({url, container: '#search-result'});
    });
});

$('.offer-index-search-btn').click(function (e) {
    e.preventDefault();
    $('#form-offer-index').submit(); 
});

$('.offer-index-search-clear').click(function (e) {
    e.preventDefault();
    url = window.location.href.split('?')[0];
    form = $('#form-offer-index');
    form.find('input[type=text]').val('');
    form.find('input[type=checkbox]').prop('checked', false);
    form.find('select').prop('selectedIndex',0);
    $('#search-result-count').fadeOut(200);

    $('#data-wrap').fadeOut(200, function () {
        $.pjax.reload({url, container: '#search-result'});
    });  
});

$(document).on('pjax:complete', function() {
    //$('#data-wrap').hide().fadeIn(200);
    if (window.location.href.indexOf('OfferSearch') !== -1)
    {
        $('#search-result-count').fadeIn(200);
    }
});
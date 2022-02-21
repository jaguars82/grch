$(function () {
    $(document).on('click', '.import-auto', function (e) {
        $('#loading-block').fadeIn(200);
        
        $.post($(e.target).data('target'), function( data ) {
            $('#loading-block').fadeOut(200);
            alert = $('.alert-template').clone().removeClass('alert-template').addClass('alert-success');
            processAlert(alert, data['message']);                
            $.pjax.reload({container: '#developer-view-block'});
        }).fail(function(data) {
            $('#loading-block').fadeOut(200);
            alert = $('.alert-template').clone().removeClass('alert-template').addClass('alert-danger');
            processAlert(alert, data.responseJSON.message);
        });
    });
});
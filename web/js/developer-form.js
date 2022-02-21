$(function () {
    $(document).on('click', '.import-auto', function (e) {
        $('.import-auto').attr('disabled', true);

        $.post($(e.target).data('target'), function( data ) {
            $('.import-auto').removeAttr('disabled');
            alert = $('.alert-template').clone().removeClass('alert-template').addClass('alert-success');
            processAlert(alert, data['message']);                
        }).fail(function(data) {
            $('.import-auto').removeAttr('disabled');
            alert = $('.alert-template').clone().removeClass('alert-template').addClass('alert-danger');
            processAlert(alert, data.responseJSON.message);
        });
    });
});
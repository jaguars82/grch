$(function () {    
    function clickHandle(isAddFavorite) {
        return function (e, targetUrl = null) {
            url = (targetUrl) ? targetUrl : $(e.target).parent().data('target');

            $.post(url, function( data ) {
                alert = $('.alert-template').clone().removeClass('alert-template').addClass('alert-success');
                processAlert(alert, data['message']);
                
                if (isAddFavorite) {
                    $('#add-favorite-block').fadeOut(200, function () {
                        $('#delete-favorite-block').fadeIn(200);
                    });                    
                } else {
                    $('#delete-favorite-block').fadeOut(200, function () {
                        $('#add-favorite-block').fadeIn(200);
                    });                    
                }
            }).fail(function(data) {                
                alert = $('.alert-template').clone().removeClass('alert-template').addClass('alert-danger');
                processAlert(alert, data.responseJSON.message);
            });
        };
    }
    
    $('.add-favorite-symbol').click(clickHandle(true));
    $('.delete-favorite-symbol').click(clickHandle());
    
    $('.add-favorite').click(function (e) {        
        clickHandle(true)(e, $(e.target).data('target'));
    });
    
    $('.delete-favorite').click(function (e) {        
        clickHandle()(e, $(e.target).data('target'));
    });
});
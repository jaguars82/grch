$('.js-send-code').click(sendCode);
// $('#login-form').submit();

function sendCode(e) {
    var email = $('.email-otp').val();
    console.log(email);
    $.ajax({
        type: 'POST',
        cache: false,
        dataType: 'json',
        data: {email: email},
        url: '/auth/send-code',
        success: function(data) {
            if (data.error) {
                if (data.error === 'bad_email') {
                    message = 'Неверный email. Попробуйте ещё раз или обратитесь в службу поддержки.';
                } else if (data.error === 'cant_send_code') {
                    message = 'Между отправками кода на email должно пройти определеннное время. Попробуйте ещё раз через некоторое время или обратитесь в службу поддержки.';
                } else {
                    message = 'Произошла ошибка. Обратитесь в службу поддержки.';
                }
                
                alert = $('.alert-template').clone().removeClass('alert-template').addClass('alert-danger');
                processAlert(alert, message);
            }
            else {
                $('.code-otp').show();
                $('.js-otp').hide();
                $('.js-login').show();

                alert = $('.alert-template').clone().removeClass('alert-template').addClass('alert-success');
                processAlert(alert, 'Вам на e-mail отправлено письмо с кодом для входа на сайт');
            }
        }
    });
}

$('.js-delete-object').click(function() {
    var id = $(this).data('id');
    var url = $(this).data('url');

    var elem = $(this);

    $.ajax({
        type: 'POST',
        cache: false,
        dataType: 'json',
        url: url,
        success: function(data) {
            elem.parents('.agency-item').remove();
        }
    });
});

$(function () {    
    $('#is_set_worktime').click(function () {
        $('#worktime').collapse('toggle');
    });
    
    $(document).on('click', '.toggle_worktime', function(e) {
         $(e.target).parents('.item-contact').find('.worktime-block').collapse('toggle');
    });
});

$(function () {    
    $(document)
        .on('click', '.pagination a', function (event) {
            $('#search-result').fadeOut(200, function () {
                $.pjax.reload({'url': $(event.target).attr('href'), container: '#search-result'});
            });

            return false;
        })
        .on('pjax:complete', function() { $('#search-result').fadeIn(200); })
});

function processAlert(alert, message)
{
    clearTimeout(this.autoClose);
    $('.alert-seat .alert').remove();
    $('.alert-seat').append(alert);
    alert.find('.alert-content').html(message);
    alert.fadeIn(500, function () {
        this.autoClose = setTimeout(function () {
            alert.fadeOut(500, function () {
                alert.remove();
            });
        }, 5000);
    });
}

$(function () {
    setTimeout(function () {
        alert = $('#alert-block');
        
        alert.fadeOut(500, function () {
            alert.remove();
        });
    }, 5000);
});

$(function () {
    function deleteContact(e)
    {
        url = $(e.target).parent().data('target');
        $.post(url, function( data ) {
            alert = $('.alert-template').clone().removeClass('alert-template').addClass('alert-success');
            processAlert(alert, data['message']);

            $(e.target).closest('.item-contact').fadeOut(200, function () {
                $.pjax.reload({container: '#contacts-block'});
            });            
        }).fail(function(data) {                
            alert = $('.alert-template').clone().removeClass('alert-template').addClass('alert-danger');
            processAlert(alert, data.responseJSON.message);
        });
        
        return false;
    }
    
    $('.delete-contact').click(function (e) {
        deleteContact(e);
    });
    
    $(document).on('pjax:complete', function() {
        $('.delete-contact').click(function (e) {
            deleteContact(e);
        });
    });
});

$(function () {
    $('#search-form-by-name').submit(function (e) {
        e.preventDefault();
        form = $(e.target);        
        name = form.find('input[type=text]').val();
        
        if (name === '') {
            $('.search-clear').trigger('click');
            return;
        }
        
        url = window.location.href.split('?')[0] + '?' + form.data('form-name') + '[name]=' + name;

        $('#data-wrap').fadeOut(200, function () {
            $.pjax.reload({url, container: '#search-result'});
            $('.search-clear').css('display', 'inline-block');
        });
    });
    
    $('.search-clear').click(function (e) {
        url = window.location.href.split('?')[0];        
        $(e.target).parent().prev().val('');        
        $('#search-result-count').fadeOut(200);
        
        $('#data-wrap').fadeOut(200, function () {
            $.pjax.reload({url, container: '#search-result'});
            $('.search-clear').css('display', 'none');
        });  
    });
    
    $(document).on('pjax:complete', function() {
        //$('#data-wrap').hide().fadeIn(200);
        if (window.location.href.indexOf('[name]=') !== -1) {
            $('#search-result-count').fadeIn(200);
        }
    });
});
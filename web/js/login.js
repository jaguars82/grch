$(function () {
    $('#toggle-to-pass').click(function(){
        $(this).addClass('active');
        $('#toggle-to-code').removeClass('active');
        $('#login-form-code').addClass('hidden');
        $('#login-form-pass').removeClass('hidden');
    });

    $('#toggle-to-code').click(function(){
        $(this).addClass('active');
        $('#toggle-to-pass').removeClass('active');
        $('#login-form-pass').addClass('hidden');
        $('#login-form-code').removeClass('hidden');
    });

    $('#pass-login-button').click(function(){
        console.log('нажатие');
    });
});
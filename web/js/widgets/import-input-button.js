$(function() {
    function initFileInputButton() {
        $("#select-and-commit-archive-btn").click(function(e) {        
            $("#select-archive-file").click();
            e.preventDefault();
            e.stopPropagation();
        });

        
        $("#select-archive-file").change(function(e) {
            var $input = $("#select-archive-file");

            if($input.prop('files').length) {
                $('#archive-submit').show();
            } else {
                $('#archive-submit').hide();
            }
        });

        $('#archive-submit').on('click', function (e) {
            e.preventDefault();
            e.stopPropagation();

            $('#archive-submit').attr('disabled', true);

            var $input = $("#select-archive-file");

            var fd = new FormData($("#load-archive-file-form")[0]);
            fd.append($input.attr('name'), $input.prop('files')[0]);
            form = $("#load-archive-file-form");

            $.ajax({
                url: form.attr('action'),
                data: fd,
                cache: false,
                contentType: false,
                processData: false,
                type: 'POST',
                success: function(data){
                    alert = $('.alert-template').clone().removeClass('alert-template').addClass('alert-success');
                    processAlert(alert, data['message']);
                    
                    $('#archive-submit').attr('disabled', false);
                    $.pjax.reload({container: form.data('container')});
                }
            }).fail(function(data) {
                alert = $('.alert-template').clone().removeClass('alert-template').addClass('alert-danger');
                processAlert(alert, data.responseJSON.message);
                $('#archive-submit').attr('disabled', false);
                $.pjax.reload({container: form.data('container')});
            });
            
            $("#select-archive-file").val('');
        });
    }
    
    $(document).on('pjax:complete', function() {
        if ($("#select-and-commit-archive-btn").attr('data-reinit') !== '') {
            initFileInputButton();
        }
    });
    
    initFileInputButton();
});
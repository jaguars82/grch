$(function() {
    function initFileInputButton() {
        $("#select-and-commit-btn").click(function(e) {        
            $("#select-import-file").click();
            e.preventDefault();
            e.stopPropagation();
        });

        $("#select-import-file").change(function(e) {
            //$("#load-import-file-form").submit();

            var $input = $("#select-import-file");
            var fd = new FormData($("#load-import-file-form")[0]);
            fd.append($input.attr('name'), $input.prop('files')[0]);
            form = $("#load-import-file-form");

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
                    $.pjax.reload({container: form.data('container')});
                }
            }).fail(function(data) {
                alert = $('.alert-template').clone().removeClass('alert-template').addClass('alert-danger');
                processAlert(alert, data.responseJSON.message);
            });
            
            $("#select-import-file").val('');
        });
    }
    
    $(document).on('pjax:complete', function() {
        if ($("#select-and-commit-btn").attr('data-reinit') !== '') {
            initFileInputButton();
        }
    });
    
    initFileInputButton();
});
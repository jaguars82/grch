$(function () {
    $(document).on('change', '.image-input', function(e) {
        var parent = $(this).closest('.form-group').parent();
        img = $(e.target).parent().next().children().get(0);
        var fr = new FileReader();

        fr.readAsDataURL(e.target.files[0]);
        fr.onload = function(e) {
            img.src = this.result;
            parent.find('.close_btn').show();
            parent.find('.is_image_reset').val(0); 
        };
    });

    $('.close_btn').click(function () {
        var parent = $(this).closest('.form-group');
        img = parent.find('img.input-file-img');
        img.attr('src', '/img/noimage.jpg');
        parent.find('.image-input').val('');
        parent.find('.close_btn').hide();
        parent.find('.is_image_reset').val(1);
    });
});

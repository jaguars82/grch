$(function () {   
    $(document).on('change', '.last-image', function() {
        newElement = $('.images').find(">:first-child").clone();
        lastElement = $('.last-image').removeClass('last-image');
        
        if(lastElement.hasClass('first-image')) {
            lastElement.parent().prepend('<button type="button" class="close first-close-btn" title="Удалить изображение"><span>&times;</span></button>');
        } else {
            lastElement.prepend('<button type="button" class="close" title="Удалить изображение"><span>&times;</span></button>');
        }
        
        $('.images').append(newElement);
        newElement.addClass('last-image').css('display', 'block');
    });
    
    $(document).on('change', '.images-input', function(e) {
        img = $(e.target).parent().next().get(0);
        var fr = new FileReader();
        fr.readAsDataURL(e.target.files[0]);
        fr.onload = function(e) {
            img.src = this.result;
        };
    });
    
    $(document).on('click', '.close', function(e) {
        if($(e.target).parent().hasClass('first-close-btn')) {
            $(e.target).parent().parent().parent().remove();
        } else {
            $(e.target).parent().parent().remove();
        }
    });
});
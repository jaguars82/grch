$(function () {    
    $(document).on('change', '.last-file', function() {
        newElement = $('.news-files').find(">:first-child").clone();
        lastElement = $('.last-file').removeClass('last-file');
        
        if(lastElement.hasClass('first-file')) {
            lastElement.parent().prepend('<button type="button" class="close first-close-btn" ><span>&times;</span></button>');
        } else {
            lastElement.prepend('<button type="button" class="close" ><span>&times;</span></button>');
        }
        
        $('.news-files').append(newElement);
        newElement.addClass('last-file').css('display', 'block');
    });
    
    $(document).on('click', '.close', function(e) {
        if($(e.target).parent().hasClass('first-close-btn')) {
            $(e.target).parent().parent().parent().remove();
        } else {
            $(e.target).parent().parent().remove();
        }
    });
});
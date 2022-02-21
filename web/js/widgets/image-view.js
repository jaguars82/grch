$(function () {   
    $(document).on('click', '.photo', function(e) {
        e.preventDefault();
        
        if (typeof(e.target.src) !== 'undefined') {
            url = e.target.src;
        } else if (typeof(e.target.href) !== 'undefined') {
            url = e.target.href;
        } else if (typeof($(e.target).parent().attr('href')) !== 'undefined') {
            url = $(e.target).parent().attr('href');
        } else {
            return;
        }
        
        //if(e.target.src.match(/(uploads|#)$/)) {
        if(url.match(/(uploads|#|\/img\/noimage.jpg)$/)) {
            return;
        }
        
        var azimuth = $(this).data('azimuth');
        if (typeof azimuth !== typeof undefined && azimuth !== false) {
            $('#azimuth-block').show();
            azimuth = azimuth < 180 ? azimuth : -(360 - azimuth);
            $('#azimuth').attr('data-azimuth', azimuth);
            $('#azimuth').rotate(-azimuth);
            img_align_state = true;
            $('#photo-view-img').rotate(0);
        } else {
            $('#azimuth-block').hide();
        }
        
        $('#photo-view').modal();
        //$('#photo-view-img').get(0).src = e.target.src;
        $('#photo-view-img').get(0).src = url;
    });
    
    $('#azimuth').click(function () {
        angle = $('#azimuth').data('azimuth');
        if (img_align_state) {
            $('#azimuth').rotate({angle: -angle, animateTo: 0});
            $('#photo-view-img').rotate({angle: 0, animateTo: angle});
        } else {
            $('#azimuth').rotate({angle: 0, animateTo: -angle});
            $('#photo-view-img').rotate({angle: angle, animateTo: 0});
        }
        img_align_state = !img_align_state;  
    });
});
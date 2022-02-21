$(function () {   
    $(document).on('click', '.floor-layout', function(e) {
        e.preventDefault();
        
        if (typeof($(e.target).data('viewer-id')) !== 'undefined') {
            viewerId = $(e.target).data('viewer-id');
        } else if (typeof($(e.target).parent().data('viewer-id')) !== 'undefined') {
            viewerId = $(e.target).parent().data('viewer-id');
        } else {
            return;
        }
        
        $('#floor-layout-' + viewerId).modal();
        
        setTimeout(function () {
            $('.floor-layout-img').maphilight();
            $('#floor-layout-' + viewerId).scrollTop($(window).height()-100);
        }, 200);
    });
    
    $(document).on('click', '.flat-position-area', function (e) {
        e.preventDefault();
        
        $('.floor-position-input').val($(e.target).data('position'));
        
        var clickedArea = $(this);
        $('map area.flat-position-area').each(function(e) {
            hData = $(this).data('maphilight') || {};
            if ($(this).is(clickedArea)) {
                hData.alwaysOn = true;
                hData.fillColor = $(this).attr('data-color');
                $(this).attr('title', 'Текущая позиция квартиры');
            } else {
                hData.alwaysOn = false;
                hData.fillColor = "0000ff";
                $(this).attr('title', 'Кликните чтобы выбрать позицию квартиры');
            }
            $(this).data('maphilight', hData ).trigger('alwaysOn.maphilight');
        });
    });
    
    $(document).on('click', '.flat-position-area-view', function (e) {
        e.preventDefault();
        $(this).mouseover();
    });
    
    $(document).on('change', '.flat-status', function (e) {
        color = JSON.parse($(this).attr('data-colors'))[parseInt($(".flat-status option:selected").val())];
        $('map area.flat-position-area').attr('data-color', color);
        
        hData = $('.selected-area').data('maphilight');
        hData.fillColor = color;
        $(this).data('maphilight', hData ).trigger('alwaysOn.maphilight');
    });
});
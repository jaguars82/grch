$(function () {
    $(document).on('click', '.news-tab-control--item', function () {
        if($(this).is('.active')) {
            return;
        }

        var tab = $(this).data('tab');
        
        $('.news-tab-content').removeClass('active');
        $('.news-tab-pagination').removeClass('active');
        $('.news-tab-control--item').removeClass('active');

        $('.news-tab-content[data-tab="' + tab + '"]').addClass('active');
        $('.news-tab-pagination[data-tab="' + tab + '"]').addClass('active');
        $(this).addClass('active');

        queryString = window.location.href.split('?')[1];
        if(typeof queryString == 'undefined') {
            queryString = '';
        }
        
        queryString = queryString.replace(/&?(all|news|action)-page=[0-9]+/, '');
        delimeter = queryString.length > 0 ? '&' : '';

        if (tab == 'all') {
            queryString += delimeter + 'all-page=1';
        } else if (tab == 'news') {
            queryString += delimeter + 'news-page=1';
        } else if (tab == 'actions') {
            queryString += delimeter + 'action-page=1';
        }
        
        history.pushState(null, null, window.location.href.split('?')[0] + '?' + queryString);
        // window.location.href = window.location.href.split('?')[0] + '?' + queryString;
    });

    $(document).on('click', '.pagination a', function (e) {
        e.preventDefault();
        url = $(e.target).attr('href');
        $.pjax.reload({url, container: '#news-content'});
    });
});
$(function () {
    $('.paginator').hide();
    
    $(document).on('click', '.load-more-btn', function (e) {
        loadMoreBtn = $(this);
        page = parseInt(loadMoreBtn.attr('data-page'));
        pageCount = parseInt(loadMoreBtn.data('page-count'));
        containerId = loadMoreBtn.data('container-id');
        type = loadMoreBtn.data('type');
        loadingFlag = true;
        loading = loadMoreBtn.next('.loading-img');
        loading.show();
        
        url = window.location.href;
        if (url.indexOf('?') !== -1) {
            url = url + '&' + type + "=" + (page + 1);
        } else {
            url = url + '?' + type + "=" + (page + 1);
        }
        
        $.post(url, function(answer) {
            loadMoreBtn.attr('data-page', ++page);
            loadingFlag = false;
            loading.hide();            
            answer = $(answer).css('display', 'none');
            $('#' + containerId).append(answer);
            answer.fadeIn(1000);
            
            if (page >= pageCount) {
                loadMoreBtn.hide();
            }
        })
        .fail(function(answer) {
            console.log(answer);
        });
         
        return false;
    });
});
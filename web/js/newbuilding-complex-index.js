$('#search-form-newbuilding-index').submit(function (e) {
    e.preventDefault();
    form = $(e.target);        

    url = window.location.href.split('?')[0] + '?' + form.serialize();

    $('#data-wrap').fadeOut(200, function () {
        $.pjax.reload({url, container: '#search-result'});
    });
});

$('.newbuilding-complex-index-search-btn').click(function (e) {
    e.preventDefault();
    $('#search-form-newbuilding-index').submit(); 
});

$('.newbuilding-complex-index-search-clear').click(function (e) {
    e.preventDefault();
    url = window.location.href.split('?')[0];
    form = $('#search-form-newbuilding-index');
    form.find('input[type=text]').val('');
    form.find('input[type=checkbox]').prop('checked', false);
    form.find('select').prop('selectedIndex',0);
    $('#search-result-count').fadeOut(200);

    $('#data-wrap').fadeOut(200, function () {
        $.pjax.reload({url, container: '#search-result'});
    });  
});

function processSearchResultCount() 
{
    if (window.location.href.indexOf('NewbuildingComplexSearch') !== -1)
    {
        $('#search-result-count').fadeIn(200);
    }
}

$(document).on('pjax:complete', function() {
    processSearchResultCount();
});

processSearchResultCount();
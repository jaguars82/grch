$(function () {
    $('.floor-layout-img').maphilight();
    
    $('.edit-price-cash-btn').mouseup(function (e) {
        e.preventDefault();
        $('#edit-price-cash').toggle();
    });
    
    $('#edit-price-cash input').keyup(function (e) {
        if(e.which == 13) {
            $('#edit-price-cash').hide();
        }
        
        value = $('#edit-price-cash input').val().replace(/\s/g, '').replace('.', ',').replace(/(\d)(?=(\d\d\d)+([^\d]|$))/g, '$1 ');        
        newPrice = (value === '') ? '' : value + ' руб.';        
        $('.price-cash-value').text(newPrice);
        $('#price-cash-print .price-value').text(newPrice);
    });
    
    $('#edit-price-cash input').change(function (e) {
        $('input[name=new_price_cash]').val($('#edit-price-cash input').val());
    });
    
    $('.edit-price-credit-btn').mouseup(function (e) {
        e.preventDefault();
        $('#edit-price-credit').toggle();
    });
    
    $('#edit-price-credit input').keyup(function (e) {
        if(e.which == 13) {
            $('#edit-price-credit').hide();
        }
        
        value = $('#edit-price-credit input').val().replace(/\s/g, '').replace('.', ',').replace(/(\d)(?=(\d\d\d)+([^\d]|$))/g, '$1 ');
        newPrice = (value === '') ? '' : value + ' руб.';
        
        $('.price-credit-value').text(newPrice);
        $('#price-credit-print .price-value').text(newPrice);
        
        if (value === '') {
            $('#price-credit-print').hide();
            $('#price-cash-print .title').text('Стоимость');
        } else {
            $('#price-credit-print').show();
            $('#price-cash-print .title').text('Стоимость(Нал.)');
        }
    });
    
    $('#edit-price-credit input').change(function (e) {
        $('input[name=new_price_credit]').val($('#edit-price-credit input').val());
    });
    
    $('.send-email-btn').click(function (e) {
        url = $(e.target).data('target') + "&" + $("#offer-form").serialize();
        $('#loading-block').fadeIn(200);
        $.get(url, function( data ) {
            $('#loading-block').fadeOut(200);
            alert = $('.alert-template').clone().removeClass('alert-template').addClass('alert-success');
            processAlert(alert, data['message']);          
        }).fail(function(data) {
            $('#loading-block').fadeOut(200);
            alert = $('.alert-template').clone().removeClass('alert-template').addClass('alert-danger');
            processAlert(alert, data.responseJSON.message);
        });
    });
    
    $('.telegram-btn').click(function (e) {
        url = $(e.target).data('target') + "&" + $("#offer-form").serialize();
        $('#loading-block').fadeIn(200);
        $.get(url, function( data ) {
            $('#loading-block').fadeOut(200);
            alert = $('.alert-template').clone().removeClass('alert-template').addClass('alert-success');
            processAlert(alert, data['message']);           
        }).fail(function(data) {
            $('#loading-block').fadeOut(200);
            alert = $('.alert-template').clone().removeClass('alert-template').addClass('alert-danger');
            processAlert(alert, data.responseJSON.message);
        });
    });
    
    $('.link-btn').click(function (e) {
        url = $(e.target).data('target');
        $.post(url, $("#offer-form").serialize(), function( data ) {
            link = data['link'];
            $('#link-value').text(link).attr('href', link);
            $('#link-view').modal();
        }).fail(function(data) {                
            alert = $('.alert-template').clone().removeClass('alert-template').addClass('alert-danger');
            processAlert(alert, data.responseJSON.message);
        });
    });
    
    $('.update-offer-btn').click(function (e) {
        url = $(e.target).data('target');
        $.post(url, $("#offer-form").serialize(), function( data ) {
            alert = $('.alert-template').clone().removeClass('alert-template').addClass('alert-success');
            processAlert(alert, data['message']);
        }).fail(function(data) {                
            alert = $('.alert-template').clone().removeClass('alert-template').addClass('alert-danger');
            processAlert(alert, data.responseJSON.message);
        });
    });
    
    /*var saveByteArray = (function () {
        var a = document.createElement("a");
        document.body.appendChild(a);
        a.style = "display: none";
        return function (data, name) {
            var blob = new Blob(data),
                url = window.URL.createObjectURL(blob);
            a.href = url;
            a.download = name;
            a.click();
            window.URL.revokeObjectURL(url);
        };
    }());*/

    $('.download-pdf-btn').click(function (e) {
        $('#loading-block').fadeIn(200);
        url = $(e.target).data('target') + "&" + $("#offer-form").serialize();
        $(this).attr("href", url);
        /*e.preventDefault();
        url = $(e.target).data('target');
        console.log(url);
        $.post(url, $("#offer-form").serialize(), function( data ) {
            saveByteArray([data], 'Коммерческое предложение.pdf');
        }).fail(function(data) {                
            alert = $('.alert-template').clone().removeClass('alert-template').addClass('alert-danger');
            processAlert(alert, data.responseJSON.message);
        });*/
    });
    
    $(window).blur(function () {
        $('#loading-block').fadeOut(200);
    });
    
    $('#edit-price-cash input').val(parseFloat($('.price-cash-value').text().replace(/\s/g, '').replace(',', '.')));
    
    priceCreditValue = parseFloat($('.price-credit-value').text().replace(/\s/g, '').replace(',', '.'));
    if (!isNaN(priceCreditValue)) {
        $('#edit-price-credit input').val(priceCreditValue);
    }
    
    $('.floor-layout-img').css('opacity', '100');
});
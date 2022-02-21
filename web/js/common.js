var bodyOverflow = {
    getScrollbarWidth: function () {
        var div = document.createElement('div');

        div.style.overflowY = 'scroll';
        div.style.width = '50px';
        div.style.height = '50px';

        document.body.append(div);
        var scrollWidth = div.offsetWidth - div.clientWidth;

        div.remove();

        return scrollWidth;
    },
    set: function () {
        var scrollWidth = this.getScrollbarWidth();

        $('body')
            .addClass('block-scroll')
            .css({
                paddingRight: scrollWidth + 'px',
            });
    },
    unset: function () {
        $('body')
            .removeAttr('style')
            .removeClass('block-scroll');
    },
    toggle: function () {
        if($('body').is('.block-scroll')) {
            this.unset();
        } else {
            this.set();
        }
    }
};

$(function () {
    var sticlyElements = $('.sticky');
    Stickyfill.add(sticlyElements);

    $('.main-select select').select2({
        minimumResultsForSearch: -1,
        allowClear: false,
    });
    
    $('.main-select select').on('select2:opening select2:closing', function( event ) {
        var $searchfield = $(this).parent().find('.select2-search__field');
        $searchfield.prop('disabled', true);
    });

    $('.inline-select select').select2({
        minimumResultsForSearch: -1,
        allowClear: false,
        dropdownCssClass: 'inline-dropdown'
    });

    $('.rooms-count-trigger').on('click', function () {
        $(this).parent().toggleClass('open');
    });

    $('.price-select-trigger').on('click', function () {
        $(this).parent().toggleClass('open');
    });

    $(document).on('click', function (e) {
        if(!$(e.target).is('.rooms-count-trigger') && 
            $('.rooms-count-trigger').has(e.target).length == 0 &&
            !$(e.target).is('.rooms-count-dropdown') &&
            $('.rooms-count-dropdown').has(e.target).length == 0) {
                $('.rooms-count').removeClass('open');
        }
        
        if(!$(e.target).is('.price-select-trigger') && 
            $('.price-select-trigger').has(e.target).length == 0 &&
            !$(e.target).is('.price-select-dropdown') &&
            $('.price-select-dropdown').has(e.target).length == 0) {
                $('.price-select').removeClass('open');
        }
    });

    $('.common-slider').each(function (index, item) {
        var that = this;
        var commonSlider = new Swiper(item, {
            slidesPerView: 1,
            spaceBetween: 30,
            loop: true,
            autoHeight: true,
            slidesOffsetBefore: 0,
            grabCursor: true,
            breakpoints: {
                600: {
                    slidesPerView: 2,
                    spaceBetween: 15,
                },
                767: {
                    spaceBetween: 30,
                    slidesPerView: 'auto',
                    slidesOffsetBefore: ($('.common-slider').width() - $('.container').width()) / 2,
                }
            },
            on: {
                afterInit: function (swiper) {
                    if(swiper.currentBreakpoint == "max") {
                        $(that).find('.slider-card').css({
                            height: 'auto'
                        }).matchHeight({
                            remove: 1
                        });
                    } else {
                        $(that).find('.slider-card').matchHeight();
                    }
                    swiper.update();
                },
                beforeResize: function (swiper) {
                    if(swiper.currentBreakpoint == "767") {
                        swiper.params.slidesOffsetBefore = ($('.common-slider').width() - $('.container').width()) / 2;
                    } else {
                        swiper.params.slidesOffsetBefore = 0;
                    }
                },
                breakpoint: function (swiper, breakpoints) {
                    if(swiper.currentBreakpoint == "max") {
                        $(that).find('.slider-card').css({
                            height: 'auto'
                        }).matchHeight({
                            remove: 1
                        });
                    } else {
                        $(that).find('.slider-card').matchHeight();
                    }
                }
            }
        }); 
    });

    $('.toggle-desc--trigger').on('click', function () {
        var parent = $(this).closest('.toggle-desc');
        var content = parent.find('.toggle-desc--content');
        
        if(!$(this).is('.up')) {
            content.animate({
                height: content[0].scrollHeight
            }, 300);
        } else {
            content.animate({
                height: 45
            }, 300);
        }

        parent.toggleClass('open');
        $(this).toggleClass('up');
    });

    $('.toggle-desc').each(function () {
        var trigger = $(this).find('.toggle-desc--trigger');
        var content = $(this).find('.toggle-desc--content');

        if((content[0].scrollHeight - 25) <= content.height()) {
           $(this).addClass('disabled');
        }
    });

    
    $('.document-list').each(function () {
        var items = $(this).find('.document-list--item');
        if(items.length <= 2) {
            $(this).addClass('disabled');
        }
    });

    $(window).on('resize', function () {
        $('.document-list:not(.disabled)').each(function () {
            var content = $(this).find('.document-list--content');
            var items = content.find('.document-list--item');
            content.height(items.eq(0).outerHeight() + items.eq(1).outerHeight());
        });
    });

    $('.document-list--trigger').on('click', function () {
        var parent = $(this).closest('.document-list');
        var content = parent.find('.document-list--content');
        var items = content.find('.document-list--item');

        content.height(content.outerHeight());
        content.find('.document-list--item').css({
            display: 'block'
        });
        
        if(!$(this).is('.up')) {
            content.stop().animate({
                height: content[0].scrollHeight
            }, 300);
        } else {
            content.stop().animate({
                height: items.eq(0).outerHeight() + items.eq(1).outerHeight()
            }, 300);
        }
        $(this).toggleClass('up');
    });


    $('.scrollbar').each(function (index, element) {
        new SimpleBar(element, { autoHide: false });
    });

    $('.responsive-table').each(function (index, element) {
        new SimpleBar(element, { autoHide: false });
    });

    $('.flat-chess__item .info').on('click', function () {
        $(this).closest('.flat-chess__item').toggleClass('marked').find('.content').stop().slideToggle(300, function () {
            $(this).closest('.flat-chess__item').toggleClass('open');
        });
    });

    $('.chess-table .flat-item').on('click', function() {
        location.href = $(this).data('flaturl');
    })

    $('.similar-flat--trigger').on('click', function () {
        var parent = $(this).closest('.similar-flat');
        var content = parent.find('.similar-flat--content');

        parent.toggleClass('dropdown');
        content.stop().slideToggle(300, function () {
            parent.toggleClass('open');
        });
    });

    $('.mobile-menu-icon').on('click', function () {
        $(this).toggleClass('open');
        $('.mobile-menu').stop().slideToggle();
    });
    
    $('.js-news-categories').on('click', function () {
        $('.news-categories').addClass('open');
        bodyOverflow.set();
    });

    $('.news-categories .mobile-close').on('click', function () {
        $('.news-categories').removeClass('open');
        bodyOverflow.unset();
    });

    $('.js-price-format').on('input keyup paste change', function(event) {
        var $input = $(event.target);
        var value = $input.val();
        if (value !== '') {
            var amount = value.replace(/[^\d]/ig, '').replace(/^0/ig, '');
            //amount = String(amount).split( /(?=(?:\d{3})+(?!\d))/ ).join(' ');
            $input.val(amount);
        }
    });

    $(document).on('click', '.btn-favorite', function () {
        var id = $(this).data('flat-id');
        var isAddFavorite = $(this).is('.in');
        var $that = $(this);

        if(isAddFavorite) {
            var url = '/favorite/delete-flat?flatId=' + id;
        } else {
            var url = '/favorite/create?flatId=' + id;
        }

        $.post(url, function( data ) {
            alert = $('.alert-template').clone().removeClass('alert-template').addClass('alert-success');
            processAlert(alert, data['message']);
            
            if (isAddFavorite) {
                $that.removeClass('in');
            } else {
                $that.addClass('in');
            }
        }).fail(function(data) {                
            alert = $('.alert-template').clone().removeClass('alert-template').addClass('alert-danger');
            processAlert(alert, data.responseJSON.message);
        });
    });

    $('.furnish-view').on('click', function () {
        var images = $(this).data('images');
        var desc = $(this).data('desc');
        
        if(!images) {
            return;
        }

        var opts = [];
        images.forEach(function (img) {
            opts.push({
                src: img,
                opts: {
                    caption: desc
                }
            });
        });

        $.fancybox.open(opts, {
            buttons: ['close']
        });
    });

    $('.dropdown-toggle').on('click', function () {
        $(this).parent().toggleClass('open');
    });

    $(document).on('click', function (e) {
        if(!$(e.target).is('.dropdown-toggle') && $('.dropdown-toggle').has(e.target).length == 0 &&
            !$(e.target).is('.dropdown-menu') && $('.dropdown-menu').has(e.target).length == 0) {
                $('.dropdown').removeClass('open');
            }
    });
});

$('.js-send-code').click(sendCode);

function sendCode(e) {
    var email = $('.email-otp').val();
    console.log(email);
    $.ajax({
        type: 'POST',
        cache: false,
        dataType: 'json',
        data: {email: email},
        url: '/auth/send-code',
        success: function(data) {
            if (data.error) {
                if (data.error === 'bad_email') {
                    message = 'Неверный email. Попробуйте ещё раз или обратитесь в службу поддержки.';
                } else if (data.error === 'cant_send_code') {
                    message = 'Между отправками кода на email должно пройти определеннное время. Попробуйте ещё раз через некоторое время или обратитесь в службу поддержки.';
                } else {
                    message = 'Произошла ошибка. Обратитесь в службу поддержки.';
                }
                
                alert = $('.alert-template').clone().removeClass('alert-template').addClass('alert-danger');
                processAlert(alert, message);
            }
            else {
                $('.field-loginform-otp').show();
                $('.js-otp').hide();
                $('.js-login').show();

                alert = $('.alert-template').clone().removeClass('alert-template').addClass('alert-success');
                processAlert(alert, 'Вам на e-mail отправлено письмо с кодом для входа на сайт');
            }
        }
    });
}

$('.js-delete-object').click(function() {
    var id = $(this).data('id');
    var url = $(this).data('url');

    var elem = $(this);

    $.ajax({
        type: 'POST',
        cache: false,
        dataType: 'json',
        url: url,
        success: function(data) {
            elem.parents('.agency-item').remove();
        }
    });
});

$(function () {    
    $('#is_set_worktime').click(function () {
        $('#worktime').collapse('toggle');
    });
    
    $(document).on('click', '.toggle_worktime', function(e) {
         $(e.target).parents('.item-contact').find('.worktime-block').collapse('toggle');
    });
});

$(function () {    
    $(document)
        .on('click', '#search-result .pagination a', function (event) {
            $('#search-result').fadeOut(200, function () {
                $.pjax.reload({'url': $(event.target).attr('href'), container: '#search-result'});
            });

            return false;
        })
        .on('pjax:complete', function() { $('#search-result').fadeIn(200); })
});

function processAlert(alert, message)
{
    clearTimeout(this.autoClose);
    $('.alert-seat .alert').remove();
    $('.alert-seat').append(alert);
    alert.find('.alert-content').html(message);
    alert.fadeIn(500, function () {
        this.autoClose = setTimeout(function () {
            alert.fadeOut(500, function () {
                alert.remove();
            });
        }, 5000);
    });
}

$(function () {
    setTimeout(function () {
        alert = $('#alert-block');
        
        alert.fadeOut(500, function () {
            alert.remove();
        });
    }, 5000);
});

$(function () {
    function deleteContact(e)
    {
        url = $(e.target).parent().data('target');
        $.post(url, function( data ) {
            alert = $('.alert-template').clone().removeClass('alert-template').addClass('alert-success');
            processAlert(alert, data['message']);

            $(e.target).closest('.item-contact').fadeOut(200, function () {
                $.pjax.reload({container: '#contacts-block'});
            });            
        }).fail(function(data) {                
            alert = $('.alert-template').clone().removeClass('alert-template').addClass('alert-danger');
            processAlert(alert, data.responseJSON.message);
        });
        
        return false;
    }
    
    $('.delete-contact').click(function (e) {
        deleteContact(e);
    });
    
    $(document).on('pjax:complete', function() {
        $('.delete-contact').click(function (e) {
            deleteContact(e);
        });
    });

    $('.interactions-tab--name').on('click', function () {
        var parent = $(this).closest('.interactions-tab');
        parent.toggleClass('open');
        parent.find('.interactions-tab--content').stop().slideToggle(300);
    });
});

$(function () {
    $('#search-form-by-name').submit(function (e) {
        e.preventDefault();
        form = $(e.target);        
        name = form.find('input[type=text]').val();
        
        if (name === '') {
            $('.search-clear').trigger('click');
            return;
        }
        
        url = window.location.href.split('?')[0] + '?' + form.data('form-name') + '[name]=' + name;

        $('#data-wrap').fadeOut(200, function () {
            $.pjax.reload({url, container: '#search-result'});
            $('.search-clear').css('display', 'inline-block');
        });
    });
    
    $('.search-clear').click(function (e) {
        url = window.location.href.split('?')[0];        
        $(e.target).parent().prev().val('');        
        $('#search-result-count').fadeOut(200);
        
        $('#data-wrap').fadeOut(200, function () {
            $.pjax.reload({url, container: '#search-result'});
            $('.search-clear').css('display', 'none');
        });  
    });
    
    $(document).on('pjax:complete', function() {
        //$('#data-wrap').hide().fadeIn(200);
        if (window.location.href.indexOf('[name]=') !== -1) {
            $('#search-result-count').fadeIn(200);
        }
    });
});

$(function() {
   $('.worktime-label').click(function() {
       $(this).next('.worktime-block').collapse('toggle');
   });
});
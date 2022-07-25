$(function () {
    function init() {
        myMap = new ymaps.Map('map-content', {
            center: initCoords,
            zoom: initZoom,            
            controls: []//'smallMapDefaultSet']
        }, {
            searchControlProvider: 'yandex#search',
            maxZoom: 17,
            searchControlVisible: false,
        });
        balloonLayout = ymaps.templateLayoutFactory.createClass(
            ' <div class="flat-list-map">' +
                '<div class="scrollbar">' +
                    '$[[options.contentLayout observeSize minWidth=350 maxWidth=350 minHeight=100 maxHeight=400]]' +
                '</div>' +
            ' </div>', 
            {
                build: function () {
                    this.constructor.superclass.build.call(this);
                },
                clear: function () {
                    this.constructor.superclass.clear.call(this);
                },
                onSublayoutSizeChange: function () {
                    balloonLayout.superclass.onSublayoutSizeChange.apply(this, arguments);

                    if(!this._isElement(this._$element)) {
                        return;
                    }
                    this.applyElementOffset();
                    this.events.fire('shapechange');
                },
                applyElementOffset: function () {
                },
                onCloseClick: function (e) {
                    e.preventDefault();
                    this.events.fire('userclose');
                },
                getShape: function () {
                    if(!this._isElement(this._$element)) {
                        return balloonLayout.superclass.getShape.call(this);
                    }
                    var position = this._$element.position();
                    return new ymaps.shape.Rectangle(new ymaps.geometry.pixel.Rectangle([
                        [position.left, position.top], [
                        position.left + this._$element[0].offsetWidth,
                        position.top + this._$element[0].offsetHeight + this._$element.find('.arrow')[0].offsetHeight]
                    ]));
                },
                _isElement: function (element) {
                    return element && element[0] && element.find('.arrow')[0];
                }
            }
        );
        balloonContentLayout = ymaps.templateLayoutFactory.createClass(
            '$[properties.balloonHeader]' +
            '$[properties.balloonContent]'
        );
        
        searchFlats(window.location.href);
    }

    function searchFlats(url) {
        $.get(url, function(data) {
            myMap.geoObjects.removeAll();

            if (data.result && data.result.length) {
                $('.alert-block').fadeOut(200, function () {
                    $(this).remove();
                });

                data.result.forEach((object) => {
                    placemark = new ymaps.Placemark(
                        [object.longitude, object.latitude], {
                            balloonHeader: '',
                            balloonContent: object.html
                        }, {
                            iconLayout: 'default#imageWithContent',
                            iconImageHref: '/img/icons/placemark.svg',
                            iconImageSize: [35, 35],
                            iconImageOffset: [-17.5, -35],
                            balloonShadow: false,
                            balloonLayout: balloonLayout,
                            balloonContentLayout: balloonContentLayout,
                            balloonPanelMaxMapArea: 0,
                            hideIconOnBalloonOpen: false,
                            balloonOffset: [35, 35]
                        }
                    );

                    placemark.events.add('balloonopen', function (e) {
                        myMap.events.add('click', function (e) {
                            if(e.get('target') === myMap) {
                                placemark.balloon.close();
                            }
                        });
                    });

                    myMap.geoObjects.add(placemark);
                });

                boundRect = myMap.geoObjects.getBounds();
                
                myMap.setBounds(boundRect, {
                    checkZoomRange: true, 
                    zoomMargin: 17
                });
            } else {
                myMap.setCenter(initCoords, initZoom);
                isSearch = window.location.href.split('?').length !== 1;
                
                oldAlertBlock = $('.alert-block');
                if (oldAlertBlock.length) {
                    oldAlertBlock.fadeOut(200, function () {
                        oldAlertBlock.remove();
                        if (isSearch) {
                            alertTemplate = $('.not-found-alert-template');
                            alertBlock = alertTemplate.clone().removeClass('not-found-alert-template').addClass('alert-block');
                            alertTemplate.parent().append(alertBlock);
                            alertBlock.fadeIn(200);
                        }
                    });
                } else {
                    if (isSearch) {
                        alertTemplate = $('.not-found-alert-template');
                        alertBlock = alertTemplate.clone().removeClass('not-found-alert-template').addClass('alert-block');
                        alertTemplate.parent().append(alertBlock);
                        alertBlock.fadeIn(200);
                    }
                }
            }

            $('.js-search-filter').removeClass('open');
            bodyOverflow.unset();
        });
    }

    var myMap, balloonLayout, balloonContentLayout;
    var initZoom = 12;

    if($('#map-content').data('longitude') && $('#map-content').data('latitude')) {
        var initCoords = [$('#map-content').data('longitude'), $('#map-content').data('latitude')];
    } else {
        var initCoords = [51.6708, 39.2112];
    }

    if (typeof ymaps !== 'undefined') {
        ymaps.ready(init);
    }

    $('#map-search').submit(function (e) {
        e.preventDefault();

        url = window.location.href.split('?')[0] + '?' + $("#map-search").serialize();

        searchFlats(url);
        window.history.replaceState({}, window.location.title, url);
    });

    $('.js-main-search').on('click', function (e) {
        e.preventDefault();

        var query = $('#map-search')
                        .serialize()
                        .replace(/\MapFlatSearch/g, 'AdvancedFlatSearch');

        location.href = '/site/search?' + query;
    });

    // $('select[name="MapFlatSearch[developer]"]').change(function(e) {
    $('#mapflatsearch-developer').change(function(e) {
        $.post("/newbuilding-complex/get-for-developer?id=" + $(e.target).val(), function(answer) {
            // var newbuildingComplexSelect = $('select[name="MapFlatSearch[newbuilding_complex]"]');
            let newbuildingComplexSelect = $('#mapflatsearch-newbuilding_complex');
            newbuildingComplexSelect.find('option').remove();
            newbuildingComplexSelect.append(new Option('Жилой комплекс', ''));
            answer.forEach(function (currentValue, index, array) {
                newbuildingComplexSelect.append(new Option(currentValue['name'], currentValue['id']));
            });
            // $('select[name="MapFlatSearch[newbuilding_array][]"] option').remove();
            $('#mapflatsearch-newbuilding_array option').remove();
            
            // $('select[name="MapFlatSearch[newbuilding_complex]"]').change(function(e) {
            $('#mapflatsearch-newbuilding_complex').change(function(e) {
                $.post("/newbuilding/get-for-newbuilding-complex?id=" + $(e.target).val(), function(answer) {
                    // var newbuildingSelect = $('select[name="MapFlatSearch[newbuilding_array][]"]');
                    let newbuildingSelect = $('#mapflatsearch-newbuilding_array');
                    newbuildingSelect.find('option').remove();
                    answer.forEach(function (currentValue, index, array) {
                        newbuildingSelect.append(new Option(currentValue['name'], currentValue['id']));
                    });
                })
                .fail(function(answer) {
                    alert = $('.alert-template').clone().removeClass('alert-template').addClass('alert-danger');
                    processAlert(alert, 'Произошла ошибка. Обратитесь в службу поддержки');
                });
            });
        })
        .fail(function(answer) {
            alert = $('.alert-template').clone().removeClass('alert-template').addClass('alert-danger');
            processAlert(alert, 'Произошла ошибка. Обратитесь в службу поддержки');
        });
    });

    $('#region-select').on('change', function (e) {
        fillCities(e.target.value, function () {
            $('#city-select > option').on('click change', function(e) {
                fillDistricts(e.target.value);
            });
            
            $('#city-select').trigger('change');
        });
    });

    function fillCities(region_id, afterDone = null)
    {
        $.ajaxSetup({
            beforeSend: function(xhr, settings) {
                xhr.setRequestHeader("X-CSRF-Token", $('meta[name="csrf-token"]').attr('content'));
            }
        });

        $.post( "/admin/city/get-for-region?id=" + region_id, function(answer) {
            var citySelect = $('#city-select');
            citySelect.find('option').each(function (index, item) {
                if($(item).val() != '') {
                    $(item).remove();
                }
            });
            answer.forEach(function (currentValue, index, array) {
                var districtsSelect = $('#district-select');
                districtsSelect.find('option').each(function () {
                    if($(this).val() != '') {
                        $(this).remove();
                    }
                });

                citySelect.append(new Option(currentValue['name'], currentValue['id']));
            });
            
            if (afterDone != null) {
                afterDone();
            }
        })
        .fail(function(answer) {
            alert = $('.alert-template').clone().removeClass('alert-template').addClass('alert-danger');
            processAlert(alert, 'Произошла ошибка. Обратитесь в службу поддержки');
        });
    }

    $('#city-select').on('click change', function(e) {
        fillDistricts(e.target.value);
    });

    function fillDistricts(city_id, afterDone = null) {
        $.ajaxSetup({
            beforeSend: function(xhr, settings) {
                xhr.setRequestHeader("X-CSRF-Token", $('meta[name="csrf-token"]').attr('content'));
            }
        });

        $.post( "/admin/district/get-for-city?id=" + city_id, function(answer) {
            var districtsSelect = $('#district-select');
            districtsSelect.find('option').each(function () {
                if($(this).val() != '') {
                    $(this).remove();
                }
            });
            answer.forEach(function (currentValue, index, array) {
                districtsSelect.append(new Option(currentValue['name'], currentValue['id']));
            });
            
            if (afterDone != null) {
                afterDone();
            }
        })
        .fail(function(answer) {
            alert = $('.alert-template').clone().removeClass('alert-template').addClass('alert-danger');
            processAlert(alert, 'Произошла ошибка. Обратитесь в службу поддержки');
        });
    }
    
    $('.js-flat-search').on('click', function () {
        $('.js-search-filter').toggleClass('open');
        bodyOverflow.toggle();
    });
});
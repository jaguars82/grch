$(function () {
    if (typeof ymaps !== 'undefined' && $('#map-container').length) {
        ymaps.ready(init);
    }

    function init() {
        var map = $('#map-container');
        var longitude = map.data("longitude");
        var latitude = map.data("latitude");

        myMap = new ymaps.Map('map-container', {
            center: [longitude, latitude],
            zoom: 16,
            controls: ['smallMapDefaultSet']
        }, {
            searchControlProvider: 'yandex#search'
        });

        var myPlacemark = new ymaps.Placemark([longitude, latitude], {}, {
            iconLayout: 'default#imageWithContent',
            iconImageHref: '/img/icons/placemark.svg',
            iconImageSize: [35, 35],
            iconImageOffset: [0, 0],
        });
        myMap.geoObjects.add(myPlacemark);
        myMap.behaviors.disable('scrollZoom');
    }
    
    $('.address-block--trigger').on('click', function () {
        var container = $(this).closest('.address-block');
        container.find('.address-block--map').stop().slideToggle(300);
        container.toggleClass('open');
    });
});
$(function () {  

    function fillRegionDistricts(regionID) {
        $.ajaxSetup({
            beforeSend: function(xhr, settings) {
                xhr.setRequestHeader("X-CSRF-Token", $('meta[name="csrf-token"]').attr('content'));
            }
        });

        $.post("/admin/region-district/get-for-region?id=" + regionID, function(answear) {
            console.log(answear);
            $('#city-region_district_id').find('option').remove();
            answear.forEach(function (currentValue, index, array) {
                $('#city-region_district_id').append(new Option(currentValue['name'], currentValue['id']));
            });
        });
    }

    $('#city-region_id').on('change', function() {
        // console.log($(this).val());
        //$(this).val().forEach(function(id) {
            fillRegionDistricts($(this).val());
        //});
    });

});
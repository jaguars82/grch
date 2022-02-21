$(function () {
    function clearImportData() {
        $('#import-data input[type=text]').val('');
        $('#import-data select option[value=""]').prop('selected', true);
    }
    
    function processInputType(isClean = true) {
        selectedItemValue = $('#import-type option:selected').val();
        if (selectedItemValue == 0) {            
            $('#import-algorithm').fadeOut(700);
            $('#import-algorithm input[type=text]').val('');
            
        } else {
            $('#import-algorithm').fadeIn(700);
        }
        
        if (importTypes.includes(selectedItemValue)) {
            $('#import-data').fadeIn(700);
            if (isClean) {
                clearImportData();
            }
        } else {
            $.when($('#import-data').fadeOut(700)).done(function () {
                clearImportData();
            });            
        }
    }
    
    importTypes = $('#import-type').data('import-types').toString();
    
    if (importTypes.indexOf(',') !== -1) {
        importTypes = importTypes.split(",");
    } else {
        importTypes = [importTypes];
    }

    processInputType(false);
    
    //$('#import-type option').click(function(e) {
    $('#import-type').click(function(e) {
        processInputType();
    });
});
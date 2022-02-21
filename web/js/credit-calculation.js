$(function() {
    if(typeof tariffs == 'undefined') {
        return;
    }


    function setValues()
    {
        var tariffId = $('#bankcalculationform-tariff_id').val();
        var tariff = tariffs[tariffId];

        $('#yearlyRate').val(tariff['yearlyRateAsPercent']);
        $('#initialFee').val(tariff['initialFee']);
    }

    function creditCalculation() {
        cost = $('#cost').val();
        yearlyRate = $('#yearlyRate').val();
        initialFee = $('#initialFee').val();
        time = $('#time').val();
        monthlyRate = yearlyRate/ 12 / 100;
        commonRate = (1 + monthlyRate) ^ time;
        monthlyPayment = (cost - initialFee) * monthlyRate * commonRate / (commonRate - 1);

        if(monthlyPayment < 0) {
            monthlyPayment = 0;
        }

        $('#monthlyPayment').val(monthlyPayment.toFixed(2));
    }

    $('#bankcalculationform-tariff_id').change(function () {
        setValues();
        creditCalculation();
    });
    
    $('#initialFee').change(function () {
        creditCalculation();
    });
    
    $('#time').change(function () {
        creditCalculation();
    });
    
    creditCalculation();
});
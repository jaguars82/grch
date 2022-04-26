/**
 * Refresh messages via pjax
 */
$(document).ready(function() {
    setInterval(function(){
        $('#refreshButton').click();
    }, 5000);
});
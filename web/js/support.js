/**
 * Refresh list of tickets via pjax
 */
$(document).ready(function() {
    setInterval(function(){
        $('#refreshTicketsButton').click();
    }, 5000);
});

/**
 * Refresh messages in chat via pjax
 */
$(document).ready(function() {
    setInterval(function(){
        $('#refresSupportMessageshButton').click();
    }, 5000);
});
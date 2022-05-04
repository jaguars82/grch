/**
 * Refresh indicator for all the events of tickets via pjax
 */
 $(document).ready(function() {
    setInterval(function(){
        $('#refreshEventsButton').click();
    }, 10000);
    setInterval(function(){
        $('#refreshSupportMessagesAmountButton').click();
    }, 10000);
});
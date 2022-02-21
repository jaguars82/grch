$(function () {    
    $('#developer-select').change(function(e) {
        $developerElements = $('#developer-info').children();
        $developerElements.fadeOut(300, function () {
            $developerElements.remove();
        });
        
        $newbuildingComplexElements = $('#newbuilding-complex-info').children();
        $newbuildingComplexElements.fadeOut(300, function () {
            $newbuildingComplexElements.remove();
        });
        
        if (e.target.value != '') {
            fillNewbuildingComplexes(e.target.value);
            setTimeout(function () {                
                getDeveloperContacts(e.target.value);
            }, 300);
        } else {
            $('#newbuilding-complex-select option').remove();
        }
    });
    
    $(document).on('change', '#newbuilding-complex-select', function(e) {
        $newbuildingComplexElements = $('#newbuilding-complex-info').children();
        $newbuildingComplexElements.fadeOut(300, function () {
            $newbuildingComplexElements.remove();
        });
        
        setTimeout(function () {
            if (e.target.value != '') {
                getNewbuildingComplexContacts(e.target.value);
            }
        }, 300);
    });
    
    function fillNewbuildingComplexes(developerId) {
        $.ajaxSetup({
            beforeSend: function(xhr, settings) {
                xhr.setRequestHeader("X-CSRF-Token", $('meta[name="csrf-token"]').attr('content'));
            }
        });

        $.post("/newbuilding-complex/get-for-developer?id=" + developerId, function(answer) {
            var newbuildingComplexSelect = $('#newbuilding-complex-select');
            newbuildingComplexSelect.find('option').remove();
            newbuildingComplexSelect.append(new Option('', ''));
            answer.forEach(function (currentValue, index, array) {
                newbuildingComplexSelect.append(new Option(currentValue['name'], currentValue['id']));
            });
        })
        .fail(function(answer) {
            alert = $('.alert-template').clone().removeClass('alert-template').addClass('alert-danger');
            processAlert(alert, 'Произошла ошибка. Обратитесь в службу поддержки');
        });
    }
    
    function addContact(containerBlock, templateClass, contactData) {
        newElement = $('.' + templateClass).clone().removeClass(templateClass);

        if (contactData['photo'] != null) {
            newElement.find('.contact-no-photo').css('display', 'none');
            newElement.find('.contact-photo')
                    .attr('src', '/uploads/' + contactData['photo'])
                    .css('display', 'inline-block');
        }

        newElement.find('.contact-type').text(contactData['type']);
        newElement.find('.contact-person').text(contactData['person']);
        newElement.find('.contact-phone a').attr('href', 'tel:' + contactData['phone']).text(contactData['phone']);
        containerBlock.append(newElement);
        newElement.fadeIn(700);
    }

    function addStage(containerBlock, stageData)
    {
        var newElement = $('<div></div>');
        newElement.css({
            display: 'none',
            marginBottom: '20px'
        });

        var title = $('<p></p>');
        title.css({
            fontWeight: 500,
            margin: '20px 0px 5px'
        });
        title.text(stageData['name']);
        
        newElement.append(title);

        var description = $('<p></p>');
        description.text(stageData['description']);

        newElement.append(description);

        containerBlock.append(newElement);
        newElement.fadeIn(700);
    }
    
    function getDeveloperContacts(developerId) {
        $.ajaxSetup({
            beforeSend: function(xhr, settings) {
                xhr.setRequestHeader("X-CSRF-Token", $('meta[name="csrf-token"]').attr('content'));
            }
        });

        $.post("/contact/contact/get-for-developer?id=" + developerId, function(answer) {
            developerInfo = $('#developer-info');
           
            answer.forEach(function (currentValue, index, array) {
                addContact(developerInfo, 'developer-contact-template', currentValue);
            });
        })
        .fail(function(answer) {
            alert = $('.alert-template').clone().removeClass('alert-template').addClass('alert-danger');
            processAlert(alert, 'Произошла ошибка. Обратитесь в службу поддержки');
        });
    }
    
    function getNewbuildingComplexContacts(newbuildingComplexId) {
        $.ajaxSetup({
            beforeSend: function(xhr, settings) {
                xhr.setRequestHeader("X-CSRF-Token", $('meta[name="csrf-token"]').attr('content'));
            }
        });

        $.post("/contact/contact/get-for-newbuilding-complex?id=" + newbuildingComplexId, function(answer) {
            newbuildingComplexInfo = $('#newbuilding-complex-info');
            
            algorithmBlock = $('#newbuilding-complex-algorithm-template').clone();
            stages = answer[0]['stages'];
            if(stages.length > 0) {
                stages.forEach(function (stageData) {
                    addStage(algorithmBlock.find('.algorithm-text'), stageData);
                });
            } else {
                algorithmBlock.find('.algorithm-text').text('Не задан');
            }
            newbuildingComplexInfo.append(algorithmBlock);

            delete answer[0];            

            answer.forEach(function (currentValue, index, array) {
                addContact(newbuildingComplexInfo, 'newbuilding-complex-contact-template', currentValue);
            });
            
            algorithmBlock.fadeIn(700);
        })
        .fail(function(answer) {
            alert = $('.alert-template').clone().removeClass('alert-template').addClass('alert-danger');
            processAlert(alert, 'Произошла ошибка. Обратитесь в службу поддержки');
        });
    }
});
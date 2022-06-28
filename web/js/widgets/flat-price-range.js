function handleDiscountsVisibility(id) {
  let isOpened = $(`#discounts-container-${id}`).hasClass('is-opened');

  if (!isOpened) {
    $(`#discounts-container-${id}`).slideDown().addClass('is-opened');
    $(`#price-expand-button-${id}`).addClass('pressed');
  } else {
    $(`#discounts-container-${id}`).slideUp().removeClass('is-opened');
    $(`#price-expand-button-${id}`).removeClass('pressed');
  }

  isOpened = !isOpened;
}

$(function (){

  $('.discount-info-icon').each(function(indx, element){
      $(`#${element.id}`).kendoPopover({
          // showOn: "click",
          // delay: 300,
          width: "350px",
          position: "top",
          body: kendo.template($(`#action-${element.id}`).html()),
          //show: onShow,
          //hide: onHide
      });
  });
});
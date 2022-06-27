function handleDiscountsVisibility(id) {
  let isOpened = $(`#discounts-container-${id}`).hasClass('is-opened');

  if (!isOpened) {
    $(`#discounts-container-${id}`).slideDown().addClass('is-opened');
  } else {
    $(`#discounts-container-${id}`).slideUp().removeClass('is-opened');
  }

  isOpened = !isOpened;
}
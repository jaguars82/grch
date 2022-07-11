function enableDraggable () {
  $('#entrances-list div').draggable({
    containment: "#virtual-structure-form-container",
    revert: "invalid"
  });
}

function enableDroppable () {
  $('.entrance-accept-slot').droppable({
    classes: {
      "ui-droppable-active": "droppable-active"
    },
    drop: function (event, ui) {
      addToPosition(ui.draggable, event.target.dataset.positionindex);
      processData();
    }
  });
}

function addPosition () {
  const existingPositionsAmount = $('.position-container').length;
  const newPositionIndex = existingPositionsAmount + 1;
  const newPositionHTML = `<div class="position-container" data-indexOfPosition="${newPositionIndex}">
  <div class="position-name-container">
      <input id="position-${newPositionIndex}-name" class="position-name form-control" type="text" placeholder="Название позиции" onchange="processData()">
  </div>
  <div class="entrances-container">
      <ul id="entrances-list-position-${newPositionIndex}" class="position-entrances-list">
      </ul>
      <div id="accept-slot-${newPositionIndex}" class="entrance-accept-slot" data-positionIndex="${newPositionIndex}"></div>
  </div>
</div>`
  $("#positions-list").append(newPositionHTML);
  enableDroppable();
}

function addToPosition ($item, positionIndex) {
  $item[0].style = 'cursor: default; user-select: none;';
  const entranceEntry = `<li id="entrance-edit-item-${$item[0].id}" class="entrance-edit-row" data-entranceid="${$item[0].id}"><span class="entrance-edit-row-marker material-icons-outlined">door_sliding</span><div class="inputs-field"><input type="hidden" name="entrance-id[]" value="${$item[0].id}"><input id="entrance-${$item[0].id}-name" type="hidden" name="entrance-name[]" value="${$item[0].innerText}"><input id="entrance-${$item[0].id}-alias" class="form-control" type="text" name="entrance-alias[]" placeholder="Псевдоним подъезда" onchange="processData()"></div>${$item[0].outerHTML}<span id="entrance-edit-item-remove-${$item[0].id}" onclick="removeEntranceItem(${$item[0].id})" class="action-button material-icons-outlined">close</span></li>`;
  $(`#entrances-list-position-${positionIndex}`).append(entranceEntry);
  $item.remove();
}

function removeEntranceItem(id) {
  const itemName = $(`#entrance-${id}-name`).val();
  const backToListEntranceItem = `<div class="entrance-draggable-item" id="${id}">${itemName}</div>`;
  $("#entrances-list").append(backToListEntranceItem);
  enableDraggable();
  $(`#entrance-edit-item-${id}`).remove();
  processData();
}

function processData() {
  let positionIndexes = [];
  
  /** store indexes of positions with entrances to array */
  $('.position-container').each(function(index, elem){
    if ($(`#entrances-list-position-${elem.dataset.indexofposition} li`).length > 0) {
      positionIndexes.push(elem.dataset.indexofposition);
    }
  });

  /** build data structure from not empty positions */
  let dataTree = [];
  if (positionIndexes.length > 0) {
    positionIndexes.forEach(function(positionIndex) {
      const position = {};
      position.name = $(`#position-${positionIndex}-name`).val() == '' ? `Позиция ${positionIndex}` : $(`#position-${positionIndex}-name`).val();
      
      const entrances = [];

      $(`#entrances-list-position-${positionIndex} li`).each(function(index, entranceElem) {
        const alias = $(`#entrance-${entranceElem.dataset.entranceid}-alias`).val() != '' ? $(`#entrance-${entranceElem.dataset.entranceid}-alias`).val() : '';
        entrances.push({ id: entranceElem.dataset.entranceid, alias });
      });

      position.entrances = entrances;

      dataTree.push(position);
    });    
  }

  const jsonData = JSON.stringify(dataTree);

  $('#virtual-structure-input').val(jsonData);

  console.log(jsonData);


}

$(function() {
  
  enableDraggable();
  enableDroppable();

});
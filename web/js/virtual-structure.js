$(function() {
  
  $('#entrances-list li').draggable({
    containment: "#virtual-structure-form",
    revert: "invalid"
  });

  $('.entrances-slot').droppable();

});
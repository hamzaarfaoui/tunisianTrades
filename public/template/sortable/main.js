$( init );

function init() {
  $( ".droppable-area1, .droppable-area2" ).sortable({
        connectWith: ".connected-sortable",
        stack: '.connected-sortable ul',
        receive: function (event, ui) {
            var attr_id = ui.item.attr('data-id');
            if(ui.item.parent( ".isOnIndex" ).attr('data-type') == 'isNotOnIndex'){
                addCategorieOnIndex(attr_id);
            }else if(ui.item.parent( ".isNotOnIndex" ).attr('data-type') == 'isNotOnIndex'){
                removeCategorieFromIndexAction(attr_id);
            }
        }
    }).disableSelection();
}
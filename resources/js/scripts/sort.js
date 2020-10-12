$(document).ready(function() {
    $('#sortable').sortable({
       handle: 'i.icon-move',
       update: function(event, ui) {
          var productOrder = $(this).sortable('toArray').toString();
          console.log(productOrder);
       },
       start: function(event, ui) {
         ui.item.startPos = ui.item.index();
       },
       stop: function(event, ui) {
          console.log(ui.item.id);
          console.log("Start position: " + ui.item.startPos);
          console.log("New position: " + ui.item.index());
 
          $.ajax({
             url : '/nodes/'+ 1 + '/fieldposition',
             type : 'POST',
             data : {
                'startPos' : ui.item.startPos + 1,
                'newPos' : ui.item.index() + 1
             },
             success : function(dataset) {     
                console.log(dataset);
                location.reload(true);
             },
             error : function(request,error)
             {
                console.log("Request: " + JSON.stringify(request));
             }
          });
       }
    });
 });
$(document).ready(function () {

  // variable declaration
  var nodesTable;
  var nodesDataArray = [];
  // datatable initialization
  if ($("#nodes-list-datatable").length > 0) {
    nodesTable = $("#nodes-list-datatable").DataTable({
      responsive: false,
      'columnDefs': [{
        "orderable": false,
        "targets": [0, 1, 5, 6]
      }]
    });
  };
  
  // page nodes list verified filter
  $("#nodes-list-type").on("change", function () {
    var nodesTypeSelect = $("#nodes-list-type").val();
    nodesTable.search(nodesTypeSelect).draw();
  });
  // page nodes list role filter
});
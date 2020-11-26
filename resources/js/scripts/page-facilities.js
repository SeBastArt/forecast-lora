$(document).ready(function () {

  // variable declaration
  var facilitiesTable;
  var facilitiesDataArray = [];
  // datatable initialization
  if ($("#facilities-list-datatable").length > 0) {
    facilitiesTable = $("#facilities-list-datatable").DataTable({
      responsive: false,
      'columnDefs': [{
        "orderable": false,
        "targets": [0, 1, 5, 6]
      }]
    });
  };
  
  // page facilities list verified filter
  $("#facilities-list-name").on("change", function () {
    var facilitiesNameSelect = $("#facilities-list-name").val();
    facilitiesTable.search(facilitiesNameSelect).draw();
  });
  // page facilities list role filter
  $("#facilities-list-location").on("change", function () {
    var facilitiesLocationSelect = $("#facilities-list-location").val();
    // console.log(facilitiesRoleSelect);
    facilitiesTable.search(facilitiesLocationSelect).draw();
  });
});
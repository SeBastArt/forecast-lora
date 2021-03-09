$(document).ready(function () {

  // variable declaration
  var companiesTable;
  // datatable initialization
  if ($("#companies-list-datatable").length > 0) {
    companiesTable = $("#companies-list-datatable").DataTable({
      responsive: true,
      'columnDefs': [{
        "orderable": false,
        "targets": [0, 6]
      }]
    });
  };

  // page companies list city filter
  $("#companies-list-city").on("change", function () {
    var companiesCitySelect = $("#companies-list-city").val();
    companiesTable.search(companiesCitySelect).draw();
  });
  // page companies list country filter
  $("#companies-list-country").on("change", function () {
    var companiesCountrySelect = $("#companies-list-country").val();
    // console.log(companiesRoleSelect);
    companiesTable.search(companiesCountrySelect).draw();
  });
  // page companies list owner filter
  $("#companies-list-owner").on("change", function () {
    var companiesOwnerSelect = $("#companies-list-owner").val();
    // console.log(companiesStatusSelect);
    companiesTable.search(companiesOwnerSelect).draw();
  });
});
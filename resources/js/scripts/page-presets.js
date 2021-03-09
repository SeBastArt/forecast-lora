$(document).ready(function () {

  // variable declaration
  var presetsTable;
  var presetsDataArray = [];
  // datatable initialization
  if ($("#presets-list-datatable").length > 0) {
    presetsTable = $("#presets-list-datatable").DataTable({
      responsive: false,
      'columnDefs': [{
        "orderable": true,
        "targets": [0, 5, 6]
      }]
    });
  };
  // on click selected presets data from table(page named page-presets-list)
  // to store into local storage to get rendered on second page named page-presets-view
  $(document).on("click", "#presets-list-datatable tr", function () {
    $(this).find("td").each(function () {
      presetsDataArray.push($(this).text().trim())
    })

    localStorage.setItem("presetId", presetsDataArray[1]);
    localStorage.setItem("presetsName", presetsDataArray[2]);
  })
  
  // page presets list verified filter
  $("#presets-list-name").on("change", function () {
    var presetsVerifiedSelect = $("#presets-list-name").val();
    presetsTable.search(presetsVerifiedSelect).draw();
  });
});
$(document).ready(function () {

  // variable declaration
  var usersTable;
  var usersDataArray = [];
  // datatable initialization
  if ($("#users-list-datatable").length > 0) {
    usersTable = $("#users-list-datatable").DataTable({
      responsive: true,
      'columnDefs': [{
        "orderable": false,
        "targets": [0, 7, 8, 9]
      }]
    });
  };
  // on click selected users data from table(page named page-users-list)
  // to store into local storage to get rendered on second page named page-users-view
  $(document).on("click", "#users-list-datatable tr", function () {
    $(this).find("td").each(function () {
      usersDataArray.push($(this).text().trim())
    })

    localStorage.setItem("usersId", usersDataArray[1]);
    localStorage.setItem("usersName", usersDataArray[2]);
    localStorage.setItem("usersEmail", usersDataArray[3]);
    localStorage.setItem("usersVerified", usersDataArray[5]);
    localStorage.setItem("usersRole", usersDataArray[6]);
    localStorage.setItem("usersStatus", usersDataArray[7]);
  })
  
  // page users list verified filter
  $("#users-list-verified").on("change", function () {
    var usersVerifiedSelect = $("#users-list-verified").val();
    usersTable.search(usersVerifiedSelect).draw();
  });
  // page users list role filter
  $("#users-list-role").on("change", function () {
    var usersRoleSelect = $("#users-list-role").val();
    // console.log(usersRoleSelect);
    usersTable.search(usersRoleSelect).draw();
  });
  // page users list status filter
  $("#users-list-status").on("change", function () {
    var usersStatusSelect = $("#users-list-status").val();
    // console.log(usersStatusSelect);
    usersTable.search(usersStatusSelect).draw();
  });
  // users language select
  if ($("#users-language-select2").length > 0) {
    $("#users-language-select2").select2({
      dropdownAutoWidth: true,
      width: '100%'
    });
  }
  // users music select
  if ($("#users-music-select2").length > 0) {
    $("#users-music-select2").select2({
      dropdownAutoWidth: true,
      width: '100%'
    });
  }
  // users movies select
  if ($("#users-movies-select2").length > 0) {
    $("#users-movies-select2").select2({
      dropdownAutoWidth: true,
      width: '100%'
    });
  }

  // Input, Select, Textarea validations except submit button validation initialization
  if ($(".users-edit").length > 0) {
    $("#accountForm, #infotabForm").validate({
      rules: {
        username: {
          required: true,
          minlength: 5
        },
        name: {
          required: true
        },
        email: {
          required: true
        },
        datepicker: {
          required: true
        },
        address: {
          required: true
        }
      },
      errorElement: 'div'
    });
    $("#infotabForm").validate({
      rules: {
        datepicker: {
          required: true
        },
        address: {
          required: true
        }
      },
      errorElement: 'div'
    });
  }
});
$.ajaxSetup({
    headers: {
        'X-CSRF-TOKEN': $('meta[name="csrf-token"]').attr('content')
    }
});

function confirmDelete(slug) {
  swal({
    title: "Are you sure?",
    text: "You will not be able to recover this item!",
    icon: 'warning',
    dangerMode: true,
    buttons: {
      cancel: 'No, Please!',
      delete: 'Yes, Delete It'
    }
  }).then(function (willDelete) {
    if (willDelete) {
      $.ajax({
        url: slug,
        type: "POST",
        data: {
            '_method': 'DELETE'
        },
        success: function (data) {
          swal("Finish! Your item has been deleted!", {
            icon: "success",}
          ).then(function (willDelete) {
            location.reload();
          });
        },
        error: function (data) {
          console.log(data);
          swal({
              title: 'Opps... ',
              text: data.responseText,
              icon: 'error',
              timer: '5000'
          })
        }
      })
    } else {
      swal("Your item file is safe", {
        title: 'Cancelled',
        icon: "error",
      });
    }
  });
}
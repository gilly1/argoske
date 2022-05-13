$(document).ready(function () {
      
    $('html #listDataTable').on('submit', 'form.myAction', function (e) {
          e.preventDefault();
          var that = this;
          
          Swal.fire({
              title: 'Are you sure?',
              text: "You won't be able to revert this!",
              type: 'warning',
              showCancelButton: true,
              confirmButtonColor: '#3085d6',
              cancelButtonColor: '#d33',
              confirmButtonText: 'Yes, delete it!',
              closeOnConfirm:false
          }).then((result) => {
              if (result.value) {
                that.submit();
                Swal.fire(
                  'Deleted!',
                  'Your file has been deleted.',
                  'success'
                )
              }
          });
      });

  });
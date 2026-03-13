document.addEventListener('DOMContentLoaded', function () {
  window.cancelBooking = function (id, param, url) {
    Swal.fire({
      title: '',
      text: 'Bạn có chắc chắn muốn hủy lịch đặt không ?',
      type: 'warning',
      showCancelButton: true,
      confirmButtonColor: '#d33',
      cancelButtonColor: '#3085d6',
      confirmButtonText: 'Có',
      cancelButtonText: 'Không',
    }).then(result => {
      if (result.value) {
        main_layout.show_loader()
        $.ajax({
          url: url,
          type: 'DELETE',
          headers: headersClient,
          data: {
            _token: main.token,
          },
          success: function (result) {
            main_layout.hide_loader()
            if ('success' in result) {
              Swal.fire({
                text: 'Hủy lịch đặt thành công !',
                icon: 'success',
                timer: 1500,
              })
              window.location.reload()
            } else {
              Swal.fire({
                text: result['error'],
                icon: 'error',
                timer: 1500,
              })
            }
          },
          Error: function (result) {
            main_layout.hide_loader()
            Swal.fire({
              text: 'Hủy lịch đặt không thành công!',
              icon: 'error',
              timer: 1500,
            })
          },
        })
      }
    })
  }
})

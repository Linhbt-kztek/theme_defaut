const input = document.getElementById('excelFileInput')
window.handleImportFile = function () {
  input.click()
}

input.addEventListener('change', function (event) {
  const file = event.target.files[0]
  if (!file) return
  staff_index.data_file = file
  event.target.value = ''
  handleImportExcel()
})

window.handleImportExcel = function () {
  main_layout.show_loader()

  const formData = new FormData()
  formData.append('excel_file', staff_index.data_file)
  formData.append('_token', $('meta[name="csrf-token"]').attr('content'))
  const url = staff_index.url_import_excel

  $.ajax({
    url: url,
    type: 'POST',
    data: formData,
    processData: false,
    contentType: false,
    success: function (response) {
      main_layout.hide_loader()

      if (response.status === 200) {
        Swal.fire({
          icon: 'success',
          title: 'Thành công!',
          text: response.message || 'Thêm mới dữ liệu thành công',
          timer: 2000,
          showConfirmButton: false,
        }).then(() => {
          window.location.reload()
        })
      } else {
        // Hiển thị lỗi nếu có
        if (response.data_errors && response.data_errors.length > 0) {
          showImportErrors(response.data_errors)
        } else {
          console.error('Import error:', response.message)
          Swal.fire({
            icon: 'error',
            title: 'Lỗi!',
            text: response.message || 'Có lỗi xảy ra khi thêm mới dữ liệu',
          })
        }
      }
    },
    error: function (xhr, status, error) {
      main_layout.hide_loader()
      console.error('Import error:', error)

      let errorMessage = 'Có lỗi xảy ra khi import dữ liệu'

      if (xhr.responseJSON && xhr.responseJSON.message) {
        errorMessage = xhr.responseJSON.message
      }

      Swal.fire({
        icon: 'error',
        title: 'Lỗi!',
        text: errorMessage,
      })
    },
    complete: function () {
      // Reset input file
      $('#excelFileInput').val('')
    },
  })
}
function showImportErrors(errors) {
  const grouped = new Map()
  let errorHtml = `
<div class="alert alert-borderless alert-danger fs-16 d-flex align-items-center" role="alert">
  <i class="ri-error-warning-line px-1"></i>
  <div class="text-start">
    <p class="m-0">Dữ liệu chưa được thêm vào hệ thống. Vui lòng kiểm tra và thực hiện thao tác lại.</p>
  </div>
</div>
<div class="table-responsive">
  <table class="table table-light-custom align-middle table-nowrap mb-0">
    <thead class="table-light text-muted">
      <tr><th class="text-center">Dòng</th><th>Lỗi</th></tr>
    </thead>
    <tbody>`

  // Gom lỗi theo dòng
  errors.forEach(({ row, message }) => {
    if (!grouped.has(row)) grouped.set(row, [])
    grouped.get(row).push(message)
  })

  for (const [row, messages] of grouped.entries()) {
    errorHtml += `<tr>
    <td class="text-center" style="vertical-align: middle;">${row}</td>
    <td class="ps-2 text-start">${messages.join('<br>')}</td>
  </tr>`
  }

  errorHtml += `</tbody></table></div>`

  const container = document.getElementById('importErrorsContainer')
  if (container) {
    container.innerHTML = errorHtml
    container.scrollIntoView({ behavior: 'smooth' })
    container.classList.add('d-block')
  }
  $('#errorsImportModal').modal('show')
}

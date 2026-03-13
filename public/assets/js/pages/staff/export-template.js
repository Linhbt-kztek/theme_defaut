function ExportFileTemplate() {
  let url = staff_index.url_export_template
  let namDowload = 'Mau_nhap_danh_sach_thanh_vien.xlsx'
  main_layout.show_loader()

  $.ajax({
    url: url,
    type: 'GET',
    headers: headersClient,
    xhrFields: {
      responseType: 'blob',
    },

    success: function (data, status, xhr) {
      main_layout.hide_loader()

      // Tạo blob URL
      const blob = new Blob([data], {
        type: 'application/vnd.openxmlformats-officedocument.spreadsheetml.sheet',
      })
      const url = window.URL.createObjectURL(blob)

      // Tạo link download
      const link = document.createElement('a')
      link.href = url
      link.download = namDowload
      link.style.display = 'none'

      // Download file
      document.body.appendChild(link)
      link.click()
      document.body.removeChild(link)

      // Cleanup
      window.URL.revokeObjectURL(url)

      // Hiển thị thông báo thành công
      Swal.fire({
        icon: 'success',
        title: 'Thành công!',
        text: 'Đã tải xuống file mẫu',
        timer: 1500,
        target: document.body,
        showConfirmButton: false,
        customClass: {
          popup: 'swal-custom-z',
        },
      })
    },
    error: function (xhr, status, error) {
      main_layout.hide_loader()
      console.error('Export error:', error)

      Swal.fire({
        icon: 'error',
        title: 'Lỗi!',
        text: 'Có lỗi xảy ra khi tải file mẫu',
      })
    },
  })
}

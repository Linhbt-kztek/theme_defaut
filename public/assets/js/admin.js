let main = {
  token: '',
  timestamp: function () {
    return new Date().getTime()
  },

  formatDate: function (dateString, has_minute = false, only_minute = false) {
    const date = new Date(dateString)

    const day = String(date.getDate()).padStart(2, '0')
    const month = String(date.getMonth() + 1).padStart(2, '0')
    const year = date.getFullYear()

    const hour = String(date.getHours()).padStart(2, '0')
    const minute = String(date.getMinutes()).padStart(2, '0')
    if (only_minute) {
      return `${hour}:${minute}`
    } else {
      if (has_minute) {
        // Giờ:phút ngày/tháng/năm
        return `${hour}:${minute} ${day}/${month}/${year}`
      } else {
        // Ngày/tháng/năm
        return `${day}/${month}/${year}`
      }
    }
  },
  formatDatePicker: function (dateString) {
    const date = new Date(dateString)
    const day = String(date.getDate()).padStart(2, '0')
    const month = String(date.getMonth() + 1).padStart(2, '0')
    const year = date.getFullYear()

    return `${day}-${month}-${year}`
  },

  formatDateTime(dateString) {
    const date = new Date(dateString)
    return date.toLocaleString('vi-VN', {
      hour: '2-digit',
      minute: '2-digit',
      day: '2-digit',
      month: '2-digit',
      year: 'numeric',
    })
  },
  formatTime(dateString) {
    const date = new Date(dateString)
    return date.toLocaleString('vi-VN', {
      hour: '2-digit',
      minute: '2-digit',
    })
  },

  getEndOfDay: function () {
    const today = new Date()
    const endOfDay = new Date(today)
    endOfDay.setHours(23, 59, 59, 999)
    return endOfDay
  },

  parseDate(dateStr) {
    if (!dateStr || typeof dateStr !== 'string') return null

    const parts = dateStr.trim().split(' ')
    if (parts.length !== 2) return null // Sai định dạng

    const [time, date] = parts
    const timeParts = time.split(':')
    const dateParts = date.split('-')

    if (timeParts.length !== 2 || dateParts.length !== 3) return null // Sai định dạng

    const [hour, minute] = timeParts.map(Number)
    const [day, month, year] = dateParts.map(Number)

    // Kiểm tra các giá trị hợp lệ
    if (isNaN(hour) || isNaN(minute) || isNaN(day) || isNaN(month) || isNaN(year)) {
      return null
    }

    // Tạo đối tượng Date
    const dateObj = new Date(year, month - 1, day, hour, minute)

    // Kiểm tra tính hợp lệ của ngày
    if (
      dateObj.getFullYear() !== year ||
      dateObj.getMonth() !== month - 1 ||
      dateObj.getDate() !== day ||
      dateObj.getHours() !== hour ||
      dateObj.getMinutes() !== minute
    ) {
      return null // Ngày hợp lệ không đúng
    }

    return dateObj
  },
}

function deleteItems(id, param, url, text_alert = 'Bạn có chắc chắn muốn xoá không ?') {
  Swal.fire({
    title: '',
    text: 'Bạn có chắc chắn muốn xoá không ?',
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
            $('#' + param + id).remove()
            Swal.fire({
              text: 'Xóa thành công !',
              icon: 'success',
              timer: 1500,
            })
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
            text: 'Xóa không thành công!',
            icon: 'error',
            timer: 1500,
          })
        },
      })
    }
  })
}

function initializeDateInputs() {
  // Khởi tạo cho input "Từ ngày"
  document.querySelectorAll('.cleave-date-from').forEach(function (element) {
    if (!element.cleave) {
      element.cleave = new Cleave(element, {
        date: true,
        delimiter: '/',
        datePattern: ['d', 'm', 'Y'],
      })
    }
  })

  // Khởi tạo cho input "Đến ngày"
  document.querySelectorAll('.cleave-date-to').forEach(function (element) {
    if (!element.cleave) {
      element.cleave = new Cleave(element, {
        date: true,
        delimiter: '/',
        datePattern: ['d', 'm', 'Y'],
      })
    }
  })
}

function initFlatpickr() {
  document.querySelectorAll('.date_single').forEach(el => {
    if (!el._flatpickr) {
      flatpickr(el, { dateFormat: 'd-m-Y', locale: 'vi' })
    }
  })

  document.querySelectorAll('.input_time').forEach(el => {
    if (!el._flatpickr) {
      flatpickr(el, {
        enableTime: true,
        noCalendar: true,
        dateFormat: 'H:i',
        time_24hr: true,
      })
    }
  })
}

function colorByLetter(letter) {
  const colors = [
    '#ff7b7b', // A
    '#ffa94d', // B
    '#ffd43b', // C
    '#69db7c', // D
    '#38d9a9', // E
    '#74c0fc', // F
    '#4dabf7', // G
    '#9775fa', // H
    '#da77f2', // I
    '#f783ac', // J
    '#ff922b', // K
    '#fab005', // L
    '#82c91e', // M
    '#40c057', // N
    '#12b886', // O
    '#15aabf', // P
    '#228be6', // Q
    '#4263eb', // R
    '#7048e8', // S
    '#ae3ec9', // T
    '#e64980', // U
    '#fa5252', // V
    '#f76707', // W
    '#f59f00', // X
    '#37b24d', // Y
    '#1098ad', // Z
  ]

  if (!letter) return '#adb5bd' // màu mặc định
  const index = letter.toUpperCase().charCodeAt(0) - 65 // A → 0, B → 1, ...
  return colors[index % colors.length] // fallback nếu vượt giới hạn
}

function updateRowIndex(tableID) {
  const rows = document.querySelectorAll(`${tableID} tbody tr`)
  rows.forEach((row, index) => {
    const sttCell = row.querySelector('td:first-child')
    if (sttCell) {
      sttCell.textContent = index + 1
    }
  })
}

function adjustColor(col, amt) {
  let usePound = false

  if (col[0] === '#') {
    col = col.slice(1)
    usePound = true
  }

  let num = parseInt(col, 16)

  let r = (num >> 16) + amt
  let g = ((num >> 8) & 0x00ff) + amt
  let b = (num & 0x0000ff) + amt

  r = Math.min(255, Math.max(0, r))
  g = Math.min(255, Math.max(0, g))
  b = Math.min(255, Math.max(0, b))

  return (usePound ? '#' : '') + ((r << 16) | (g << 8) | b).toString(16).padStart(6, '0')
}

let main_layout = {
  alert_main: function (title = '', icon = 'success', position = 'top-end') {
    Swal.fire({
      position: position,
      icon: icon,
      title: title,
      showConfirmButton: false,
      timer: 1500,
      showCloseButton: false,
    })
  },
  show_loader: function () {
    document.getElementById('overlay-loader-layout').style.display = 'block'
  },
  hide_loader: function () {
    document.getElementById('overlay-loader-layout').style.display = 'none'
  },
  formattedNumber: function (numberToFormat) {
    const formattedNumber = numberToFormat.toLocaleString('vi-VN', {
      style: 'decimal',
    })
    return formattedNumber
  },
  setIndex(querySelectorAll, coefficient = 0) {
    const rows = document.querySelectorAll(querySelectorAll)
    rows.forEach((row, index) => {
      const firstTd = row.querySelector('td:first-child')
      firstTd.textContent = index + coefficient + 1
    })
  },
}

flatpickr('.flatpickr', {
  enableTime: false,
  dateFormat: 'd-m-Y',
  locale: 'vn',
})

flatpickr('.input_datetime', {
  enableTime: true, // Enable time selection
  dateFormat: 'H:i d-m-Y', // Include hours and minutes in the date format
  locale: 'vn',
})

flatpickr('.date-range', {
  mode: 'range', // Chế độ chọn khoảng thời gian
  dateFormat: 'd-m-Y', // Định dạng hiển thị ngày
  enableTime: false, // Bật chọn thời gian
  dateInline: false, // Không hiển thị ngày inline
  time_24hr: false, // Sử dụng định dạng 24 giờ
  locale: 'vn',
})

flatpickr('.date-range-min', {
  mode: 'range',
  dateFormat: 'd-m-Y',
  enableTime: false,
  dateInline: false,
  time_24hr: false,
  locale: 'vn',
  minDate: 'today',
})

flatpickr('.date-single', {
  mode: 'single', // Chế độ chọn khoảng thời gian
  dateFormat: 'd-m-Y', // Định dạng hiển thị ngày
  enableTime: false, // Bật chọn thời gian
  dateInline: false, // Không hiển thị ngày inline
  time_24hr: false, // Sử dụng định dạng 24 giờ
  locale: 'vn',
})

flatpickr('.date-single-min', {
  mode: 'single', // Chế độ chọn khoảng thời gian
  dateFormat: 'd-m-Y', // Định dạng hiển thị ngày
  enableTime: false, // Bật chọn thời gian
  dateInline: false, // Không hiển thị ngày inline
  time_24hr: false, // Sử dụng định dạng 24 giờ
  locale: 'vn',
  minDate: 'today',
})

// flatpickr('.date-full', {
//   mode: 'single',
//   dateFormat: 'd/m/Y',
//   altInput: true,
//   altFormat: 'l, d/m/Y',
//   enableTime: false,
//   locale: 'vn',
// })

flatpickr('.datetime-range', {
  mode: 'range', // Chế độ chọn khoảng thời gian
  dateFormat: 'H:i d-m-Y', // Định dạng hiển thị ngày
  enableTime: true, // Bật chọn thời gian
  dateInline: false, // Không hiển thị ngày inline
  time_24hr: true, // Sử dụng định dạng 24 giờ
  locale: 'vn',
  // locale: {
  //     firstDayOfWeek: 1 // Đặt thứ Hai là ngày bắt đầu tuần
  // }
})

$('.nav-link.active').parents().eq(2).addClass('show')
$('.nav-link.active').parents().eq(3).children('a').attr('aria-expanded', 'true')

flatpickr('.input_time', {
  enableTime: true,
  noCalendar: true,
  dateFormat: 'H:i',
  time_24hr: true,
  allowInput: true,
})

flatpickr('.input_time_30', {
  enableTime: true,
  noCalendar: true,
  dateFormat: 'H:i',
  time_24hr: true,
  allowInput: true,
  minuteIncrement: 30,

  onClose: function (selectedDates, dateStr, instance) {
    let raw = instance.input.value.replace(':', '')

    if (raw.length === 4) {
      let hours = parseInt(raw.substring(0, 2))
      let minutes = parseInt(raw.substring(2, 4))

      // Làm tròn lên block 30
      let roundedMinutes = Math.ceil(minutes / 30) * 30

      if (roundedMinutes === 60) {
        hours += 1
        roundedMinutes = 0
      }

      let formatted = String(hours).padStart(2, '0') + ':' + String(roundedMinutes).padStart(2, '0')

      instance.setDate(formatted, true)
    }
  },
})

function formatMoney(amount) {
  return Number(amount ?? 0).toLocaleString('vi-VN') + ' đ'
}

function timeToMinutes(time) {
  if (!time) return null

  const [h, m] = time.split(':').map(Number)
  return h * 60 + m
}

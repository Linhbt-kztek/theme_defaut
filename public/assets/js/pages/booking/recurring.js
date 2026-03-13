document.addEventListener('DOMContentLoaded', () => {
  // existing court blocks
  document.querySelectorAll('#bookingDayContainer .court-block').forEach(block => {
    initCourtBlock(block)
    block.querySelectorAll('.input_time_30').forEach(el => {
      if (!el._flatpickr) {
        flatpickr(el, {
          enableTime: true,
          noCalendar: true,
          dateFormat: 'H:i',
          time_24hr: true,
          allowInput: true,
          minuteIncrement: 30,
        })
      }
    })
  })

  // thêm sân

  const btnAddCourt = document.getElementById('btnAddCourt')
  if (btnAddCourt) {
    btnAddCourt.addEventListener('click', () => {
      const tpl = document.getElementById('courtTemplate').content.cloneNode(true)
      const block = tpl.querySelector('.court-block')
      initCourtBlock(block)
      document.getElementById('bookingDayContainer').appendChild(tpl)

      block.querySelectorAll('.input_time_30').forEach(el => {
        if (!el._flatpickr) {
          flatpickr(el, {
            enableTime: true,
            noCalendar: true,
            dateFormat: 'H:i',
            time_24hr: true,
            allowInput: true,
            minuteIncrement: 30,
          })
        }
      })
    })
  }

  /** DATE RANGE (single input) */
  document.getElementById('date-range')?.addEventListener('change', e => {
    bookingRecurring.date_range.start = e.target.value
    bookingRecurring.date_range.end = e.target.value
  })

  document.getElementById('bookingModal')?.addEventListener('hidden.bs.modal', function () {
    resetRecurringForm()
  })

  function resetRecurringForm() {
    const form = document.getElementById('recurringBookingForm')
    const container = document.getElementById('bookingDayContainer')

    container.innerHTML = ''

    const tpl = document.getElementById('courtTemplate').content.cloneNode(true)

    const block = tpl.querySelector('.court-block')

    container.appendChild(tpl)

    initCourtBlock(container.querySelector('.court-block'))

    container.querySelectorAll('.input_time_30').forEach(el => {
      flatpickr(el, {
        enableTime: true,
        noCalendar: true,
        dateFormat: 'H:i',
        time_24hr: true,
        allowInput: true,
        minuteIncrement: 30,
      })
    })

    form.reset()

    form.querySelectorAll('.date-range-min').forEach(input => {
      if (input._flatpickr) {
        input._flatpickr.clear()
      }
    })

    bookingRecurring.booking = {}

    document.getElementById('customerInput').value = ''
    form.querySelector('[name="customer_id"]').value = ''

    document.querySelector('.total-price').innerText = '0 vnđ'
    document.querySelector('.prepaid-price').innerText = '0 vnđ'
  }
})

function validateTime(block) {
  const startVal = block.querySelector('.start-time').value
  const endVal = block.querySelector('.end-time').value
  const select = block.querySelector('select')

  if (!startVal || !endVal) {
    return 'Vui lòng chọn giờ'
  }

  const start = toMinutes(startVal)
  const end = toMinutes(endVal)

  if (start >= end) {
    return 'Giờ kết thúc phải lớn hơn giờ bắt đầu'
  }

  const opt = select.options[select.selectedIndex]

  if (!opt.value) {
    return 'Vui lòng chọn sân'
  }

  const open = toMinutes(opt.dataset.startTime)
  const close = toMinutes(opt.dataset.endTime)

  if (start < open || end > close) {
    return `Sân chỉ hoạt động từ ${opt.dataset.startTime} đến ${opt.dataset.endTime}`
  }

  const diff = end - start
  if (diff % 30 !== 0) {
    return 'Thời gian phải là bội 30 phút'
  }

  return null
}

function checkDuplicateCourtTime(currentBlock) {
  const blocks = document.querySelectorAll('#bookingDayContainer .court-block')

  const courtId = currentBlock.dataset.courtId

  const start = currentBlock.querySelector('.start-time').value
  const end = currentBlock.querySelector('.end-time').value

  const days = [...currentBlock.querySelectorAll('.weekday-btn.active')].map(btn => btn.dataset.day)


  for (let block of blocks) {
    if (block === currentBlock) continue

    const otherCourtId = block.dataset.courtId
    if (courtId !== otherCourtId) continue

    const otherStart = block.querySelector('.start-time').value
    const otherEnd = block.querySelector('.end-time').value

    const otherDays = [...block.querySelectorAll('.weekday-btn.active')].map(btn => btn.dataset.day)

    /* check trùng ngày */
    const dayOverlap = days.some(d => otherDays.includes(d))

    if (!dayOverlap) continue

    /* check trùng giờ */
    if (start < otherEnd && end > otherStart) {
      return true
    }
  }

  return false
}

function toMinutes(t) {
  const [h, m] = t.split(':').map(Number)
  return h * 60 + m
}

/** Validate time của 1 court block */
function validateCourtTime(block) {
  const startInput = block.querySelector('.start-time')
  const endInput = block.querySelector('.end-time')
  const select = block.querySelector('select')

  if (!startInput.value || !endInput.value || !select.value) {
    return 'Vui lòng chọn sân và thời gian đầy đủ'
  }

  const start = timeToMinutes(startInput.value)
  const end = timeToMinutes(endInput.value)

  const opt = select.options[select.selectedIndex]

  const open = timeToMinutes(opt.dataset.startTime)
  const close = timeToMinutes(opt.dataset.endTime)

  // 1. End > Start
  if (start >= end) {
    return 'Giờ kết thúc phải lớn hơn giờ bắt đầu'
  }

  // 2. Trong giờ mở cửa
  if (start < open || end > close) {
    return `Thời gian sân hoạt động từ ${opt.dataset.startTime} đến ${opt.dataset.endTime}`
  }

  // 3. Bội số 30 phút
  if (start % 30 !== 0 || end % 30 !== 0) {
    return 'Thời gian phải theo bước 30 phút'
  }

  return null
}

function initCourtBlock(block, modalSelector) {
  const select = block.querySelector('select')
  const start = block.querySelector('.start-time')
  const end = block.querySelector('.end-time')

  const btnOk = block.querySelector('.btn-confirm')
  const btnEdit = block.querySelector('.btn-edit')
  const btnDel = block.querySelector('.btn-remove-time')

  block.dataset.state = 'editing'

  /* ===== realtime validate ===== */
  ;[start, end, select].forEach(el => {
    el.addEventListener('change', () => {
      const err = validateTime(block)

      if (err) {
        block.classList.add('is-invalid')
        block.dataset.error = err
      } else {
        block.classList.remove('is-invalid')
        block.dataset.error = ''
      }
    })
  })

  /* ===== Confirm ===== */
  btnOk.addEventListener('click', () => {
    const err = validateTime(block)

    if (err) {
      main_layout.alert_main(err, 'error')
      return
    }

    /* check trùng */
    if (checkDuplicateCourtTime(block)) {
      main_layout.alert_main('Thời gian đặt sân đang tạo bị trùng với khung giờ đã chọn', 'error', 'center')
      return
    }

    lockBlock(block)

    calculatePrice(null, modalSelector)
  })
  /* ===== Edit ===== */
  btnEdit.addEventListener('click', () => {
    unlockBlock(block)
  })

  /* ===== Remove ===== */
  btnDel.addEventListener('click', () => {
    const id = block.dataset.courtId
    const container = block.closest('form') || block.closest('.modal')

    block.remove()

    if (id) delete bookingRecurring.booking[id]

    autoUpdatePrice(container)
  })
}

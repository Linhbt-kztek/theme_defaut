document.addEventListener('DOMContentLoaded', function () {
  window.submitBooking = function () {
    main_layout.show_loader()
    let url = booking_index.url_store
    let type = $('#rentalType li.active').data('value')
    let data = null

    if (type == 1) {
      // Đặt sân lẻ
      data = new FormData($('#singleBookingForm')[0])
      data.append('booking', JSON.stringify(bookingTemp))
      data.append('type', 1)
    } else if (type == 2) {
      // Đặt sân cố định (recurring)
      data = new FormData($('#recurringBookingForm')[0])

      const activeTab = document.querySelector('.tab-pane.active')
      const customerId = activeTab.querySelector('input[name="customer_id"]')?.value

      if (customerId) data.append('customer_id', customerId)
      const dateRange = document.getElementById('date-range')?.value || null
      data.append('date_range', dateRange)

      const bookingPayload = { courts: [] }
      document.querySelectorAll('#bookingDayContainer .court-block').forEach(block => {
        const courtId = block.querySelector('select')?.value
        if (!courtId) return

        const rangeTime = []
        block.querySelectorAll('.time-range').forEach(tr => {
          const startTime = tr.querySelector('.start-time')?.value
          const endTime = tr.querySelector('.end-time')?.value
          if (startTime && endTime) {
            rangeTime.push({ start_time: startTime, end_time: endTime })
          }
        })

        const days = []
        block.querySelectorAll('.weekday-btn').forEach(btn => {
          if (btn.classList.contains('active')) days.push(btn.dataset.day)
        })

        bookingPayload.courts.push({
          court_id: courtId,
          rangeTime: rangeTime,
          day_of_week: days.join(','),
        })
      })

      data.append('booking', JSON.stringify(bookingPayload))
    } else if (type == 3) {
      data = new FormData($('#mergeCourtForm')[0])
      data.append('type', 3)
    }

    if (!data) return

    data.append('_token', main.token)

    $.ajax({
      url: url,
      type: 'POST',
      data: data,
      processData: false,
      contentType: false,
      success: function (result) {
        main_layout.hide_loader()
        if (result.status == 200) {
          main_layout.alert_main(result.message, 'success', 'center')
          $('#bookingModal').modal('hide')
          window.location.reload()
        } else {
          if (result.type_error == 'data' || result.status == 90) {
            main_layout.alert_main(result.message, 'error', 'center')
          }

          if (result.type_error == 'conflict') {
            renderConflictList(result.data_conflict)
          }
        }
      },
    })
  }

  window.quickSingleBooking = function () {
    main_layout.show_loader()
    let url = booking_index.url_store
    let data = null

    data = new FormData($('#singleBookingForm')[0])
    data.append('booking', JSON.stringify(bookingTemp))
    data.append('type', 1)

    if (!data) return

    data.append('_token', main.token)

    $.ajax({
      url: url,
      type: 'POST',
      data: data,
      processData: false,
      contentType: false,
      success: function (result) {
        main_layout.hide_loader()
        if (result.status == 200) {
          main_layout.alert_main(result.message, 'success', 'center')
          $('#singleBookingModal').modal('hide')
          window.location.reload()
        } else {
          if (result.type_error == 'data' || result.status == 90) {
            main_layout.alert_main(result.message, 'error', 'center')
          }

          if (result.type_error == 'conflict') {
            renderConflictList(result.data_conflict)
          }
        }
      },
    })
  }

  $('#bookingModal').on('show.bs.modal', function () {
    resetMergeCourtForm()
    resetForm('#recurringBookingForm')
  })

  function resetForm(formId) {
    const form = document.querySelector(formId)
    if (!form) return
    form.reset()
    form.querySelectorAll('select').forEach(select => {
      select.selectedIndex = 0
    })

    form
      .querySelectorAll('input[type="time"], input[type="number"], input[type="text"]')
      .forEach(input => {
        input.value = ''
      })

    const totalPriceText = form.querySelector('.total-price')
    const prepaidPriceText = form.querySelector('.prepaid-price')

    if (totalPriceText) totalPriceText.innerText = '0 vnđ'
    if (prepaidPriceText) prepaidPriceText.innerText = '0 vnđ'
  }

  function resetMergeCourtForm() {
    const form = document.getElementById('mergeCourtForm')
    if (!form) return

    form.reset()

    form.querySelectorAll('input').forEach(i => (i.value = ''))
    form.querySelectorAll('select').forEach(s => (s.selectedIndex = 0))
  }

  window.initCourtBlock = function (block) {
    const selectCourt = block.querySelector('select')
    const weekdayBtns = block.querySelectorAll('.weekday-btn')
    const startInput = block.querySelector('.start-time')
    const endInput = block.querySelector('.end-time')

    let courtId = null
    let selectedDays = new Set()

    /* INIT selectedDays from UI */
    function syncDaysFromUI() {
      selectedDays.clear()

      block.querySelectorAll('.weekday-btn.active').forEach(btn => {
        selectedDays.add(btn.dataset.day)
      })
    }

    /* ================= SELECT ================= */
    selectCourt.addEventListener('change', () => {
      courtId = selectCourt.value
      if (!courtId) return

      block.dataset.courtId = courtId

      bookingRecurring.booking[courtId] = {
        court_id: courtId,
        court_name: selectCourt.options[selectCourt.selectedIndex].text,
        day_of_week: '',
        rangeTime: [{ start_time: '', end_time: '' }],
      }
    })

    /* ================= WEEKDAY ================= */
    weekdayBtns.forEach(btn => {
      btn.addEventListener('click', () => {
        if (block.dataset.state === 'locked') return

        if (!courtId) {
          main_layout.alert_main('Vui lòng chọn sân trước', 'error', 'center')
          return
        }

        btn.classList.toggle('active')

        syncDaysFromUI()

        bookingRecurring.booking[courtId].day_of_week = Array.from(selectedDays).join(',')

        const container = block.closest('form') || block.closest('.modal')
        autoUpdatePrice(container)
      })
    })

    // validate thời gian từng phiên
    function syncTime() {
      if (!courtId) return

      if (!validateCourtTime(block)) return

      bookingRecurring.booking[courtId].rangeTime[0] = {
        start_time: startInput.value,
        end_time: endInput.value,
      }

      const container = block.closest('form') || block.closest('.modal')
      autoUpdatePrice(container)
    }

    startInput.addEventListener('change', syncTime)
    endInput.addEventListener('change', syncTime)

    // xóa lịch đặt
    block.querySelector('.btn-remove-time')?.addEventListener('click', () => {
      const container = block.closest('form') || block.closest('.modal')
      if (courtId) {
        delete bookingRecurring.booking[courtId]
      }
      block.remove()
      autoUpdatePrice(container)
    })

    // xác nhận sân đặt và thời gian
    block.querySelector('.btn-confirm')?.addEventListener('click', () => {
      const error = validateCourtTime(block, courtId, selectedDays)

      if (checkDuplicateCourtTime(block)) {
        main_layout.alert_main('Thời gian đặt sân đang tạo bị trùng với khung giờ đã chọn', 'error')
        return
      }

      if (error) {
        main_layout.alert_main(error, 'error', 'center')
        return
      }
      lockBlock(block)

      const container = block.closest('form') || block.closest('.modal')
      autoUpdatePrice(container)

      // Lưu data
      bookingRecurring.booking[courtId] = {
        court_id: courtId,
        court_name: selectCourt.options[selectCourt.selectedIndex].text,
        day_of_week: Array.from(selectedDays).join(','),
        rangeTime: [
          {
            start_time: startInput.value,
            end_time: endInput.value,
          },
        ],
      }
    })

    block.querySelector('.btn-edit')?.addEventListener('click', () => {
      unlockBlock(block)
    })

    syncDaysFromUI()
  }

  window.lockBlock = function (block) {
    block.dataset.state = 'locked'

    block.querySelectorAll('input,select,button.weekday-btn').forEach(el => (el.disabled = true))

    block.querySelector('.btn-confirm').classList.add('d-none')
    block.querySelector('.btn-edit').classList.remove('d-none')
  }

  window.unlockBlock = function (block) {
    block.dataset.state = 'editing'

    block.querySelectorAll('input,select,button.weekday-btn').forEach(el => (el.disabled = false))

    block.querySelector('.btn-confirm').classList.remove('d-none')
    block.querySelector('.btn-edit').classList.add('d-none')
  }

  window.resetPayment = function (container) {
    if (!container) return

    const total = container.querySelector('.total-price')
    const prepaid = container.querySelector('.prepaid-price')

    if (total) total.innerText = '0 vnđ'
    if (prepaid) prepaid.innerText = '0 vnđ'
  }

  window.canCalculate = function (container) {
    // Check date
    const dateInput =
      container.querySelector('[name="date_range"]') || container.querySelector('[name="date"]')

    if (!dateInput || !dateInput.value) {
      return false
    }

    // Có block đã confirm chưa
    const lockedBlocks = container.querySelectorAll('.court-block[data-state="locked"]')

    if (!lockedBlocks.length) {
      return false
    }

    // Check từng block đủ dữ liệu
    for (const block of lockedBlocks) {
      const court = block.querySelector('select')?.value
      const start = block.querySelector('.start-time')?.value
      const end = block.querySelector('.end-time')?.value

      if (!court || !start || !end) {
        return false
      }
    }

    return true
  }

  window.triggerCalculate = function (type) {
    const container =
      type === 2
        ? document.querySelector('#recurringBookingForm')
        : document.querySelector('#singleBookingForm')

    if (!container) return

    if (!canCalculate(container)) {
      resetPayment(container)
      return
    }

    calculatePrice(type)
  }

  window.calculatePrice = function (type, containerSelector = null) {
    let container = getBookingContainer(type, containerSelector)
    if (!container) return

    let url = booking_index.url_calculate
    let data = []
    const dateInput =
      container.querySelector('[name="date_range"]') ||
      container.querySelector('[name="date"]') ||
      container.querySelector('#date-range')

    /* ================= TYPE 2 : SÂN CỐ ĐỊNH ================= */
    if (type === 2) {
      if (!canCalculate(container)) {
        resetPayment(container)
        return
      }

      const bookingData = buildRecurringBooking(container)

      if (!Object.keys(bookingData.booking).length) {
        resetPayment(container)
        return
      }

      data = new FormData(container)
      data.append('booking', JSON.stringify(bookingData))
      data.append('type', 2)
      data.append('date', dateInput.value)
    }

    /* ================= TYPE 1 : SÂN LẺ ================= */
    if (type === 1) {
      data = new FormData(container)
      data.append('booking', JSON.stringify(bookingTemp))
      data.append('type', 1)
    }

    if (!data) return
    data.append('_token', main.token)

    $.ajax({
      url,
      type: 'POST',
      data,
      processData: false,
      contentType: false,
      success(res) {
        const newPrice = res.total_price ?? 0
        container.querySelector('.total-price').innerText = formatMoney(newPrice)
        container.querySelector('.prepaid-price').innerText = formatMoney(oldTotalPrice)

        const refundEl = container.querySelector('#refundPriceText')

        if (refundEl && oldTotalPrice) {
          const diff = oldTotalPrice - newPrice

          if (diff > 0) {
            // Hoàn +
            refundEl.innerText = '-' + formatMoney(diff)
          } else if (diff < 0) {
            // Thu thêm
            refundEl.innerText = '+' + formatMoney(Math.abs(diff))
          } else {
            refundEl.innerText = '0 vnđ'
          }
        }
      },
    })
  }
  function buildRecurringBooking(container) {
    const result = {
      date_range: {
        start: '',
        end: '',
      },
      booking: {},
    }

    /* ===== LẤY DATE RANGE ===== */
    const dateInput =
      container.querySelector('[name="date_range"]') || container.querySelector('[name="date"]')

    if (dateInput && dateInput.value) {
      result.date_range.start = dateInput.value
      result.date_range.end = dateInput.value
    }

    /* ===== DUYỆT CÁC BLOCK ĐÃ TICK ===== */
    container.querySelectorAll('.court-block[data-state="locked"]').forEach(block => {
      const select = block.querySelector('select')
      if (!select || !select.value) return

      const courtId = select.value
      const courtName = select.options[select.selectedIndex]?.text || ''

      /* ===== LẤY NGÀY TRONG TUẦN ===== */
      const days = Array.from(block.querySelectorAll('.weekday-btn.active')).map(
        btn => btn.dataset.day
      )

      /* ===== LẤY GIỜ ===== */
      const timeRanges = []

      block.querySelectorAll('.time-range').forEach(range => {
        const start = range.querySelector('.start-time')?.value
        const end = range.querySelector('.end-time')?.value

        if (!start || !end) return

        timeRanges.push({
          start_time: start,
          end_time: end,
        })
      })

      /* ===== GOM THEO COURT ===== */
      if (!result.booking[courtId]) {
        result.booking[courtId] = {
          court_id: courtId,
          court_name: courtName,
          day_of_week: days.join(','),
          rangeTime: [],
        }
      }

      result.booking[courtId].rangeTime.push(...timeRanges)
    })

    return result
  }

  window.getBookingContainer = function (type, containerSelector) {
    // ưu tiên selector
    if (containerSelector) {
      const modal = document.querySelector(containerSelector)

      if (!modal) return null

      return modal.querySelector('form')
    }

    if (type === 1) return document.querySelector('#singleBookingForm')
    if (type === 2) return document.querySelector('#recurringBookingForm')

    return null
  }
})

document.addEventListener('change', function (e) {
  if (!e.target.matches('.date-range-min')) return

  const container = e.target.closest('form') || e.target.closest('.modal')

  autoUpdatePrice(container)
})

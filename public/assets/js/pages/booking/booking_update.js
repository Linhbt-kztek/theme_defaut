let oldTotalPrice = 0
document.addEventListener('DOMContentLoaded', function () {
  window.showRenewBoooking = function (booking_id) {
    document.querySelector('[name="last_booking_id"]').value = booking_id
    // document.querySelector('[name="customer_id"]').value = booking_id

    $.ajax({
      url: 'booking/getRawBooking/' + booking_id,
      type: 'GET',
      success: function (response) {
        if (response.status == 200) {
          fillRenewBookingModal(response, '#renewBookingModal')
          $('#renewBookingModal').modal('show')
        }
      },
    })
  }

  function fillRenewBookingModal(response, modalSelector) {
    if (!response || response.status !== 200) {
      console.error('Response invalid')
      return
    }
    customer = response.data.customer
    booking_data = response.data.booking_data
    const modal = document.querySelector(modalSelector)
    if (!modal) return

    oldTotalPrice = response.data.booking_depossit ?? 0

    const oldPriceEl = modal.querySelector('.total-price')
    const depositEl = modal.querySelector('.prepaid-price')

    if (oldPriceEl) {
      oldPriceEl.innerText = formatMoney(oldTotalPrice)
      depositEl.innerText = formatMoney(oldTotalPrice)
    }

    if (customer) {
      modal.querySelector('[name="customer_id"]').value = customer.id
      modal.querySelector('#customerInput').value = `${customer.name} - ${customer.phone ?? ''}`
    }
    if (response.data.booking_type == 2) {
      modal.querySelector('#date-range').value = response.data.date
    }

    //  Reset UI
    const container = modal.querySelector('.booking-day-container')
    // Lấy court-block đầu tiên làm template
    const templateBlock = container.querySelector('.court-block')

    container.innerHTML = ''
    bookingRecurring.booking = {}

    // Loop courts
    booking_data.courts.forEach((court, index) => {
      const block = templateBlock.cloneNode(true)
      container.appendChild(block)
      initCourtBlock(block)
      bookingRecurring.booking[court.court_id] = {
        court_id: court.court_id,
        court_name: court.court_name,
        day_of_week: court.day_of_week,
        rangeTime: court.rangeTime,
      }

      // Set court select
      const courtSelect = block.querySelector('select')
      courtSelect.value = court.court_id
      courtSelect.dispatchEvent(new Event('change'))

      //  Set weekday
      const days = (court.day_of_week || '').split(',')

      block.querySelectorAll('.weekday-btn').forEach(btn => {
        btn.classList.remove('active')
        if (days.includes(btn.dataset.day)) {
          btn.classList.add('active')
        }
      })

      //  Set time range
      if (court.rangeTime?.length) {
        const { start_time, end_time } = court.rangeTime[0]

        const startInput = block.querySelector('.start-time')
        const endInput = block.querySelector('.end-time')

        startInput.value = start_time
        endInput.value = end_time

        startInput.dispatchEvent(new Event('change'))
        endInput.dispatchEvent(new Event('change'))
      }

      // Init flatpickr
      block.querySelectorAll('.input_time_30').forEach(el => {
        if (!el._flatpickr) {
          flatpickr(el, {
            enableTime: true,
            noCalendar: true,
            dateFormat: 'H:i',
            time_24hr: true,
            minuteIncrement: 30,
          })
        }
      })

      lockBlock(block)
      // saveBlock(block)
    })
  }

  function fillShareBookingModal(response, modalSelector) {
    if (!response || response.status !== 200) {
      console.error('Response invalid')
      return
    }

    const data = response.data
    const modal = document.querySelector(modalSelector)

    if (!modal || !data) return

    const courtSelect = modal.querySelector('[name="court_id"]')
    if (courtSelect) {
      courtSelect.value = data.court_id
    }

    modal.querySelector('[name="date"]').value = data.date
    modal.querySelector('[name="start_time"]').value = data.start_time.substring(0, 5) // 16:00
    modal.querySelector('[name="end_time"]').value = data.end_time.substring(0, 5) // 18:00
    modal.querySelector('[name="slots"]').value = data.slots
    modal.querySelector('[name="type"]').value = data.booking_type
    modal.querySelector('[name="booking_id"]').value = data.booking_id
    modal.querySelector('[name="price"]').value = data.price
  }

  function bindRemoveTime(modalSelector) {
    const modal = document.querySelector(modalSelector)
    if (!modal) return

    const container = modal.querySelector('form')
    const bookingContainer = modal.querySelector('.booking-day-container')

    bookingContainer.addEventListener('click', function (e) {
      const btn = e.target.closest('.btn-remove-time')
      if (!btn) return

      const block = btn.closest('.court-block')
      if (!block) return

      const courtId = block.dataset.courtId

      // Remove UI
      block.remove()

      // Remove data
      if (courtId && bookingRecurring.booking[courtId]) {
        delete bookingRecurring.booking[courtId]
      }

      const type = 2 // sân cố định
      calculatePrice(type, modalSelector)
    })
  }

  document.addEventListener('click', e => {
    const btn = e.target.closest('.btn-add-court')
    if (!btn) return

    const modal = btn.closest('.modal')

    const tpl = document.getElementById('courtTemplate').content.cloneNode(true)
    const block = tpl.querySelector('.court-block')

    initCourtBlock(block, '#' + modal.id)
    initTimePicker(block)

    modal.querySelector('.booking-day-container').appendChild(tpl)
  })

  document.addEventListener('change', function (e) {
    if (!e.target.matches('[name="date_range"]')) return

    bookingRecurring.date_range = {
      start: e.target.value,
      end: e.target.value,
    }
  })

  document.addEventListener('change', function (e) {
    const select = e.target.closest('.court-block select')
    if (!select) return

    const block = select.closest('.court-block')
    if (!block) return

    const courtId = block.dataset.courtId || select.value
    block.dataset.courtId = courtId

    const name = select.options[select.selectedIndex].text

    if (!bookingRecurring.booking[courtId]) {
      bookingRecurring.booking[courtId] = {}
    }

    bookingRecurring.booking[courtId].court_id = select.value
    bookingRecurring.booking[courtId].court_name = name
  })

  bindRemoveTime('#renewBookingModal')
  bindRemoveTime('#editRecurringBookingModal')

  window.renewBooking = function () {
    const lastBookingId = $('#renewBookingForm [name="last_booking_id"]').val()
    let url = booking_index.url_renew.replace('__ID__', lastBookingId)

    let data = new FormData($('#renewBookingForm')[0])
    const courts = Object.values(bookingRecurring.booking)

    data.append('booking', JSON.stringify({ courts: courts }))

    if (!data) return

    $.ajax({
      url: url,
      type: 'POST',
      data: data,
      processData: false,
      contentType: false,
      success: function (result) {
        if (result.status == 200) {
          main_layout.alert_main(result.message, 'success', 'center')
          $('#renewBookingModal').modal('hide')
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

  window.showEditBookingModal = function (type, booking_id, detailModalContainer) {
    $(detailModalContainer).modal('hide')
    main_layout.show_loader()
    if (type == 3) {
      $.ajax({
        url: 'booking/getRawBooking/' + booking_id,
        type: 'GET',
        success: function (response) {
          if (response.status == 200) {
            fillShareBookingModal(response, '#editShareBookingModal')
            document.querySelector('[name="booking_id"]').value = booking_id
            main_layout.hide_loader()

            $('#editShareBookingModal').modal('show')
          }
        },
      })
    }

    if (type == 2) {
      $.ajax({
        url: 'booking/getRawBooking/' + booking_id,
        type: 'GET',
        success: function (response) {
          if (response.status == 200) {
            fillRenewBookingModal(response, '#editRecurringBookingModal')
            document.querySelector('[name="booking_id"]').value = booking_id
            main_layout.hide_loader()
            $('#editRecurringBookingModal').modal('show')
          }
        },
      })
    }
  }
  window.autoUpdatePrice = function (container) {
    if (!container) return

    if (!canCalculate(container)) {
      resetPayment(container)
      return
    }

    // form thường
    if (container.id === 'recurringBookingForm') {
      calculatePrice(2)
      return
    }

    // modal edit
    const modal = container.closest('.modal')
    if (modal) {
      calculatePrice(2, '#' + modal.id)
    }
  }

  window.handelEditRecurringBooking = function () {
    main_layout.show_loader()
    const bookingId = $('#editRecurringBookingForm [name="booking_id"]').val()
    let url = booking_index.url_edit.replace('__ID__', bookingId)
    let data = new FormData($('#editRecurringBookingForm')[0])

    const bookingPayload = { courts: [] }
    document.querySelectorAll('.booking-day-container .court-block').forEach(block => {
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
    data.append('_token', main.token)

    $.ajax({
      url: url,
      type: 'POST',
      data: data,
      processData: false,
      contentType: false,
      success: function (result) {
        main_layout.hide_loader
        console.log(result)

        if (result.status == 200) {
          main_layout.alert_main(result.message, 'success', 'center')
          $('#editRecurringBookingModal').modal('hide')
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

  window.handelEditShareBooking = function () {
    main_layout.show_loader()
    const bookingId = $('#editShareBookingForm [name="booking_id"]').val()
    let url = booking_index.url_edit.replace('__ID__', bookingId)
    let data = new FormData($('#editShareBookingForm')[0])

    data.append('_token', main.token)

    $.ajax({
      url: url,
      type: 'POST',
      data: data,
      processData: false,
      contentType: false,
      success: function (result) {
        main_layout.hide_loader
        if (result.status == 200) {
          $('#editShareBookingModal').modal('hide')
          main_layout.alert_main(result.message, 'success', 'center')
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
})

function initTimePicker(block) {
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
}

function initCourtBlockFull(block, modalId = null) {
  initCourtBlock(block, modalId)
  initTimePicker(block)
}

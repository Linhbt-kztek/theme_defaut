;(function () {
  function onQtyChange() {
    const qtyEl = document.getElementById('sbQty')
    const qty = Number(qtyEl.value) || 0
    const price = Number(document.getElementById('sbPrice').dataset.raw) || 0
    const remain = Number(document.getElementById('sbRemain').dataset.raw) || 0

    const errorEl = document.getElementById('sbError')
    const confirmBtn = document.getElementById('sbConfirm')

    if (qty < 1) {
      qtyEl.classList.add('is-invalid')
      errorEl.innerText = 'Số lượng phải lớn hơn 0'
      confirmBtn.disabled = true
    } else if (qty > remain) {
      qtyEl.classList.add('is-invalid')
      errorEl.innerText = 'Số lượng vượt quá số chỗ còn lại'
      confirmBtn.disabled = true
    } else {
      qtyEl.classList.remove('is-invalid')
      errorEl.innerText = ''
      confirmBtn.disabled = false
    }

    document.getElementById('sbTotal').value = formatMoney(qty * price)
  }

  function onConfirm() {
    const qty = Number(document.getElementById('sbQty').value) || 0
    const payment_method = Number(document.getElementById('payment_method').value)

    const bid = document.getElementById('sbConfirm').dataset.bookingId
    const session = window.sharedSessions && window.sharedSessions[bid]
    if (!session) return

    const data = new FormData()
    data.append('_token', main.token)
    data.append('booking_id', bid)
    data.append('qty', qty)
    data.append('payment_method', payment_method)

    const sbCustomerIdEl = document.getElementById('sbCustomerId')
    if (sbCustomerIdEl?.value) {
      data.append('customer_id', sbCustomerIdEl.value)
    }

    $.ajax({
      url: booking_index.url_join_shared,
      type: 'POST',
      data: data,
      processData: false,
      contentType: false,
      dataType: 'json',

      success: function (result) {
        if (result.status !== 200) {
          main_layout.alert_main(result.message || 'Đăng ký thất bại', 'error', 'center')
          return
        }

        // update phiên
        if (window.sharedSessions && window.sharedSessions[bid]) {
          window.sharedSessions[bid].count = result.data.count
        }

        // update UI
        document.getElementById('sbCount').value = result.data.count
        const remain = Math.max(0, result.data.slots - result.data.count)
        document.getElementById('sbRemain').value = remain
        document.getElementById('sbRemain').dataset.raw = remain

        if (typeof markBookedSlots === 'function') markBookedSlots()

        $('#shareBookingModal').modal('hide')

        main_layout.alert_main(result.message, 'success', 'center')
        window.location.reload()
      },

      error: function (xhr) {
        console.error(xhr)
        main_layout.alert_main('Có lỗi khi gửi yêu cầu', 'error')
      },
    })
  }

  document.addEventListener('DOMContentLoaded', () => {
    const qtyEl = document.getElementById('sbQty')
    const confirmBtn = document.getElementById('sbConfirm')
    if (qtyEl) qtyEl.addEventListener('input', onQtyChange)
    if (confirmBtn) confirmBtn.addEventListener('click', onConfirm)

    function render(session, bookingId) {
      console.log('session', session)
      const sbCourt = document.getElementById('sbCourt')
      const sbTime = document.getElementById('sbTime')
      const sbPrice = document.getElementById('sbPrice')
      const sbSlots = document.getElementById('sbSlots')
      const sbCount = document.getElementById('sbCount')
      const sbRemain = document.getElementById('sbRemain')
      const sbQty = document.getElementById('sbQty')

      if (!sbCourt || !sbTime) return

      sbCourt.value = session.court_name
      sbTime.value = `${session.start_time} - ${session.end_time}`

      sbPrice.value = formatMoney(session.price)
      sbPrice.dataset.raw = session.price

      sbSlots.value = session.slots
      sbCount.value = session.count

      const remain = Math.max(0, session.slots - session.count)

      sbRemain.value = remain
      sbRemain.dataset.raw = remain

      sbQty.value = remain > 0 ? 1 : 0

      onQtyChange()

      const confirmBtn = document.getElementById('sbConfirm')
      confirmBtn.dataset.bookingId = bookingId

      $('#shareBookingModal').modal('show')
    }
    window.openShareModal = function (bookingId) {
      let session = window.sharedSessions?.[bookingId]
      // nếu đã có cache
      if (session) {
        render(session, bookingId)
        return
      } else {
        // nếu chưa có thì gọi API
        $.ajax({
          url: booking_index.url_show.replace('__ID__', bookingId),
          type: 'GET',
          dataType: 'json',

          success: function (result) {
            const session = result.booking

            // cache lại
            window.sharedSessions = window.sharedSessions || {}
            window.sharedSessions[bookingId] = session

            render(session, bookingId)
          },

          error: function (xhr) {
            console.error(xhr)
            main_layout.alert_main('Có lỗi khi gửi yêu cầu đăng ký', 'error')
          },
        })
      }
    }
  })
})()

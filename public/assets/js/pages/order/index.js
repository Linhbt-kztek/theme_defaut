function showOrderDetail(orderId) {
  main_layout.show_loader()

  $.ajax({
    url: `/admin/order/show/${orderId}`,
    type: 'GET',
    dataType: 'json',

    success: function (result) {
      main_layout.hide_loader()
      if (result.status == 200) {
        data = result.data
        document.querySelector('.order-code').innerText = '#' + data.order.code
        document.querySelector('.order-created-at').innerText = data.order.created_at
        document.querySelector('.booking-created-at').innerText = data.booking.created_at
        document.querySelector('.booking-created-by').innerText = data.booking.created_by
        document.querySelector('.booking-code').innerText = data.booking.booking_code
        const badgeEl = document.querySelector('.booking-status')

        let text = 'Đặt cọc'
        let badgeClass = 'badge-primary'

        const status = data.order.status
        const activationDate = data.order.activation_date
        const billSuccess = data.order.bill_success

        console.log(status, activationDate, billSuccess, new Date(activationDate) < new Date())

        if (status == 2) {
          text = 'Đã hoàn'
          badgeClass = 'badge-warning'
        } else if (status == 1) {
          if (activationDate && new Date(activationDate) < new Date()) {
            if (billSuccess == 1) {
              text = 'Đã gửi HĐĐT'
              badgeClass = 'badge-info'
            } else {
              text = 'Đã thanh toán'
              badgeClass = 'badge-success'
            }
          } else {
            text = 'Đặt cọc'
            badgeClass = 'badge-primary'
          }
        }

        badgeEl.className = 'booking-status badge-custome ' + badgeClass
        badgeEl.innerText = text

        const bookingTypeText = {
          1: 'Sân lẻ',
          2: 'Sân cố định',
          3: 'Tham gia sân ghép',
        }

        document.querySelector('.booking-type').innerText =
          bookingTypeText[data.booking.type] ?? 'Không xác định'

        document.querySelector('.order-amonut').innerText =
          Number(data.order.amount).toLocaleString('vi-VN') + ' đ'
        // document.querySelector('.order-created-by').innerText = data.order.created_by
        document.querySelector('.order-payment-method').innerText = data.order.payment_method_text

        renderCustomerInfo(data.customer)
        if (data.order.type != 2) {
          $('#expire').hide()
          $('#booking-note').hide()
        } else {
          document.querySelector('.booking-note').innerText = data.booking.note
          document.querySelector('.booking-range-time').innerText = data.details.length
            ? `${data.details[0].date} - ${data.details[data.details.length - 1].date}`
            : '-'
        }

        if (data.order.type === 3) {
          renderShareSlotTable(data.details)
        } else {
          renderNormalBookingTable(data.details)
        }
        $('#orderDetailModal').modal('show')
      }
    },

    error: function () {
      main_layout.hide_loader()
      main_layout.alert_main('Lỗi tải chit iết hóa đơn', 'error', 'center')
    },
  })
}

function renderCustomerInfo(customer) {
  document.querySelector('.customer-name').innerText = customer.name ?? ''
  document.querySelector('.customer-code').innerText = customer.code
  document.querySelector('.customer-email').innerText = customer.email ?? 'Không có dữ liệu'
  document.querySelector('.customer-phone').innerText = customer.phone ?? 'Không có dữ liệu'
}

function renderNormalBookingTable(details) {
  document.getElementById('table-normal').classList.remove('d-none')
  document.getElementById('table-share-slot').classList.add('d-none')

  const tbody = document.querySelector('#table-normal tbody')
  tbody.innerHTML = ''

  details.forEach(row => {
    tbody.innerHTML += `
            <tr>
                <td class="text-center">${row.stt}</td>
                <td class="text-start">${row.court_name}</td>
                <td class="text-center">${row.date}</td>
                <td class="text-center">${row.time_range}</td>
                <td class="text-center">${row.duration}</td>
                <td class="text-end">${row.amount.toLocaleString()} đ</td>
            </tr>
        `
  })
}
function renderShareSlotTable(details) {
  document.getElementById('table-normal').classList.add('d-none')
  document.getElementById('table-share-slot').classList.remove('d-none')

  const tbody = document.querySelector('#table-share-slot tbody')
  tbody.innerHTML = ''

  details.forEach(row => {
    tbody.innerHTML += `
            <tr>
                <td class="text-center">${row.stt}</td>
                <td class="text-center">${row.court_name}</td>
                <td class="text-center">${row.date}</td>
                <td class="text-center">${row.time_range}</td>
                <td class="text-center">${row.slots}</td>
                <td class="text-center">${row.price.toLocaleString()} đ</td>
                <td class="text-center fw-bold">${row.amount.toLocaleString()} đ</td>
            </tr>
        `
  })
}

function deleteOrder(id, param, url) {
  Swal.fire({
    title: '',
    text: 'Bạn có chắc chắn muốn xóa hóa đơn không ?',
    type: 'warning',
    showCancelButton: true,
    confirmButtonColor: '#d33',
    cancelButtonColor: '#3085d6',
    confirmButtonText: 'Có',
    cancelButtonText: 'Không',
  }).then(datault => {
    if (datault.value) {
      main_layout.show_loader()
      $.ajax({
        url: url,
        type: 'DELETE',
        headers: headersClient,
        data: {
          _token: main.token,
        },
        success: function (datault) {
          main_layout.hide_loader()
          if ('success' in datault) {
            Swal.fire({
              text: 'Xóa hóa đơn thành công !',
              icon: 'success',
              timer: 1500,
            })
            window.location.reload()
          } else {
            Swal.fire({
              text: datault['error'],
              icon: 'error',
              timer: 1500,
            })
          }
        },
        Error: function (datault) {
          main_layout.hide_loader()
          Swal.fire({
            text: 'Xóa hóa đơn không thành công!',
            icon: 'error',
            timer: 1500,
          })
        },
      })
    }
  })
}

function sendE_Invoice($orderId) {
  main_layout.show_loader()
  $.ajax({
    url: 'order/send-e-invoice/' + $orderId,
    method: 'POST',
    contentType: 'application/json',
    headers: {
      'X-CSRF-TOKEN': main.token,
    },
    success: function (res) {
      if (res.status) {
        main_layout.alert_main(res.message, 'success', 'center');
        window.location.reload()
      } else {
        main_layout.alert_main(res.message, 'error', 'center')
      }
      main_layout.hide_loader()
    },
    error: function (err) {},
  })
}

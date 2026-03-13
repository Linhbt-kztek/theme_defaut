let currentStatus = 0
let currentCustomer = null

function showCustomerForm(context) {
  $.ajax({
    url: customer_index.url_generate_code,
    type: 'GET',
    success: function (result) {
      if (result.status == 200) {
        $('#saveFormModal')[0].reset()
        $('#createPreviewAvatar').attr('src', '/images/default-user.jpg')
        $('#code').val(result.data ?? '')

        $('#customerFormModal')
          .off('shown.bs.modal')
          .on('shown.bs.modal', function () {
            const nameInput = this.querySelector('#name')
            if (nameInput) {
              nameInput.focus()
            }
          })
          .modal('show')

        $('#modalLabel').text('Thêm mới khách hàng')
        $('#context').val(context)
      }
    },
  })
}

function submit() {
  main_layout.show_loader()
  let id = $('input[name="customer_id"]').val() ?? null
  let objectContext = $('input[name="context"]').val() ?? null
  if (id) {
    url = customer_index.url_edit.replace('__ID__', id)
  } else {
    url = customer_index.url_store
  }

  let data = new FormData($('#saveFormModal')[0])
  $.ajax({
    url: url,
    type: 'POST',
    data: data,
    processData: false,
    contentType: false,
    success: function (result) {
      if (objectContext == 'customer') {
        if (result.status == 200) {
          $('#customerFormModal').modal('hide')
          main_layout.hide_loader()
          main_layout.alert_main(result.message, 'success', 'center')
          location.reload()
        } else {
          main_layout.alert_main(result.message, 'error', 'center')
        }
      }

      if (objectContext == 'booking') {
        // hiển thị thông tin khách hàng vừa tạo
        main_layout.hide_loader()
        $('#customerFormModal').modal('hide')
        main_layout.alert_main(result.message, 'success', 'center')
        if (result.data) {
          const display = `${result.data.name} | ${result.data.phone}`
          $('.customer-input').each(function () {
            $(this).val(display)
          })

          $('input[name="customer_id"]').each(function () {
            $(this).val(result.data.id)
          })

          const dropdownList = document.querySelector('#customerDropdown .dropdown-list')
          if (!dropdownList) return

          const item = document.createElement('div')
          item.className = 'customer-item'
          item.dataset.id = result.data.id
          item.dataset.name = result.data.name.toLowerCase()
          item.dataset.phone = result.data.phone ?? ''
          item.dataset.address = result.data.address.toLowerCase() ?? ''

          item.innerHTML = `
              <div class="fw-bold">${result.data.name} | ${result.data.phone ?? ''}</div>
              <div class="small text-muted"></div>
          `
          dropdownList.prepend(item)
        }
      }
    },
  })
}

function showEditCustomerForm(id) {
  $('#modalLabel').text('Cập nhật khách hàng')
  $('#btnSave').text('Cập nhật')
  $('input[name="customer_id"]').val(id)
  $('#context').val('customer')

  let url = customer_index.url_show.replace('__ID__', id)

  $.ajax({
    url: url,
    type: 'GET',
    success: function (result) {
      main_layout.hide_loader()
      $('#name').val(result.data.name ?? '')
      $('#gender').val(result.data.gender ?? '')
      $('#code').val(result.data.code ?? '')

      $('#phone').val(result.data.phone ?? '')
      $('#email').val(result.data.email ?? '')
      $('#adress').val(result.data.adress ?? '')
      $('#createPreviewAvatar').attr('src', result.data.url_image)

      if (result.status !== 200) {
        main_layout.alert_main(result.message, 'error', 'center')
        return
      }
      $('#customerFormModal').modal('show')
    },
  })
}

function handleResizePage(perSize) {
  main_layout.show_loader()
  let key_search = $('#key_search').val()
  let search_confim = 1
  let baseUrl = $('#pageLimit').data('url')

  let url =
    baseUrl +
    '?key_search=' +
    encodeURIComponent(key_search) +
    '&search_confim=' +
    search_confim +
    '&limit=' +
    perSize

  window.location.href = url
}

function previewImageAvatar(event) {
  var reader = new FileReader()
  reader.onload = function () {
    $('#createPreviewAvatar').attr('src', reader.result)
  }
  profile_image = event.target.files[0]
  reader.readAsDataURL(event.target.files[0])
}

function openBookingHistory(customerId) {
  currentCustomer = customerId
  $('#hisStatus span').removeClass('active')
  $('#hisStatus span[value="0"]').addClass('active')

  currentStatus = 0
  loadBookingHistory()
  $('#bookingHistoryModal').modal('show')
}

function loadBookingHistory(reload = false) {
  const date = $('#booking-history-date').val()

  $.get(
    '/admin/booking/history/' + currentCustomer,
    {
      status: currentStatus,
      date: date,
    },
    function (res) {
      if (res.status !== 200) return
      $('#booking-history-date').val(res.dateRange)
      renderHistory(res.data)

      if (!reload) {
        renderSummary(res.data.summary)
      }
    }
  )
}

function renderSummary(data) {
  $('#sumDeposit').text(formatMoney(data.deposit))
  $('#sumPaid').text(formatMoney(data.paid))
  $('#sumRefund').text(formatMoney(data.refund))
}

function renderHistory(data) {
  /* CUSTOMER */
  if (data.customer) {
    $('.customer-name').text(data.customer.name)
    $('.customer-code').text(data.customer.code ?? '')
    $('.customer-phone').text(data.customer.phone ?? 'Không có dữ liệu')
    $('.customer-email').text(data.customer.email ?? 'Không có dữ liệu')
    $('#historyCustomer')
      .find('[name="customer_avatar"]')
      .attr('src', data.customer.url_image ?? '/assets/images/user-default.jpg')
  }

  const list = $('#historyList')
  list.empty()

  /* EMPTY */
  if (!data.list || data.list.length === 0) {
    list.html(`
      <div class="text-center text-muted py-4">
        Không có dữ liệu
      </div>
    `)

    return
  }

  const template = document.getElementById('bookingHistory')

  data.list.forEach(b => {
    const clone = template.content.cloneNode(true)

    const item = $(clone).find('.history-item')

    /* CODE */
    item.find('.his-code').text('#' + b.code)

    /* BADGE */
    item.find('.his-badge').replaceWith(getStatusBadge(b.time_status, b.time_status_text))

    item.find('.his-type').replaceWith(getTypeBadge(b.type))

    /* TIME */
    item.find('.his-time').text(b.created_at)

    /* AMOUNT */
    item.find('.his-amount').text(formatMoney(b.amount))

    /* INFO */
    let info = ''

    if (b.display.court) {
      info += b.display.court
    }

    if (b.display.time) {
      info += ' | ' + b.display.time
    }

    if (b.display.date) {
      info += ' | ' + b.display.date
    }

    if (b.display.quantity) {
      info += ' | SL: ' + b.display.quantity
    }

    item.find('.his-info').text(info)

    /* DETAIL */
    const detail = item.find('.detail')

    detail.attr('id', 'detail-' + b.id).html(renderDetails(b.details, b.type, b.note))

    item.find('.toggleDetail').attr('data-id', b.id)

    list.append(item)
  })
}

function renderDetails(details, type, note = '') {
  if (!details || details.length === 0) return ''

  let html = ''

  /* ================= TYPE = 2 ================= */
  if (type === 2) {
    // Lấy danh sách ngày
    const dates = details.map(d => d.date).filter(d => d)

    // Sắp xếp ngày
    dates.sort((a, b) => {
      return new Date(a.split('/').reverse().join('-')) - new Date(b.split('/').reverse().join('-'))
    })

    const startDate = dates[0]
    const endDate = dates[dates.length - 1]

    html = `
      <div class="small text-muted">
        <div><b>Thời gian:</b> ${startDate} - ${endDate}</div>
        <div><b>Ghi chú:</b> ${note || '---'}</div>
      </div>
    `

    return html
  }

  /* ================= TYPE = 1 ================= */
  if (type === 1) {
    const date = details[0].date

    let timeHtml = ''

    details.forEach(d => {
      timeHtml += `
      <div class="">${d.court}: ${d.time}</div>
    `
    })

    html = `
    <div class="small text-muted">
      <div><b>Ngày:</b> ${date}</div>
      <div class="d-flex gap-2"><b>Khung giờ:</b><div class="row">${timeHtml}</div> </div>
    </div>
  `

    return html
  }

  /* ================= TYPE = 3 ================= */
  if (type === 3) {
    html = '<ul class="small text-muted mb-0 p-0">'

    details.forEach(d => {
      html += `
        <div><b>Ngày:</b> ${d.date}</div>
        <div><b>Sân đặt:</b> ${d.court} </div>
        <div><b>Khung giờ:</b> ${d.time}</div>
        <div><b>Số lượt đăng ký:</b> ${d.slots ?? 0} người</div>
      `
    })

    html += '</ul>'

    return html
  }

  html += '</ul>'

  return html
}

document.addEventListener('DOMContentLoaded', () => {
  $(document).on('click', '.toggleDetail', function () {
    const id = $(this).data('id')

    $('#detail-' + id).toggleClass('d-none')

    $(this).toggleClass('fa-chevron-down fa-chevron-up')
  })

  $('#hisStatus').on('click', 'span', function () {
    $('#hisStatus span').removeClass('active')
    $(this).addClass('active')

    currentStatus = $(this).attr('value')

    loadBookingHistory(true)
  })
  $('#booking-history-date').on('input change', function () {
    loadBookingHistory()
  })
})

function getStatusBadge(key, text) {
  let cls = 'danger'

  if (key == 1) cls = 'primary'
  if (key == 2) cls = 'success'
  if (key == 3) cls = 'warning'
  if (key == 4) cls = 'secondary'

  return `<span class="badge-custome badge-${cls}">${text}</span>`
}

function getTypeBadge(type) {
  let cls = 'danger'
  let text = ''

  if (type == 1) {
    cls = 'success'
    text = 'Sân lẻ'
  }

  if (type == 2) {
    cls = 'info'
    text = 'Sân cố định'
  }
  if (type == 3) {
    cls = 'warning'
    text = 'Tham gia sân ghép'
  }

  return `<span class="badge bg-${cls}">${text}</span>`
}

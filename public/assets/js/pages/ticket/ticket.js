let ticket_id = ''
let limit = 0
let is_used_count = 0
let html = ''
let newlyAddedCustomers = [] // Thêm mảng để track khách hàng mới thêm
let t_expiry_date_from = ''
let t_expiry_date_to = ''
let date_of_use = ''
let ticket_code = ''

//  tính toán ngày hết hạn cho thành viên mới
function calculateNewMemberExpiry() {
  const today = new Date()
  const startDate = new Date(today)
  let endDate = new Date(today)

  if (date_of_use && date_of_use > 0) {
    // Cộng thêm số ngày sử dụng
    endDate.setDate(today.getDate() + parseInt(date_of_use))
  }

  return {
    from: startDate,
    to: endDate,
  }
}

//  format ngày cho hiển thị
function formatExpiryForNewMember() {
  const expiry = calculateNewMemberExpiry()
  const fromFormatted = main.formatDate(expiry.from.toISOString())
  const toFormatted = main.formatDate(expiry.to.toISOString())

  return {
    from: fromFormatted,
    to: toFormatted,
  }
}

function addCustomerToTable(cardData) {
  const customer = cardData.customers[0]
  const template = $('#memberTableRowTemplate').html()

  newlyAddedCustomers.push(customer.id)
  // Tính toán ngày hết hạn cho thành viên mới
  const { from, to } = formatExpiryForNewMember()

  const rowHtml = template
    .replace(/\[customer_id\]/g, customer.id)
    .replace('[stt]', $('#selectedMembersTable tr').length + 1)
    .replace('[code]', customer.code)
    .replace('[customer_name]', customer.name)
    .replace('[customer_email]', customer.email || '')
    .replace('[customer_phone]', customer.phone || '')
    .replace('[customer_address]', customer.address || '')
    .replaceAll('[ticket_id]', ticket_id || '')
    .replace('[expiry_from]', from)
    .replace('[expiry_to]', to)
    .replace('[count_turn]', 0)
    .replace('[card_number]', cardData.card_number || '')
    .replaceAll('[card_id]', cardData.id || '')
    .replace('[row_class]', 'newly-added-member')
    .replaceAll('[limit]', '')

  const $row = $(rowHtml)

  // Đánh dấu là thành viên mới
  $row.attr('data-is-new-member', 'true')

  $('#selectedMembersTable').append($row)
  $(`#expiryDateFrom_group_${cardData.id}`).val(`${from}`)
  $(`#expiryDateTo_group_${cardData.id}`).val(`${to}`)
  updateTableSTT()
}

function showDatePicker(ticketId) {
  let dateRange = $(`#dateText_${ticketId}`).clone().children().remove().end().text().trim()
  let [from, to] = dateRange.split(' - ')
  $(`.cleave-date-from[data-ticket-id="${ticketId}"]`).val(from)
  $(`.cleave-date-to[data-ticket-id="${ticketId}"]`).val(to)

  // Nếu đã có thẻ hoặc là vé nhóm, cho phép thay đổi hạn
  $(`#datePickerContainer_${ticketId}`).show()
  $(`#datePickerContainer_${ticketId}`).css('display', 'flex')
  $(`#dateText_${ticketId}`).hide()
}

function displayExistingMembers(linkedCustomers) {
  const template = $('#memberTableRowTemplate').html()

  linkedCustomers.forEach((link, index) => {
    if (link.card && link.card.customers && link.card.customers.length > 0) {
      const customer = link.card.customers[0]

      const rowHtml = template
        .replaceAll('[customer_id]', customer.id)
        .replace('[stt]', index + 1)
        .replace('[code]', customer.code ?? '')
        .replace('[customer_name]', customer.name)
        .replace('[customer_email]', customer.email || '')
        .replace('[customer_phone]', customer.phone || '')
        .replace('[customer_address]', customer.address || '')
        .replaceAll('[ticket_id]', link.ticket_id || '')
        .replace('[count_turn]', `${link.count_turn ?? 0}`)
        .replace('[card_number]', `${link.card.card_number ?? 0}`)

        .replace('[limit]', link.turn_of_use || '')
        .replace('[expiry_from]', `${main.formatDate(link.expiry_date_from)}`)
        .replace('[expiry_to]', `${main.formatDate(link.expiry_date_to)}`)

        .replaceAll('[card_id]', link.card_id || '')
        .replace('[row_class]', '') // Không có class đặc biệt cho thành viên cũ

      $('#selectedMembersTable').append(rowHtml)
      $(`#expiryDateFrom_group_${link.card.id}`).val(`${main.formatDate(link.expiry_date_from)}`)
      $(`#expiryDateTo_group_${link.card.id}`).val(`${main.formatDate(link.expiry_date_to)}`)
      $(`#countTurn_single_${link.card.id}`).val(link.turn_of_use)
    }
  })
  updateMemberCount()
}

function updateMemberCount(ticket_code) {
  const totalMembers = $('#selectedMembersTable tr').length
  $('#memberCount').text(`${totalMembers}/${limit}`)

  if (ticket_code) {
    $(`#ticket_${ticket_code} .bg-info`).text(`${totalMembers}/${limit}`)
    // const currentHtml = $(`#ticket_${ticket_code}`).html()
    // const newHtml = currentHtml.replace('[member_count]', totalMembers)
    // $(`#ticket_${ticket_code}`).html(newHtml)
  }
}

let selectedCustomers = []
let searchTimeout

function initializeCustomerSearch() {
  selectedCustomers = []
  $('#selectedCustomersBadges').empty()
  $('#customerSearchInput').val('')
  updateMemberCount()

  // XÓA event listeners cũ TRƯỚC KHI thêm mới
  $('#customerSearchInput').off('input.customerSearch')
  $(document).off('click.customerSearch')

  // Thêm event listeners với namespace
  $('#customerSearchInput').on('input.customerSearch', function () {
    const searchTerm = $(this).val().trim()

    clearTimeout(searchTimeout)

    if (searchTerm.length >= 2) {
      searchTimeout = setTimeout(() => {
        searchCustomers(searchTerm)
      }, 300)
    } else {
      hideSearchResults()
    }
  })

  $(document).on('click.customerSearch', function (e) {
    if (!$(e.target).closest('#customerSearchInput, #customerSearchResults').length) {
      hideSearchResults()
    }
  })
}

let currentSearchRequest = null

function searchCustomers(searchTerm) {
  // Cancel request trước đó
  if (currentSearchRequest) {
    currentSearchRequest.abort()
  }

  currentSearchRequest = $.ajax({
    url: '/admin/card/search-for-group',
    type: 'GET',
    data: { keyword: searchTerm },
    success: function (response) {
      currentSearchRequest = null
      if (response.status && response.data.length > 0) {
        displaySearchResults(response.data)
      } else {
        showNoResults()
      }
    },
    error: function (xhr) {
      currentSearchRequest = null
      if (xhr.statusText !== 'abort') {
        showSearchError()
      }
    },
  })
}

function displaySearchResults(cards) {
  const template = $('#customerSearchResultTemplate').html()
  html = '' // Reset html

  cards.forEach(card => {
    // Kiểm tra dữ liệu hợp lệ
    if (card.customers && card.customers.length > 0) {
      const customer = card.customers[0]
      const initial = customer.name.charAt(0).toUpperCase()
      html += template
        .replace(/\[customer_id\]/g, customer.id)
        .replace('[customer_initial]', initial)
        .replace('[customer_name]', customer.name)
        .replace('[code]', customer.code ?? '')
        .replace('[customer_email]', customer.email || '')
        .replace('[customer_address]', customer.address || '')
        .replace('[customer_birthday]', main.formatDate(customer.birthday) ?? '')
        .replace('[customer_phone]', customer.phone || '')
        .replace('[card_id]', card.id || '')
        .replace('[card_number]', card.card_number || '')
    }
  })

  $('#customerSearchResults').html(html).show()

  // XÓA event listeners cũ trước khi thêm mới
  $('.customer-search-item').off('click.customerSelect')

  // Thêm event listeners với namespace
  $('.customer-search-item').on('click.customerSelect', function () {
    const customerId = $(this).data('customer-id')
    const cardData = cards.find(
      c => c.customers && c.customers.length > 0 && c.customers[0].id == customerId
    )
    if (cardData && cardData.customers[0]) {
      selectCustomerDirectly(cardData)
    }
  })
}

function selectCustomerDirectly(cardData) {
  const customer = cardData.customers[0]

  // Kiểm tra khách hàng đã tồn tại trong bảng chưa
  if ($(`#selectedMembersTable tr[data-customer-id="${customer.id}"]`).length > 0) {
    showModalAlert('Khách hàng đã được chọn!', 'warning', 3000)
    return
  }

  // Kiểm tra giới hạn số lượng
  const currentTotal = $('#selectedMembersTable tr').length

  if (currentTotal >= limit) {
    showModalAlert('Bạn đã đến giới hạn số lượng khách hàng cho vé này!', 'error', 3000)
    return
  }

  // Thêm trực tiếp vào bảng
  addCustomerToTable(cardData)

  // Clear search
  $('#customerSearchInput').val('')
  hideSearchResults()
  updateMemberCount()
}

// khi không tìm thấy kết quả
function showNoResults() {
  $('#customerSearchResults')
    .html('<div class="dropdown-item text-muted">Không tìm thấy khách hàng nào</div>')
    .show()
}

// khi lỗi gọi api
function showSearchError() {
  $('#customerSearchResults')
    .html('<div class="dropdown-item text-danger">Lỗi khi tìm kiếm</div>')
    .show()
}

function hideSearchResults() {
  $('#customerSearchResults').hide()
}

function removeMemberFromTable(customerId) {
  $(`#selectedMembersTable tr[data-customer-id="${customerId}"]`).remove()

  // Xóa khỏi mảng newly added nếu có
  const index = newlyAddedCustomers.indexOf(parseInt(customerId))
  if (index > -1) {
    newlyAddedCustomers.splice(index, 1)
  }

  updateTableSTT()
  updateMemberCount()
}

function updateTableSTT() {
  $('#selectedMembersTable tr').each(function (index) {
    $(this)
      .find('td:first')
      .text(index + 1)
  })
}

function saveGroupTicketSetting() {
  const members = []
  let isValid = true

  $('#selectedMembersTable tr').each(function () {
    const customerId = $(this).data('customer-id')
    const cardId = $(this).data('card-id')
    const limit = $(this).data('limit')
    const expiry_from = $(this).data('expiry-from')
    const expiry_to = $(this).data('expiry-to')

    if (!checkDate(cardId)) {
      isValid = false
      return false
    }

    members.push({
      customer_id: customerId,
      list_card: { [ticket_id]: cardId },
      limit: limit,
      expiry_from: expiry_from,
      expiry_to: expiry_to,
      card_id: cardId,
      ticket_id: ticket_id,
    })
  })
  if (!isValid) {
    main_layout.hide_loader()
    return
  }

  main_layout.show_loader()

  $.ajax({
    url: '/admin/ticket/linkMultiCustomerToTicket',
    type: 'POST',
    data: {
      members: members,
      _token: main.token,
      ticket_id: ticket_id,
    },
    success: function (response) {
      $('#groupTicketSetting').off('hidden.bs.modal')
      $('#addCardModal').off('hidden.bs.modal')

      main_layout.hide_loader()

      Swal.fire({
        text: 'Cài đặt thành công !',
        icon: 'success',
        timer: 1500,
        showConfirmButton: false,
      }).then(() => {
        reopenGroupTicketSettingWithUpdatedData()
      })

      updateMemberCount(ticket_code)
    },
    error: function () {
      main_layout.hide_loader()
      Swal.fire({
        text: 'Lưu không thành công!',
        icon: 'error',
        timer: 1500,
      })
    },
  })
}

function reopenGroupTicketSettingWithUpdatedData() {
  $('#groupTicketSetting').off('hidden.bs.modal')
  $('#addCardModal').off('hidden.bs.modal')
  $('#customerEditModal').off('hidden.bs.modal')

  $('.modal').modal('hide')
  $('.modal-backdrop').remove()
  $('body').removeClass('modal-open')
  $('body').css('padding-right', '')

  $('#groupTicketSetting').removeClass('show')
  $('#addCardModal').removeClass('show')
  $('#customerEditModal').removeClass('show')

  newlyAddedCustomers = []

  setTimeout(() => {
    $.ajax({
      url: '/admin/ticket/show',
      type: 'GET',
      data: {
        id: ticket_id,
      },
      success: function (response) {
        $('#selectedMembersTable').empty()

        // Cập nhật thông tin ticket
        limit = response.data.ticket_type.number_of_people ?? '-'
        ticket_code = response.data.code ?? null
        t_expiry_date_from = response.data.expiry_date_from
        t_expiry_date_to = response.data.expiry_date_to
        date_of_use = response.data.ticket_type.date_of_use ?? null

        $('#limit_member').text(limit)
        $('#tt_code').text(response.data.code)
        $('#tt_name').text(response.data.ticket_type_name)
        $('#tt_order_code').text(response.data.order.code_order)
        $('#tt_order_customer').text(response.data?.order?.customer?.name ?? 'Khách lẻ')
        $('#tt_date').text(main.formatDate(response.data.order.created_at))

        // Hiển thị danh sách khách hàng đã cập nhật
        if (response.linked_for_customer && response.linked_for_customer.length > 0) {
          displayExistingMembers(response.linked_for_customer)
        }

        setTimeout(() => {
          $('#groupTicketSetting').modal('show').css('display', 'block')

          // Khởi tạo lại customer search
          initializeCustomerSearch()
        }, 200)
      },
      error: function () {
        console.error('Lỗi khi tải lại dữ liệu modal')

        setTimeout(() => {
          $('#groupTicketSetting').modal({
            backdrop: false,
            keyboard: true,
            show: true,
          })
          initializeCustomerSearch()
          showModalAlert('Có lỗi khi tải dữ liệu mới!', 'error', 2000)
        }, 200)
      },
    })
  }, 300)
}

function hideModalAlert() {
  $('#warning-message').fadeOut(300)
}

function showModalAlert(message, type = 'warning', duration = 1500) {
  const alertContainer = $('#modal-alert-container')
  const alert = $('#modal-alert')
  const alertMessage = $('.alert-message')
  const alertIcon = $('.alert-icon')

  // Cấu hình theo type
  const alertConfig = {
    success: {
      class: 'alert-success',
      icon: 'ri-check-circle-line',
    },
    warning: {
      class: 'alert-warning',
      icon: 'ri-alert-line',
    },
    error: {
      class: 'alert-danger',
      icon: 'ri-error-warning-line',
    },
    info: {
      class: 'alert-info',
      icon: 'ri-information-line',
    },
  }

  const config = alertConfig[type] || alertConfig['warning']

  // Reset classes và thêm class mới
  alert.removeClass('alert-success alert-warning alert-danger alert-info').addClass(config.class)

  // Set icon và message
  alertIcon.removeClass().addClass(`alert-icon me-2 ${config.icon}`)
  alertMessage.text(message)

  // Hiển thị alert
  alertContainer.slideDown(300)

  // Tự động ẩn sau duration (nếu có)
  if (duration > 0) {
    setTimeout(() => {
      hideModalAlert()
    }, duration)
  }
}

function hideModalAlert() {
  $('#modal-alert-container').slideUp(300)
}

// Thêm callback để quay lại modal trước đó
let previousModal = null

function showGroupTicketSetting(id, callback = null) {
  hideModalAlert()
  $('#selectedMembersTable').empty()
  $('.modal-backdrop').remove()
  $('body').removeClass('modal-open')
  ticket_id = id
  newlyAddedCustomers = []
  previousModal = callback // Lưu callback để quay lại modal trước đó

  $.ajax({
    url: '/admin/ticket/show',
    type: 'GET',
    data: {
      id: id,
    },
    success: function (response) {
      limit = response.data.ticket_type.number_of_people ?? '-'
      ticket_code = response.data.code ?? null
      t_expiry_date_from = response.data.expiry_date_from
      t_expiry_date_to = response.data.expiry_date_to
      date_of_use = response.data.ticket_type.date_of_use ?? null

      $('#limit_member').text(limit)
      $('#tt_code').text(response.data.code)
      $('#tt_name').text(response.data.ticket_type_name)
      $('#tt_order_code').text(response.data.order.code_order)
      $('#tt_order_customer').text(response.data?.order?.customer?.name ?? 'Khách lẻ')
      $('#tt_date').text(main.formatDate(response.data.order.created_at))

      // Hiển thị danh sách khách hàng đã được add từ trước
      if (response.linked_for_customer && response.linked_for_customer.length > 0) {
        displayExistingMembers(response.linked_for_customer)
      }

      $('#groupTicketSetting').modal({ backdrop: false, keyboard: true })
      initializeCustomerSearch()
    },
    error: function () {},
  })
}

function closeGroupTicketSetting() {
  $('#groupTicketSetting').modal('hide').remove('show').css('display', 'none')
  $('.modal-backdrop').remove()
  $('body').removeClass('modal-open')
  $('#groupTicketSetting').removeClass('show')
  $('#addCardModal').modal('show').css('display', 'block')
}

function getUpdateDate(cardId) {
  const fromValue = $(`#expiryDateFrom_group_${cardId}`).val()
  const toValue = $(`#expiryDateTo_group_${cardId}`).val()
  const limit = $(`#countTurn_single_${cardId}`).val() ?? null
  checkDate(cardId)
  $(`tr[data-card-id="${cardId}"]`)
    .attr('data-expiry-from', fromValue)
    .attr('data-expiry-to', toValue)
    .attr('data-limit', limit ?? '')
}

function getUpdateDateSingle(ticketId) {
  const fromValue = $(`#expiryDateFrom_single_${ticketId}`).val()
  const toValue = $(`#expiryDateTo_single_${ticketId}`).val()
  const limit = $(`#countTurn_single_${ticketId}`).val() ?? null
  checkDateSingle(ticketId)
  $(`tr[data-ticket-id="${ticketId}"]`)
    .attr('data-expiry-from', fromValue)
    .attr('data-expiry-to', toValue)
    .attr('data-limit', limit ?? '')
}

// cho vé nhóm
function checkDate(cardId) {
  const fromValue = $(`#expiryDateFrom_group_${cardId}`).val()?.trim()
  const toValue = $(`#expiryDateTo_group_${cardId}`).val()?.trim()
  const $row = $(`tr[data-card-id="${cardId}"]`)
  $row.removeClass('row-invalid')

  
  if (!fromValue || !toValue) {
     main_layout.alert_main('Vui lòng nhập hạn sử dụng!', 'error')
    $row.addClass('row-invalid')
    return false
  }


  if (!isValidFullDate(fromValue) || !isValidFullDate(toValue)) {
    main_layout.alert_main('Thời hạn không hợp lệ (định dạng dd/mm/yyyy).', 'error')
    $row.addClass('row-invalid')
    return false
  }

  const from = moment(fromValue, 'DD/MM/YYYY', true)
  const to = moment(toValue, 'DD/MM/YYYY', true)

  if (!from.isValid() || !to.isValid()) {
    main_layout.alert_main('Ngày không hợp lệ.', 'error')
    $row.addClass('row-invalid')
    return false
  }

  if (!from.isBefore(to)) {
    main_layout.alert_main('Ngày bắt đầu phải nhỏ hơn ngày kết thúc.', 'error')
    $row.addClass('row-invalid')
    return false
  }

  return true
}

// cho vé thường
function checkDateSingle(ticketId) {
  console.log(ticketId);
  const fromValue = $(`#expiryDateFrom_single_${ticketId}`).val()?.trim()
  const toValue = $(`#expiryDateTo_single_${ticketId}`).val()?.trim()
  const $row = $(`#addCardModalItem_${ticketId}`)

  console.log(fromValue,toValue )

  $row.removeClass('row-invalid')

  if (!fromValue || !toValue) {
     main_layout.alert_main('Vui lòng nhập hạn sử dụng!', 'error')
    $row.addClass('row-invalid')
    return false
  }

  if (!isValidFullDate(fromValue) || !isValidFullDate(toValue)) {
    main_layout.alert_main('Thời hạn không hợp lệ (định dạng dd/mm/yyyy).', 'error')
    $row.addClass('row-invalid')
    return false
  }


  return true
}

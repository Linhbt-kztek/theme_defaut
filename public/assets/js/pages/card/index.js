let allData = []
let paginationData = []
let count = 0
let next_url = ''
let filteredData = []
let currentPage = 1
let perPage = 54
let searchTimeout = null
let isExpiringFilter = false

function loadInitialData() {
  main_layout.show_loader()

  count = 0
  $('#list-card').html('')

  $.ajax({
    url: '/admin/card/getAll',
    type: 'GET',
    data: {
      per_page: perPage,
    },
    success: function (result) {
      main_layout.hide_loader()
      if (result && result.status === 200) {
        allData = result.data
        paginationData = result.pagination
        filteredData = [...allData]
        displayData()
      } else {
        main_layout.alert_main('Có lỗi xảy ra khi tải dữ liệu!', 'error')
      }
    },
    error: function (xhr, status, error) {
      main_layout.hide_loader()
      console.error('Error:', error)
      main_layout.alert_main('Có lỗi xảy ra khi tải dữ liệu!', 'error')
    },
  })
}

function fomatFilterDate(filters, dateRange) {
  // Xử lý date range
  if (dateRange) {
    let startStr, endStr
    if (dateRange.includes('đến')) {
      ;[startStr, endStr] = dateRange.split(' đến ')
    } else {
      ;[startStr, endStr] = [dateRange, dateRange]
    }

    if (startStr && endStr) {
      const [startDay, startMonth, startYear] = startStr.split('-')
      const [endDay, endMonth, endYear] = endStr.split('-')
      filters.date_from = `${startYear}-${startMonth}-${startDay}`
      filters.date_to = `${endYear}-${endMonth}-${endDay}`
    }
  }
}

// Hàm search mới sử dụng API
function searchCardsByCardInfo(page = 1, loadMore = false) {
  const keySearch = $('#key_search').val().trim()
  const statusId = $('#service_id').val()
  const dateRange = document.querySelector('input[name="datesearch"]').value

  let filters = {}
  // Xử lý keyword
  if (keySearch) {
    filters.keyword = keySearch
  }

  // Xử lý status
  if (statusId) {
    filters.status = statusId
  }

  fomatFilterDate(filters, dateRange)

  // Nếu không load more thì reset count và clear list
  if (!loadMore) {
    count = 0
    $('#list-card').html('')
    main_layout.show_loader()
  }

  $.ajax({
    url: '/admin/card/search',
    type: 'GET',
    data: {
      ...filters,
      page: page,
      per_page: perPage,
    },
    success: function (result) {
      if (!loadMore) {
        main_layout.hide_loader()
      }

      if (result && result.status === 200) {
        paginationData = result.pagination

        if (loadMore) {
          // Thêm dữ liệu mới vào danh sách hiện tại
          addCard(result.data)
        } else {
          // Hiển thị dữ liệu mới (sẽ reset count trong getListCard)
          getListCard(result.data)
        }

        getInfoPagination()

        // Cập nhật next_url cho load more
        next_url = paginationData.next_page_url

        // Hiển thị/ẩn button load more
        if (count < paginationData.total && paginationData.has_more_pages) {
          $('#btn_more').removeClass('d-none')
        } else {
          $('#btn_more').addClass('d-none')
        }
      } else {
        main_layout.alert_main('Có lỗi xảy ra khi tìm kiếm!', 'error')
      }
    },
    error: function (xhr, status, error) {
      if (!loadMore) {
        main_layout.hide_loader()
      }
      console.error('Search Error:', error)
      main_layout.alert_main('Có lỗi xảy ra khi tìm kiếm!', 'error')
    },
  })
}

function searchCardsByCustomer(page = 1, loadMore = false) {
  const customerKeyword = $('#key_search').val().trim()
  const statusId = $('#service_id').val()
  const dateRange = document.querySelector('input[name="datesearch"]').value

  let filters = {}

  // Xử lý keyword cho khách hàng
  if (customerKeyword) {
    filters.customer_keyword = customerKeyword
  }

  // Xử lý status
  if (statusId) {
    filters.status = statusId
  }

  // Xử lý date range
  fomatFilterDate(filters, dateRange)

  // Nếu không load more thì reset count và clear list
  if (!loadMore) {
    count = 0
    $('#list-card').html('')
    // main_layout.show_loader()
  }

  $.ajax({
    url: '/admin/card/search-by-customer',
    type: 'GET',
    data: {
      ...filters,
      page: page,
      per_page: perPage,
    },
    success: function (result) {
      if (!loadMore) {
        main_layout.hide_loader()
      }

      if (result && result.status === 200) {
        paginationData = result.pagination

        if (loadMore) {
          // Thêm dữ liệu mới vào danh sách hiện tại
          addCard(result.data)
        } else {
          // Hiển thị dữ liệu mới (sẽ reset count trong getListCard)
          getListCard(result.data)
        }

        getInfoPagination()

        next_url = paginationData.next_page_url

        // Hiển thị/ẩn button load more
        if (count < paginationData.total && paginationData.has_more_pages) {
          $('#btn_more').removeClass('d-none')
        } else {
          $('#btn_more').addClass('d-none')
        }
      } else {
        main_layout.alert_main('Có lỗi xảy ra khi tìm kiếm!', 'error')
      }
    },
    error: function (xhr, status, error) {
      handleSearchError(loadMore, error)
    },
  })
}

function searchCardsByExpiry(page = 1, loadMore = false) {
  const customerKeyword = $('#key_search').val().trim()
  const statusId = $('#service_id').val()
  const dateRange = document.querySelector('input[name="datesearch"]').value

  let filters = {}

  // Xử lý keyword cho khách hàng
  if (customerKeyword) {
    filters.customer_keyword = customerKeyword
  }

  // Xử lý status
  if (statusId) {
    filters.status = statusId
  }

  // Xử lý date range
  fomatFilterDate(filters, dateRange)

  // Nếu không load more thì reset count và clear list
  if (!loadMore) {
    count = 0
    $('#list-card').html('')
    // main_layout.show_loader()
  }

  $.ajax({
    url: '/admin/card/search-in-expiry-filter',
    type: 'GET',
    data: {
      ...filters,
      page: page,
      per_page: perPage,
    },
    success: function (result) {
      if (!loadMore) {
        main_layout.hide_loader()
      }

      if (result && result.status === 200) {
        paginationData = result.pagination

        if (loadMore) {
          // Thêm dữ liệu mới vào danh sách hiện tại
          addCard(result.data)
        } else {
          // Hiển thị dữ liệu mới (sẽ reset count trong getListCard)
          getListCard(result.data)
        }

        getInfoPagination()

        next_url = paginationData.next_page_url

        // Hiển thị/ẩn button load more
        if (count < paginationData.total && paginationData.has_more_pages) {
          $('#btn_more').removeClass('d-none')
        } else {
          $('#btn_more').addClass('d-none')
        }
      } else {
        main_layout.alert_main('Có lỗi xảy ra khi tìm kiếm!', 'error')
      }
    },
    error: function (xhr, status, error) {
      handleSearchError(loadMore, error)
    },
  })
}

function handleSearch() {
  if (searchTimeout) {
    clearTimeout(searchTimeout)
  }

  // Reset expiring filter khi search
  if (isExpiringFilter) {
    isExpiringFilter = false
    $('#btn-clear-filter').addClass('d-none')
    $('#btn-filter-expiring').removeClass('active')
  }

  searchTimeout = setTimeout(function () {
    currentPage = 1
    const searchType = $('#search_type').val() // Lấy loại tìm kiếm từ select option

    if (searchType === 'customer') {
      searchCardsByCustomer(1, false)
    } else {
      searchCardsByCardInfo(1, false)
    }
  }, 600)
}

function handleSearchTypeChange() {
  const searchType = $('#search_type').val()
  const keySearchInput = $('#key_search')

  if (searchType === 'customer') {
    keySearchInput.attr('placeholder', 'Tên khách hàng, số điện thoại')
  } else {
    keySearchInput.attr('placeholder', 'Mã thẻ, số thẻ')
  }

  // Tự động search khi thay đổi loại tìm kiếm nếu có từ khóa
  if (keySearchInput.val().trim()) {
    handleSearch()
  }
}

function handleLoadMore() {
  if (next_url) {
    const url = new URL(next_url)
    const nextPage = url.searchParams.get('page')
    const searchType = $('#search_type').val()

    if (searchType === 'customer') {
      searchCardsByCustomer(nextPage, true)
    } else {
      searchCardsByCardInfo(nextPage, true)
    }
  }
}

function getInfoPagination() {
  $('#pagination-info-card').empty()
  console.log('paginationData', paginationData)

  const total = paginationData ? paginationData.total : 0
  const actualCount = $('#list-card .card-item').length

  let newHtml = `<div class="d-flex justify-content-between align-items-center gap-1">
        <i class="bx bx-info-circle"></i><span>
        Hiển thị ${actualCount} trong tổng số ${total} thẻ
    </div>`
  $('#pagination-info-card').append(newHtml)
}

function getStatusDot(status, isExpiring = false) {
  const value = $('select[name="service_id"]').val()

  if (isExpiring && value == '3') {
    return 'border-left-color: #ff9800 !important;'
  }

  if (status == 0) {
    return 'border-left-color: #656464 !important'
  } else if (status == 1) {
    return 'border-left-color: #186f17 !important'
  } else {
    return 'border-left-color: #d41626 !important'
  }
}

function getListCard(card_data) {
  count = 0
  $('#list-card').html('')

  if (card_data.length === 0) {
    $('#list-card').html(`
            <div class="col-12">
                <div class="alert alert-info" role="alert">
                    Không có dữ liệu
                </div>
            </div>
        `)
    return
  }
  addCard(card_data)

  // Hiển thị button load more
  if (!isExpiringFilter && count < paginationData.total && paginationData.has_more_pages) {
    $('#btn_more').removeClass('d-none')
    next_url = paginationData.next_page_url
  } else {
    $('#btn_more').addClass('d-none')
  }
}

function addCard(data) {
  const template = document.getElementById('buttonActionTemplate')

  const rawTemplate = template.innerHTML

  data.forEach((element, index) => {
    const customerName =
      element.customer_count > 0 ? element.customers?.name : element.ticket_count ? 'Khách lẻ' : ''
    const customerCode =
      element.customer_count > 0 ? element.customers?.code : element.ticket_count ? '---' : ''
    const ticketCount = element.ticket_count ? element.ticket_count : 0

    // Xử lý template button action
    let buttonAction = template.innerHTML
    const lockText = element.status == 2 ? 'Mở' : 'Khóa'
    const display = element.status == 0 ? 'display:none' : 'display: block'
    const encodedCardData = encodeURIComponent(JSON.stringify(element))

    // Thay thế tất cả placeholder
    buttonAction = buttonAction.replace(/__CARD_ID__/g, element.id)
    buttonAction = buttonAction.replace(/__CARD_NUMBER__/g, element.card_number)
    buttonAction = buttonAction.replace(/__CARD_DATA__/g, encodedCardData)
    buttonAction = buttonAction.replace(/__CARD_STATUS__/g, element.status)
    buttonAction = buttonAction.replace(/__LOCK_TEXT__/g, lockText)
    buttonAction = buttonAction.replace(/__DISPLAY__/g, display)

    // Kiểm tra có phải vé sắp hết hạn không
    const isExpiring = element.is_expiring || false
    const expiringClass = isExpiring ? 'expiring-card' : ''

    const expiringInfo = element.expiring_info
      ? `
      <div class="alert alert-warning alert-sm mt-2 mb-0" style="padding: 0.25rem 0.5rem; font-size: 0.75rem;">
        <i class="ri-alarm-warning-line me-1"></i>
        <span class="line-clamp-2" title="${element.expiring_info}">${element.expiring_info}</span>
      </div>
    `
      : ''

    let card = `
            <div class="col-lg-2 col-sm-4 col-6 card-item">
                <div class="card p-2 custom-card ${expiringClass}" style="${getStatusDot(
      element.status,
      isExpiring
    )}; position: relative;">
                    
                    <div class="card-owner d-flex justify-content-between mb-2 w-100">
                        <div class="d-flex align-items-center" style="width: 85%">
                            <span class="ms-1 fw-semibold text-truncate">${element.card_number}
                        </div>
                        <div class="flex-shrink-0">
                            ${buttonAction}
                        </div>
                    </div>
                    <div class="user-name">
                        <i class="ri-time-line"></i><span>
                        <span class="px-1">${main.formatDateTime(element.updated_at)}
                    </div>
                    <div class="user-name text-truncate">
                        <i class="ri-user-line"></i><span>
                        <span class="px-1 text-truncate">${customerName}
                    </div>
                    <div class="user-name text-truncate">
                        <i class="  ri-shield-star-line"></i><span>
                        <span class="px-1 text-truncate">${customerCode}
                    </div>
                    <div class="user-name">
                        <i class="ri-ticket-line"></i><span>
                        <span class="px-1">${ticketCount}
                    </div>
                    <div class="expiring-info">
                    ${expiringInfo}
                    </div>
                </div>
            </div>`

    count++
    $('#list-card').append(card)

    const isChecked = $('#showNotice').is(':checked')

    if (isChecked) {
      $('.expiring-info').show()
    } else {
      $('.expiring-info').hide()
    }
  })

  //tự động khởi tạo dropdown
  setTimeout(() => {
    $('[data-bs-toggle="dropdown"]').dropdown()
  }, 100)
}

function displayData() {
  const startIndex = (currentPage - 1) * perPage
  const endIndex = startIndex + perPage
  const paginatedData = filteredData.slice(startIndex, endIndex)

  getListCard(paginatedData)
  getInfoPagination()
}

function handleLockCard(encodedData) {
  let data
  try {
    data = JSON.parse(decodeURIComponent(encodedData))
    console.log(data)
  } catch (e) {
    console.error('Error parsing card data:', e)
    return
  }

  var currentStatus = data.status
  const newStatus = currentStatus == 2 ? 1 : 2
  const action = currentStatus == 2 ? 'mở khóa' : 'khóa'
  const url = currentStatus == 2 ? '/admin/card/unlock' : '/admin/card/lock'

  Swal.fire({
    title: `Xác nhận ${action} thẻ?`,
    text: `Bạn có chắc chắn muốn ${action} thẻ này không?`,
    icon: 'warning',
    showCancelButton: true,
    confirmButtonColor: currentStatus != 2 ? '#d33' : '#28a745',
    cancelButtonColor: '#6c757d',
    confirmButtonText: `Có, ${action}!`,
    cancelButtonText: 'Hủy',
  }).then(result => {
    if (result.isConfirmed) {
      $.ajax({
        url: url,
        method: 'POST',
        data: {
          card_id: data.id,
          status: newStatus,
          _token: main.token,
        },
        success: function (response) {
          if (response.status === 200) {
            Swal.fire({
              title: 'Thành công!',
              text: response.message,
              icon: 'success',
              timer: 1500,
              showConfirmButton: false,
            }).then(() => {
              loadInitialData()
            })
          } else {
            Swal.fire({
              title: 'Lỗi!',
              text: response.message,
              icon: 'error',
            })
          }
        },
        error: function (xhr) {
          Swal.fire({
            title: 'Lỗi!',
            text: 'Có lỗi xảy ra trong quá trình xử lý',
            icon: 'error',
          })
        },
      })
    }
  })
}

function showFormCreate() {
  resetForm('#createCardFormModal')
  $('#createCardFormModal').modal('show')
  $('#createModalLabel').text('Thêm mới thẻ')
}

function showCardHistory(card_id) {
  main_layout.show_loader()
  $.ajax({
    url: 'card-history/show/' + card_id,
    type: 'GET',
    _token: main.token,

    success: function (result) {
      main_layout.hide_loader()
      $('#history-items-container').empty()
      $('#showCardHistoryModal').modal('show')
      $('#showCardHistoryLabel').text('Nhật kí thẻ')
      if (result.status == 200) {
        let historyHtml = ''

        result.data.forEach(item => {
          const createdAt = new Date(item.created_at)
          const formattedTime = createdAt.toLocaleString('vi-VN', {
            year: 'numeric',
            month: '2-digit',
            day: '2-digit',
            hour: '2-digit',
            minute: '2-digit',
          })
          historyHtml += `
            <div class="history-item border-bottom p-2 ">
              <div class="row align-items-center">
                <div class="col-auto">
                  <img src='${
                    item?.user?.url_user_avatar ?? '/images/default-user.jpg'
                  }' width="45" height="45" class="rounded-circle border" alt="Avatar" />
                </div>
                <div class="col">
                  <div class="d-flex justify-content-between align-items-center">
                    <div>
                      <h6 class="mb-1 fw-semibold">${item?.user?.name}</h6>
                      <p class="mb-0 text-info">${item?.content}</p>
                    </div>
                    <div class="text-end">
                      <p class="text-muted d-flex  align-items-center">
                        <i class="ri-time-line me-1"></i><span>
                        ${formattedTime}
                      </p>
                    </div>
                  </div>
                </div>
              </div>
            </div>`
        })

        $('#history-items-container').append(historyHtml)
      } else {
        var errorMessage = `
          <div class="text-center text-muted py-5">
            <i class="ri-information-line fs-1 text-secondary"></i><span>
            <p class="mt-3 mb-0">Chưa có lịch sử thao tác nào</p>
          </div>`

        $('#history-items-container').append(errorMessage)
      }
    },
  })
}

function resetForm(formSelector) {
  $(formSelector + ' input').val('')
  $(formSelector + ' .invalid-feedback')
    .text('')
    .removeClass('d-block')
  $(formSelector + ' .form-control').removeClass('is-invalid')
}

function handleUnlinkCard(cardId) {
  Swal.fire({
    title: 'Xác nhận hủy liên kết?',
    html: `Bạn có chắc chắn muốn hủy liên kết thẻ này với khách hàng không?`,
    icon: 'warning',
    showCancelButton: true,
    confirmButtonColor: '#d33',
    cancelButtonColor: '#6c757d',
    confirmButtonText: 'Có, hủy liên kết!',
    cancelButtonText: 'Hủy',
  }).then(result => {
    if (result.isConfirmed) {
      $.ajax({
        url: '/admin/card/unlink',
        method: 'POST',
        data: {
          card_id: cardId,
          _token: main.token,
        },
        success: function (response) {
          if (response.status === 200) {
            Swal.fire({
              title: 'Thành công!',
              text: response.message,
              icon: 'success',
              timer: 1500,
              showConfirmButton: false,
            }).then(() => {
              loadInitialData()
            })
          } else {
            Swal.fire({
              title: 'Lỗi!',
              text: response.message,
              icon: 'error',
            })
          }
        },
        error: function (xhr) {
          Swal.fire({
            title: 'Lỗi!',
            text: 'Có lỗi xảy ra trong quá trình xử lý',
            icon: 'error',
          })
        },
      })
    }
  })
}

function triggerFileInput() {
  document.getElementById('excelFileInput').click()
}

function handleFileImport(input) {
  const file = input.files[0]
  if (!file) return

  const object = $('#objectImport').val()

  if (object == 'card') {
    importCard(file)
  } else if (object == 'customer') {
    importCustomer(file)
  } else {
    Swal.fire({
      icon: 'warning',
      title: 'Thiếu thông tin!',
      text: 'Vui lòng chọn loại dữ liệu trước khi tải mẫu.',
    })
    return
  }
}

function importCustomer(file) {
  console.log('function importCustomer called')
  $('#importDataModal').modal('hide')
  main_layout.show_loader()

  const formData = new FormData()
  formData.append('excel_file', file)
  formData.append('_token', $('meta[name="csrf-token"]').attr('content'))

  $.ajax({
    url: '/admin/customer/importExcel',
    type: 'POST',
    data: formData,
    processData: false,
    contentType: false,
    success: function (response) {
      main_layout.hide_loader()
      if (response.status === 200) {
        main_layout.alert_main('Thêm mới dữ liệu thành công', 'success')
        location.reload()
      } else {
        if (response.errors && response.errors.length > 0) {
          showImportErrors(response.errors)
        } else {
          main_layout.alert_main('Có lỗi xảy ra khi thêm mới dữ liệu', 'error')
        }
      }
    },
    error: function (xhr, status, error) {
      main_layout.hide_loader()
      console.error('Import error:', error)

      let errorMessage = 'Có lỗi xảy ra khi import dữ liệu'

      if (xhr.responseJSON && xhr.responseJSON.message) {
        errorMessage = xhr.responseJSON.message
      }

      Swal.fire({
        icon: 'error',
        title: 'Lỗi!',
        text: errorMessage,
      })
    },
    complete: function () {
      // Reset input file
      $('#excelFileInput').val('')
    },
  })
}

function importCard(file, url) {
  $('#importDataModal').modal('hide')
  main_layout.show_loader()

  const formData = new FormData()
  formData.append('excel_file', file)
  formData.append('_token', $('meta[name="csrf-token"]').attr('content'))

  $.ajax({
    url: '/admin/card/importExcel',
    type: 'POST',
    data: formData,
    processData: false,
    contentType: false,
    success: function (response) {
      main_layout.hide_loader()

      if (response.status === 200) {
        Swal.fire({
          icon: 'success',
          title: 'Thành công!',
          text: response.message || 'Thêm mới dữ liệu thành công',
          timer: 2000,
          showConfirmButton: false,
        }).then(() => {
          // Reload dữ liệu
          loadInitialData()
        })
      } else {
        // Hiển thị lỗi nếu có
        if (response.errors && response.errors.length > 0) {
          showImportErrors(response.errors)
        } else {
          console.error('Import error:', response.message)
          Swal.fire({
            icon: 'error',
            title: 'Lỗi!',
            text: response.message || 'Có lỗi xảy ra khi thêm mới dữ liệu',
          })
        }
      }
    },
    error: function (xhr, status, error) {
      main_layout.hide_loader()
      console.error('Import error:', error)

      let errorMessage = 'Có lỗi xảy ra khi import dữ liệu'

      if (xhr.responseJSON && xhr.responseJSON.message) {
        errorMessage = xhr.responseJSON.message
      }

      Swal.fire({
        icon: 'error',
        title: 'Lỗi!',
        text: errorMessage,
      })
    },
    complete: function () {
      // Reset input file
      $('#excelFileInput').val('')
    },
  })
}

function showImportErrors(errors) {
  const grouped = new Map()
  let errorHtml = '<div class="table-responsive" style="max-height: 400px; overflow-y: auto;">'

  errorHtml += `<div class="alert alert-borderless alert-danger fs-16 d-flex align-items-center" role="alert">
          <i class="ri-error-warning-line px-1"></i><span>  <div class="text-start">
          <p class="m-0 ">Dữ liệu chưa được thêm vào hệ thống. Vui lòng kiểm tra và thực hiện thao tác lại.</p>
          </div>
        </div>`

  errorHtml += '<table class="table table-sm table-bordered">'
  errorHtml +=
    '<thead class="table-light"><tr><th class="text-center">Dòng</th><th>Lỗi</th></tr></thead><tbody>'

  errors.forEach(({ row, message }) => {
    if (!grouped.has(row)) {
      grouped.set(row, [])
    }
    grouped.get(row).push(message)
  })
  for (const [row, messages] of grouped.entries()) {
    errorHtml += `<tr><td class="text-center" style="vertical-align: middle;">${row}</td><td class="ps-2">${messages.join(
      '<br>'
    )}</td></tr>`
  }

  errorHtml += '</tbody></table></div>'
  main_layout.alert_main('Có lỗi trong quá trình thêm mới thẻ', 'error', 'center')

  setTimeout(() => {
    Swal.fire({
      html: errorHtml,
      width: '700px',
      showConfirmButton: true,
      confirmButtonText: 'Đóng',
    })
  }, 1500)
}

function showImportDataModal(object) {
  $('#objectImport').val(object)
  $('#importDataModal').modal('show')
  if (object == 'card') {
    $('#importDataModalLabel').text('Thêm mới thẻ')
  } else {
    $('#importDataModalLabel').text('Thêm mới khách hàng')
  }
}

function handleExport() {
  const object = $('#objectImport').val()
  let url = ''

  if (object === 'card') {
    url = '/admin/card/export-template'
    namDowload = 'Mau_nhap_the.xlsx'
  } else if (object === 'customer') {
    url = '/admin/customer/export-template'
    namDowload = 'Mau_nhap_khach_hang.xlsx'
  } else {
    // fallback nếu chưa chọn object
    Swal.fire({
      icon: 'warning',
      title: 'Thiếu thông tin!',
      text: 'Vui lòng chọn loại dữ liệu trước khi tải mẫu.',
    })
    return
  }

  ExportFileTemplate(url, namDowload)
}

function ExportFileTemplate(url, namDowload) {
  console.log('url', url)
  main_layout.show_loader()
  $.ajax({
    url: url,
    type: 'GET',
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
        showConfirmButton: false,
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

function handleShowInfo(card_id) {
  main_layout.show_loader()

  $.ajax({
    url: '/admin/card/show/' + card_id,
    type: 'GET',
    success: function (response) {
      main_layout.hide_loader()
      if (response.status === 200) {
        $('#showInfoDataLabel').text('Chi tiết thông tin thẻ')
        populateCardInfo(response.data.data)

        $('#infoModal').modal('show')
      } else {
        Swal.fire({
          title: 'Lỗi!',
          text: response.message || 'Không thể lấy thông tin thẻ',
          icon: 'error',
        })
      }
    },
    error: function (xhr) {
      main_layout.hide_loader()
      Swal.fire({
        title: 'Lỗi!',
        text: 'Có lỗi xảy ra trong quá trình xử lý',
        icon: 'error',
      })
    },
  })
}

function populateCardInfo(cardData) {
  let modalContent = ' <div class="row">'
  modalContent += `
   
      <div class="col-lg-3 col-md-4  col-12">
        <h5 class="">Thông tin thẻ</h5>
        <div class = "p-2">
          <p>Mã sau thẻ: ${cardData?.code ?? 'N/A'}</p>
          <p>Mã thẻ chip: ${cardData?.card_number ?? 'N/A'}</p>
          <p>Trạng thái: ${getStatusText(cardData?.status)}</p>
          <p>Cập nhật: ${main.formatDateTime(cardData?.updated_at)}</p>
        </div>
      </div>
      `

  if (cardData?.customers.length == 0 && cardData?.tickets.length == 0) {
    modalContent += `
       <div class="col-lg-9 col-md-8 col-12">
      </div>
    </div>
   
  `
  }
  if (cardData?.customers.length == 0 && cardData?.tickets.length > 0) {
    modalContent += `
       <div class="col-lg-9 col-md-8  col-12">
          <h5>Thông tin khách hàng</h5>
              <p><i class="ri-user-line"></i><span>Khách lẻ</p>
      </div>
    </div>
   
  `
  } else {
    cardData?.customers.forEach(customer => {
      const avatarUrl = customer.image ? customer.image : '/images/default-user.jpg'
      modalContent += `
      <div class="col-lg-9 col-md-8 col-12">
          <h5>Thông tin khách hàng</h5>
          <div class="d-flex align-items-center px-3 gap-4 customer-info">
          <div>
            <img src="${avatarUrl}" alt="Avatar" class="avatar-img" width="100" height="100" style="border-radius: 50%;">
          </div >
          <div class="w-100">
          
          <p class="mb-2 ps-2 fs-4"> ${
            customer.name || 'Khách lẻ'
          } <span class="badge rounded-pill bg-info fs-6 mx-2">${customer.code}</span></p>
              <div class="row w-100">
                 <div class="col-lg-4 col-12">
                  <p class="mb-2"><i class=" mdi mdi-gender-male-female"></i><span>${
                    customer.gender == 1 ? 'Nam' : 'Nữ' || ''
                  } </p>
                  <p class="mb-2"><i class="mdi mdi-map-marker-radius"></i><span> ${
                    customer.address || ''
                  }</p>
                </div>
                <div class="col-lg-4 col-12">
                  <p class="mb-2"><i class=" mdi mdi-account-circle"></i><span>${
                    customer.cccd || ''
                  } </p>
                  <p class="mb-2"><i class="ri-calendar-event-fill"></i><span> ${
                    main.formatDate(customer.birthday) || ''
                  }</p>
                </div>
                <div class="col-lg-4 col-12">
                  <p class="mb-2"><i class=" ri-phone-fill"></i><span>${customer.phone || ''} </p>
                  <p class="mb-2"><i class=" ri-mail-fill"></i><span>${customer.email || ''} </p>
                </div>
             
              </div>
            </div>
          </div>
        </div>
   
  `
    })
  }

  // Thông tin vé
  if (cardData?.tickets && cardData?.tickets.length > 0) {
    modalContent += `
    <div class="mt-3 pt-3" style="border-top: dashed 1px #e2e2e2">
      <h5>Thông tin vé dịch vụ</h5>
      <div class="table-responsive">
        <table class="table table-sm capti-top table-nowrap detail-info-card">
          <thead class="table-light">
            <tr>
              <th class="text-center">#</th>
              <th>Loại vé</th>
              <th>Giá</th>
              <th>Trạng thái</th>
              <th>Ngày bắt đầu</th>
              <th>Ngày kết thúc</th>
              <th class="text-center">Lượt sử dụng</th>
            </tr>
          </thead>
          <tbody>
  `

    if (cardData.tickets.length === 0) {
      modalContent += `
      <tr>
        <td colspan="7" class="text-center">Không có dữ liệu</td>
      </tr>
    `
    } else {
      cardData.tickets.forEach((ticket, index) => {
        const cardTicket = ticket.card_tickets?.[0] || {}
        const expiryFrom = cardTicket.expiry_date_from
          ? main.formatDate(cardTicket.expiry_date_from)
          : ''
        const expiryTo = cardTicket.expiry_date_to ? main.formatDate(cardTicket.expiry_date_to) : ''
        const remainingUses = `${cardTicket.count_turn ?? '0'}/${cardTicket.turn_of_use ?? '-'}`
        const isExpiring = ticket.is_expiring === true
        const rowClass = isExpiring ? 'table-warning' : ''
        const warningIcon = isExpiring
          ? `<i class=" ri-alarm-fill text-danger me-1" title="Vé sắp hết hạn"></i>`
          : ''

        modalContent += `
        <tr class="${rowClass}">
          <td class="text-center">${index + 1}</td>
          <td>${ticket.ticket_type_name || ''}</td>
          <td>${formatCurrency(ticket.price || 0)}</td>
          <td>${ticket.status == 1 ? 'Hoạt động' : 'Khóa'}</td>
          <td>${expiryFrom}</td>
          <td style="display: flex; align-items: center">${expiryTo}${warningIcon}</td>
          <td class="text-center">${remainingUses}</td>
        </tr>
      `
      })
    }

    modalContent += `
          </tbody>
        </table>
      </div>
    </div>
  `
  }

  // Đưa nội dung vào modal body
  $('#infoModal .modal-body').html(modalContent)
}

function getStatusText(status) {
  switch (status) {
    case 0:
      return '<span class="badge bg-warning">Chưa sử dụng'
    case 1:
      return '<span class="badge bg-success">Đã sử dụng'
    case 2:
      return '<span class="badge bg-danger">Đã khóa'
    default:
      return '<span class="badge bg-secondary">Không xác định'
  }
}

function formatCurrency(amount) {
  return new Intl.NumberFormat('vi-VN', {
    style: 'currency',
    currency: 'VND',
  }).format(amount)
}

function copyCardNumber(card_number) {
  if (navigator.clipboard && navigator.clipboard.writeText) {
    navigator.clipboard
      .writeText(card_number)
      .then(() => main_layout.alert_main('Sao chép mã thẻ thành công', 'success'))
      .catch(err => {
        console.error('Lỗi khi copy:', err)
        fallbackCopy(card_number)
      })
  } else {
    fallbackCopy(card_number)
  }
}

function fallbackCopy(text) {
  const textarea = document.createElement('textarea')
  textarea.value = text
  textarea.style.position = 'fixed' // tránh scroll nhảy
  textarea.style.opacity = '0'
  document.body.appendChild(textarea)
  textarea.focus()
  textarea.select()

  try {
    const successful = document.execCommand('copy')
    main_layout.alert_main('Sao chép mã thẻ thành công', 'success')
    $('.dropdown-menu').removeClass('show').hide().css('display', 'none')
  } catch (err) {
    console.error('Fallback copy thất bại:', err)
  }

  document.body.removeChild(textarea)
}

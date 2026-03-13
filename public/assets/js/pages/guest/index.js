function handleResizePage(perSize) {
  let key_search = $('#key_search').val()
  let faceid_status = $('select[name="faceid_status"]').val()
  let search_confim = 1

  const activeMeeting = document.querySelector('.meeting-card.active')
  const meetingId = activeMeeting.dataset.id
  let baseUrl = $('#pageLimit').data('url')

  let url =
    baseUrl +
    '?key_search=' +
    encodeURIComponent(key_search) +
    '&faceid_status=' +
    faceid_status +
    '&search_confim=' +
    search_confim +
    '&meeting_id=' +
    meetingId +
    '&limit=' +
    perSize

  window.location.href = url
}

// danh sách meeting
document.addEventListener('DOMContentLoaded', function () {
  const $list = $('#meeting-list')
  const $input = $('#key_search')
  window.$form = $('#mainSearchForm')
  window.$hiddenInput = $('#selectedMeetingId')
  window.$emptyState = $('#empty-state')
  window.$meetingContent = $('#meeting-content')

  function getMeetingInGuest(callback = null) {
    let start = null
    let end = null

    if (localStorage.getItem('meetingStartDate') && localStorage.getItem('meetingEndDate')) {
      start = localStorage.getItem('meetingStartDate')
      end = localStorage.getItem('meetingEndDate')
    }

    $.ajax({
      url: guest_index.url_meeting,
      type: 'GET',
      headers: headersClient,
      data: {
        start_time: start,
        end_time: end,
      },
      success: function (result) {
        guest_index.meetings = result.data
        renderMeetings(result.data)
        renderDateRange(main.formatDate(result.start_time), main.formatDate(result.end_time))
        setActiveMeetingCard()

        if (typeof callback === 'function') {
          callback(true)
        }
      },
      error: function (xhr) {
        getMeetinginGuest()
      },
    })
  }

  function renderMeetings(filteredMeetings) {
    if (!filteredMeetings.length) {
      $list.html('<p class="text-muted">Không tìm thấy cuộc họp nào.</p>')
      return
    }

    let html = ''
    filteredMeetings.forEach(m => {
      html += `
      <div class="meeting-card card p-2 mb-2 border-0 shadow-sm" 
        data-id="${m.id}" 
        style="cursor:pointer; border-left: 4px solid transparent;">
        <div class="d-flex justify-content-between align-items-center">
          <strong class="text-truncate" style="max-width: 180px;">${m.name}</strong>
        </div>
      </div>
    `
    })
    $list.html(html)
  }

  getMeetingInGuest()

  // tìm kiếm
  let typingTimer
  const delay = 300

  $input.on('keyup', function () {
    clearTimeout(typingTimer)
    typingTimer = setTimeout(() => {
      const keyword = $(this).val().toLowerCase().trim()

      if (!keyword) {
        renderMeetings(guest_index.meetings)
        return
      }

      // Lọc theo tên
      const filtered = guest_index.meetings.filter(m => m.name.toLowerCase().includes(keyword))
      renderMeetings(filtered)
    }, delay)
  })

  const $sidebar = $('#session-sidebar')
  const $toggleBtn = $('#toggleSidebarBtn')
  let isHidden = false

  $toggleBtn.on('click', function () {
    if (!isHidden) {
      // Ẩn sidebarde
      $sidebar.css('display', 'none')

      $sidebar.css('margin-left', '-260px')
      $toggleBtn.css('left', '10px').html('<i class="ri-arrow-right-s-line"></i>')
    } else {
      // Hiện sidebar
      $sidebar.css('display', 'block')
      $sidebar.css('width', '260px')

      $sidebar.css('margin-left', '0')
      $toggleBtn.css('left', '270px').html('<i class="ri-arrow-left-s-line"></i>')
    }
    isHidden = !isHidden
  })

  // lọc cuộc họp theo ngày
  const picker = flatpickr('#hidden-date-input', {
    mode: 'range',
    dateFormat: 'd-m-Y',
    enableTime: false,
    dateInline: false,
    time_24hr: false,
    locale: 'vn',
    defaultDate: null,
    appendTo: document.getElementById('datepicker-container'),
    clickOpens: false,

    onChange: function (selectedDates, dateStr, instance) {
      if (selectedDates.length === 2) {
        const start = instance.formatDate(selectedDates[0], 'd/m/Y')
        const end = instance.formatDate(selectedDates[1], 'd/m/Y')

        localStorage.setItem('meetingStartDate', start)
        localStorage.setItem('meetingEndDate', end)
        renderDateRange(start, end)
        getMeetingInGuest()
      }
    },
  })
  // select date
  document.getElementById('select-date').addEventListener('click', function () {
    picker.open()
  })

  // hiện/ẩn datepicker
  let isOpen = false
  document.getElementById('select-date').addEventListener('click', function () {
    isOpen = !isOpen
    document.getElementById('datepicker-container').style.display = isOpen ? 'block' : 'none'
  })

  function renderDateRange(start, end) {
    if (start != end) {
      document.querySelector('.date-text').textContent = `${start} đến ${end}`
    } else {
      document.querySelector('.date-text').textContent = `${start}`
    }
  }

  function setActiveMeetingCard() {
    if (guest_index.selectedMeeting) {
      const $card = $(`.meeting-card[data-id="${guest_index.selectedMeeting}"]`)
      $card.addClass('active').css({
        backgroundColor: '#007bff',
        color: 'white',
      })
      $hiddenInput.val(guest_index.selectedMeeting)

      // Có meeting được chọn → hiển thị bảng
      $emptyState.addClass('d-none')
      $meetingContent.removeClass('d-none')
    } else {
      // Không có meeting nào → hiển thị empty
      $emptyState.removeClass('d-none')
      $meetingContent.addClass('d-none')
    }
  }

  window.showEditForm = function (id) {
    let url = 'guest/show/' + id

    $.ajax({
      url: url,
      type: 'GET',
      headers: headersClient,
      data: {
        _token: main.token,
      },
      success: function (result) {
        main_layout.hide_loader()

        if (result['status'] === 200) {
          const guest = result.data
          $('#idFormStaff').attr('data-id', guest.id)
          $('#name').val(guest.name)
          $('#phone').val(guest.phone)
          $('#agencies_value').val(guest.agencies_value)
          $('#birthday').val(guest.birthday ? guest.birthday.split(' ')[0] : '')
          $('#cccd').val(guest.cccd)
          $('#position').val(guest.position)

          $('#gender').val(guest.gender ?? 3)
          $('#meeting_id').val(guest?.meeting[0].id ?? '')

          $('#editGuestForm').modal('show')
        } else {
          Swal.fire({
            text: result['error'],
            icon: 'error',
            timer: 1500,
          })
        }
      },
      error: function (xhr) {
        main_layout.hide_loader()
        Swal.fire({
          text: 'Không thể tải dữ liệu khách mời!',
          icon: 'error',
          timer: 1500,
        })
      },
    })
  }

  window.showDeleteModal = function (id) {
    $('#deleteGuesModal').modal('show')
    $('#deleteGuesModal #guest_id').val(id)
  }

  window.handleDeleteGuest = function () {
    let guest_id = $('#deleteGuesModal #guest_id').val()
    let url = 'guest/delete/' + guest_id
    main_layout.show_loader()
    $.ajax({
      url: url,
      type: 'DELETE',
      headers: headersClient,
      data: {
        _token: main.token,
      },
      success: function (result) {
        $('#deleteGuesModal').modal('hide')

        if (result['status'] == 200) {
          $('tr.guest-row[data-id="' + guest_id + '"]').remove()
          updateRowIndex('#guestTable')
          main_layout.hide_loader()
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

  window.handleEditGuest = function () {
    if (window.isSubmitting) return
    window.isSubmitting = true

    main_layout.show_loader()

    const form = $('#idFormStaff')
    const id = form.attr('data-id')

    let data = {
      _token: main.token,
      name: $('#name').val(),
      phone: $('#phone').val(),
      agencies_value: $('#agencies_value').val(),
      cccd: $('#cccd').val(),
      position: $('#position').val(),
      gender: $('#gender').val(),

      birthday: $('#birthday').val(),
      meeting_id: $('#meeting_id').val(),
    }

    $.ajax({
      url: 'guest/edit/' + id,
      type: 'PUT',
      headers: headersClient,
      data: data,
      success: function (response) {
        if (response.status === 200) {
          Swal.fire({
            text: 'Cập nhật thành công!',
            icon: 'success',
            timer: 1500,
          })

          $('#editGuestForm').modal('hide')

          $('tr.guest-row[data-id="' + id + '"]')
            .find('td:nth-child(2)')
            .text(data.name ?? '-')
          $('tr.guest-row[data-id="' + id + '"]')
            .find('td:nth-child(3)')
            .text(data.gender == 1 ? 'Nam' : data.gender == 2 ? 'Nữ' : '-')
          $('tr.guest-row[data-id="' + id + '"]')
            .find('td:nth-child(4)')
            .text(data.birthday ? main.formatDate(data.birthday) : '-')

          $('tr.guest-row[data-id="' + id + '"]')
            .find('td:nth-child(5)')
            .text(data.cccd ?? '-')
          $('tr.guest-row[data-id="' + id + '"]')
            .find('td:nth-child(6)')
            .text(data.phone ?? '-')

          $('tr.guest-row[data-id="' + id + '"]')
            .find('td:nth-child(7)')
            .text(data.agencies_value ?? '-')

          $('tr.guest-row[data-id="' + id + '"]')
            .find('td:nth-child(8)')
            .text(data.position ?? '-')
          updateRowIndex('#guestTable')

          main_layout.hide_loader()
        } else {
          Swal.fire({
            text: response.error || 'Cập nhật thất bại!',
            icon: 'error',
            timer: 1500,
          })
        }
      },
      error: function (xhr) {
        Swal.fire({
          text: 'Có lỗi xảy ra khi cập nhật!',
          icon: 'error',
          timer: 1500,
        })
      },
      complete: () => (window.isSubmitting = false),
    })
  }


})

let scrollPos = 0
document.addEventListener('DOMContentLoaded', () => {
  let isRendered = false
  let checkin = null
  let end = null
  let timer = null
  let now = new Date()

  monitorCheckUpdateAttendance.meeting_detail_id = $('#meeting_detail_id').val()

  window.getListMember = function (callback) {
    let url = '/monitor/staff/not-checkin/' + monitorCheckUpdateAttendance.meeting_detail_id
    let data = {
      time: new Date().toISOString(),
    }

    $.ajax({
      url: url,
      type: 'GET',
      data: data,
      success: function (result) {
        if (result.status === 200) {
          const data = result.data

          const count_total = data.total
          const count_check_in = data.count_check_in
          const count_not_check_in = data.count_not_check_in

          const staffs = data.staffs_not_checkin_info
          const staffIds = data.staffs_not_checkin_info_ids
          const session = data.session

          checkin_time = session.checkin
          end_time = session.end_time

          $('.meeting-title').text(session.meeting_name)
          $('.date').text(session.date)
          $('.room-name').text(session.room_name)

          const s_time = formatTime(session.start_time)
          const e_time = formatTime(session.end_time)

          $('.start-time').text(s_time)
          $('.end-time').text(e_time)

          start = new Date(session.start_time)
          end = new Date(session.end_time)
          checkin = new Date(session.check_in)

          if (now >= checkin && now <= end) {
            startTimer()
          } else {
            stopTimer()
          }
          staffNotCheckinTemp = staffs

          if (!isRendered) {
            renderStaffNotCheckin(staffs)
            updateMemberTable(staffs, staffIds)
            updateCountNumber(count_total, count_check_in, count_not_check_in)
            initScroll()
            isRendered = true
          } else {
            updateMemberTable(staffs, staffIds)
            updateCountNumber(count_total, count_check_in, count_not_check_in)
            initScroll()
          }
        }
      },
      error: function () {
        console.warn('Lỗi khi lấy dữ liệu — thử lại trong 1 giây...')
        setTimeout(() => {
          getListMember()
        }, 1000)
      },
    })
  }

  getListMember()

  // setInterval(() => {
  //   getListMember()
  // }, 1000)

  function formatTime(datetimeString) {
    if (!datetimeString) return ''
    const dt = new Date(datetimeString.replace(' ', 'T'))

    if (isNaN(dt.getTime())) return ''

    const hours = dt.getHours().toString().padStart(2, '0')
    const minutes = dt.getMinutes().toString().padStart(2, '0')

    return `${hours}:${minutes}`
  }

  function initScroll() {
    const tbody = document.querySelector('.table-member tbody')
    const rows = Array.from(tbody.children)

    if (rows.length <= 5) return

    const rowHeight = rows[0].offsetHeight

    tbody.innerHTML += tbody.innerHTML
    smoothScroll()
    function smoothScroll() {
      scrollPos += 0.3 // tốc độ mượt, tăng lên = nhanh hơn
      tbody.style.transform = `translateY(-${scrollPos}px)`

      // Nếu scroll đến hết phần đầu tiên => reset
      if (scrollPos >= rowHeight * rows.length) {
        scrollPos = 0
      }

      requestAnimationFrame(smoothScroll)
    }

    requestAnimationFrame(smoothScroll)
  }

  // requestAnimationFrame(smoothScroll)

  function renderStaffNotCheckin(list) {
    const tbody = document.getElementById('memberTbody')
    tbody.innerHTML = ''

    list.forEach((staff, index) => {
      const tr = document.createElement('tr')
      tr.id = `row-${staff.id}`

      tr.innerHTML = `
      <td class="index">${index + 1}.</td>
      <td class="name"> ${staff.name}</td>
      <td class="role">${staff.position ?? ''}</td>
    `

      tbody.appendChild(tr)
    })
  }

  function updateMemberTable(staffs, staffIds) {
    const tbody = document.getElementById('memberTbody')

    // Xóa các row không còn trong danh sách
    const currentRows = tbody.querySelectorAll('tr')

    currentRows.forEach(row => {
      const rowId = row.getAttribute('id')
      const realId = rowId.replace('row-', '')

      if (!staffIds.includes(realId)) {
        row.remove()
      }
    })
  }
  function updateCountNumber(count_total, count_check_in, count_not_check_in) {
    $('.stats-box .stats-number.total').text(count_total)
    $('.stats-box .stats-number.checked').text(count_check_in)
    $('.stats-box .stats-number.not-checked').text(count_not_check_in)
  }

  function startTimer() {
    if (!timer) {
      runEverySecond()
      // console.log('runEverySecond start')
    }
  }

  function stopTimer() {
    if (timer) {
      clearInterval(timer)
      timer = null

      // console.log('runEverySecond stopped')
    }
  }
})

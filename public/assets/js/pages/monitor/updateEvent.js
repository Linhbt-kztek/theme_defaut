let cachedData = { staffs: [], guests: [] }
document.addEventListener('DOMContentLoaded', () => {
  let updated_event_url = null
  window.addEventListener('sessionChanged', e => {
    window.currentSessionId = e.detail.session_id
    updated_event_url = `monitor/updated-event/${window.currentSessionId}`

    fetchData()
  })

  const staffTable = document.getElementById('monitor-member')
  const guestTable = document.getElementById('monitor-guest')

  function formatTime(dateTimeStr) {
    if (!dateTimeStr) return '--:--'
    const date = new Date(dateTimeStr)
    if (isNaN(date)) return '--:--'
    return date.toLocaleTimeString('vi-VN', {
      hour: '2-digit',
      minute: '2-digit',
      hour12: false,
    })
  }

  function getStatusText(status) {
    switch (status) {
      case 1:
        return 'Chưa checkin'
      case 2:
        return 'Đã vào'
      default:
        return 'Đã ra'
    }
  }

  // cập nhật theo id
  function updateRow(table, item) {
    const row = table.querySelector(`tr[data-id="${item.id}"]`)
    if (!row) return

    const cells = row.querySelectorAll('td')
    const statusCell = cells[cells.length - 2]
    const timeCell = cells[cells.length - 1]
    statusCell.textContent = getStatusText(item.status)
    timeCell.textContent = formatTime(item.time_action)
  }

  // so sánh và cập nhật dữ liệu mới
  function updateData(newData) {
    ;['staffs', 'guests'].forEach(type => {
      const oldList = cachedData[type]
      const newList = newData[type]
      const table = type === 'staffs' ? staffTable : guestTable

      newList.forEach(newItem => {
        const oldItem = oldList.find(x => x.id === newItem.id)
        if (
          !oldItem ||
          oldItem.status !== newItem.status ||
          oldItem.time_action !== newItem.time_action
        ) {
          updateRow(table, newItem)
        }
      })

      // Cập nhật thống kê tổng sau mỗi lần render
      updateSummary(type, newData)
    })

    cachedData = newData
  }

  // Hàm cập nhật tổng kết mỗi bảng
  function updateSummary(type, data) {
    let total = 0
    let notCheckin = 0
    let checkedIn = 0
    let checkedOut = 0

    if (type === 'staffs') {
      const list = data.staffs || []
      total = list.length
      list.forEach(p => {
        if (p.status === 1) notCheckin++
        else if (p.status === 2) checkedIn++
        else if (p.status === 3) checkedOut++
      })
      const summary = document.getElementById('staff-summary')
      if (summary)
        summary.textContent = `Chưa check-in: ${notCheckin} / ${total} — Đã vào: ${checkedIn} / ${total} — Ra ngoài: ${checkedOut} / ${total}`
    }

    if (type === 'guests') {
      // Dữ liệu guests có thể lồng thêm cấp guests[]
      const list = data.guests?.flatMap(g => g.guests || [g]) || []
      total = list.length
      list.forEach(p => {
        if (p.status === 1) notCheckin++
        else if (p.status === 2) checkedIn++
        else if (p.status === 3) checkedOut++
      })
      const summary = document.getElementById('guest-summary')
      if (summary)
        summary.textContent = `Chưa check-in: ${notCheckin} / ${total} — Đã vào: ${checkedIn} / ${total} — Ra ngoài: ${checkedOut} / ${total}`
    }
  }

  window.fetchData = function () {
    $.ajax({
      url: updated_event_url,
      type: 'GET',
      header: headersClient,
      processData: false,
      contentType: false,
      success: function (result) {
        if (result.status == 200) {
          updateData(result.data)
        }
      },
    })
  }

  fetchData()
  function getPersonData(tableId, row, data) {
    const person_id = row.dataset.id

    if (tableId == 'monitor-member') {
      return data.staffs?.find(p => p.id === person_id) || null
    }

    if (tableId == 'monitor-guest') {
      return data.guests?.find(g => g.id === person_id) || null
    }

    return null
  }

  function showPopup(row, tableId, popupId) {
    const popup = document.getElementById(popupId)
    const popupContent = popup.querySelector('.popup-content')
    const person = getPersonData(tableId, row, cachedData)
    // debugger

    if (person == null) {
      popupContent.innerHTML = `<div colspan="2" class="text-muted text-center">Không có sự kiện</div>`
    } else {
      const events = person.events || []
      let eventRows = events
        .map(
          ev => `
    <tr>
      <td>${ev.lane == 1 ? 'Đã vào' : 'Đã ra'}</td>
     <td>${new Date(ev.time_action).toLocaleTimeString('vi-VN', {
       hour: '2-digit',
       minute: '2-digit',
       second: '2-digit',
       hour12: false,
     })}</td>
    </tr> `
        )
        .join('')
      popupContent.innerHTML = `
    <div><strong>${person.name}</strong> - <span class="text-muted small px-2" s>${
        person.position || ''
      }</span></div>
   
    <table class="table table-sm table-borderless mb-0 mt-2">
      <thead class="text-muted small">
        <tr><th>Trạng thái</th><th>Thời gian</th></tr>
      </thead>
      <tbody class="text-white small">${eventRows}</tbody>
    </table>`
    }

    const rowRect = row.getBoundingClientRect()
    const containerRect = document.getElementById('main-content-session').getBoundingClientRect()

    // Tính vị trí popup nằm phía trên dòng, trong phạm vi main-content-session
    const popupTop = rowRect.top - containerRect.top - popup.offsetHeight + 20;
    const popupLeft = rowRect.left - containerRect.left + rowRect.width / 2 - popup.offsetWidth / 2

    // Đảm bảo popup không bị tràn ra ngoài bên phải hoặc trái
    const maxLeft = containerRect.width - popup.offsetWidth - 100;
    const safeLeft = Math.max(10, Math.min(popupLeft, maxLeft))

    popup.style.top = `${popupTop}px`
    popup.style.left = `${safeLeft}px`

    popup.classList.add('show')
    popup.style.display = 'block'
  }

  function hidePopup(popupId) {
    const popup = document.getElementById(popupId)
    popup.classList.remove('show')
    setTimeout(() => (popup.style.display = 'none'), 150)
  }

  function attachHoverPopup(tableId, popupId, getDataFn) {
    const table = document.getElementById(tableId)
    const tbody = table.querySelector('tbody')
    const popup = document.getElementById(popupId)

    let hideTimeout

    tbody.addEventListener(
      'mouseenter',
      e => {
        const row = e.target.closest('tr')
        if (!row) return
        clearTimeout(hideTimeout)
        showPopup(row, tableId, popupId)
      },
      true
    )

    tbody.addEventListener(
      'mouseleave',
      e => {
        clearTimeout(hideTimeout)
        hideTimeout = setTimeout(() => {
          if (!popup.matches(':hover')) hidePopup(popupId)
        }, 200)
      },
      true
    )

    table.addEventListener(
      'mouseleave',
      e => {
        clearTimeout(hideTimeout)
        hideTimeout = setTimeout(() => {
          if (!popup.matches(':hover')) hidePopup(popupId)
        }, 200)
      },
      true
    )

    // Nếu rời popup thì ẩn hẳn
    popup.addEventListener('mouseleave', () => hidePopup(popupId))
  }

  // Gắn cho 2 bảng
  attachHoverPopup('monitor-member', 'hover-popup-staff', row => ({
    name: row.dataset.name,
    position: row.dataset.position,
    department: row.dataset.department,
    events: JSON.parse(row.dataset.events || '[]'),
  }))

  attachHoverPopup('monitor-guest', 'hover-popup-guest', row => ({
    name: row.dataset.name,
    position: row.dataset.position,
    department: row.dataset.department,
    events: JSON.parse(row.dataset.events || '[]'),
  }))
})

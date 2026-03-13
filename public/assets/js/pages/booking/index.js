document.addEventListener('DOMContentLoaded', () => {
  window.setActiveTab = function (tab) {
    document.querySelectorAll('#rentalType li').forEach(li => li.classList.remove('active'))
    tab.classList.add('active')

    document.querySelectorAll('.tab-pane').forEach(el => el.classList.remove('active'))

    const tabId = tab.dataset.tab
    const target = document.getElementById(tabId)
    if (!target) {
      console.error('NOT FOUND TAB:', tabId)
      return
    }

    target.classList.add('active')
    const $form = $(target).find('form')
    $form.find('.customer-input').first().focus()
  }

  let bookedMap = {} // court_id => [minutes]

  window.loadBookedSlots = function (date) {
    bookingMap = {}
    bookedMap = {}
    window.bookingMap = {}
    window.sharedSessions = {}
    window.sharedMap = {}

    $.ajax({
      url: booking_index.url_get_booked_slots,
      type: 'GET',
      data: { date: date },
      dataType: 'json',
      success: function (res) {
        window.bookingDataMap = {}

        res.forEach(item => {
          const bid = item.booking_id ?? item.id

          window.bookingDataMap[bid] = item

          const start = timeToMinutes(item.start_time)
          const end = timeToMinutes(item.end_time)

          if (item.type == 1 || item.type == 2) {
            const bid = item.booking_id || item.id
            window.bookingMap = window.bookingMap || {}
            window.bookingMap[item.court_id] = window.bookingMap[item.court_id] || {}
            if (!bookedMap[item.court_id]) bookedMap[item.court_id] = []
            for (let m = start; m < end; m += 30) {
              bookedMap[item.court_id].push(m)
              window.bookingMap[item.court_id][m] = bid
            }
          }

          if (item.type == 3) {
            const bid = item.booking_id || item.id
            window.sharedSessions[bid] = window.sharedSessions[bid] || {
              booking_id: bid,
              booking_code: item.booking.code || '',
              created_at: item.booking.created_at || '',
              create_by_name:
                item.booking?.create_by?.name ||
                item.booking?.create_by?.user_name ||
                'Không xác định',

              court_id: item.court_id,
              court_name: item?.court?.name,

              start: start,
              end: end,
              slots: Number(item.slots) || 0,
              count: Number(item.count) || 0,
              price: Number(item.price) || 0,
              start_time: item.start_time.slice(0, 5),
              end_time: item.end_time.slice(0, 5),
            }

            // mark minutes in bookedMap to prevent normal selection on these slots
            if (!bookedMap[item.court_id]) bookedMap[item.court_id] = []
            for (let m = start; m < end; m += 30) {
              bookedMap[item.court_id].push(m)
              window.sharedMap[item.court_id] = window.sharedMap[item.court_id] || {}
              window.sharedMap[item.court_id][m] = bid
            }
          }
        })

        markBookedSlots()
        renderListBookedSlots(res)
      },
      error: function (xhr) {
        console.error('Không lấy được slot đã đặt', xhr)
      },
    })
  }

  function markBookedSlots() {
    document.querySelectorAll('.slot').forEach(slot => {
      const courtId = slot.dataset.court_id
      const timeMin = timeToMinutes(slot.dataset.time)

      // reset style
      slot.classList.remove('booked', 'shared', 'shared-green', 'shared-yellow', 'partial')
      slot.style.borderLeft = ''
      slot.style.borderRight = ''

      const type = slot.dataset.type

      if (!(bookedMap[courtId] && bookedMap[courtId].includes(timeMin))) {
        // sân chỉ cho phép sân ghép
        if (type == 2) {
          slot.classList.add('share-only')

          slot.onclick = function (e) {
            e.stopPropagation()
          }

          slot.onmouseenter = function () {
            showTooltip(slot, 'Sân chỉ cho phép tham gia giao lưu')
          }

          slot.onmouseleave = function () {
            hideTooltip()
          }
        } else {
          slot.onclick = () => toggleSelect(slot)
        }

        return
      }

      let currentId = null
      let isShared = false

      if (
        window.sharedMap &&
        window.sharedMap[courtId] &&
        typeof window.sharedMap[courtId][timeMin] !== 'undefined'
      ) {
        currentId = window.sharedMap[courtId][timeMin]
        isShared = true
      } else if (
        window.bookingMap &&
        window.bookingMap[courtId] &&
        typeof window.bookingMap[courtId][timeMin] !== 'undefined'
      ) {
        currentId = window.bookingMap[courtId][timeMin]
        isShared = false
      }

      // đóng khung từng phiên tập
      let prevId = null
      let nextId = null
      const prev = timeMin - 30
      const next = timeMin + 30

      if (currentId) {
        if (isShared) {
          prevId = (window.sharedMap[courtId] && window.sharedMap[courtId][prev]) || null
          nextId = (window.sharedMap[courtId] && window.sharedMap[courtId][next]) || null
        } else {
          prevId = (window.bookingMap[courtId] && window.bookingMap[courtId][prev]) || null
          nextId = (window.bookingMap[courtId] && window.bookingMap[courtId][next]) || null
        }
      }

      if (prevId === currentId) {
        slot.style.borderLeft = 'none'
      } else {
        slot.style.borderLeft = '1px solid #e6e6e6'
      }

      if (nextId === currentId) {
        slot.style.borderRight = 'none'
      } else {
        slot.style.borderRight = '1px solid #e6e6e6'
      }

      //  css màu sắc cho sân ghép
      if (isShared && currentId && window.sharedSessions && window.sharedSessions[currentId]) {
        const session = window.sharedSessions[currentId]

        if (session.count < session.slots) {
          slot.classList.add('shared', 'shared-green')
        } else if (session.count == session.slots) {
          slot.classList.add('shared', 'shared-yellow')
        } else {
          slot.classList.add('shared', 'partial')
        }

        slot.dataset.booking_id = currentId
        attachSlotHover(slot, currentId, true)

        // Cho phép đăng kí sân ghép với sân còn slot
        if (
          slot.classList.contains('shared-green') ||
          slot.classList.contains('shared-yellow') ||
          slot.classList.contains('booked')
        ) {
          slot.onclick = function (e) {
            e.stopPropagation()
            if (typeof showBookingDetail === 'function') {
              showBookingDetail(currentId, 3)
            }
          }
        } else {
          slot.onclick = () => toggleSelect(slot)
        }

        return
      }

      slot.classList.add('booked')
      attachSlotHover(slot, currentId, false)

      slot.onclick = function (e) {
        e.stopPropagation()

        if (!currentId) return

        const booking = window.bookingDataMap[currentId]
        if (!booking) return

        if (typeof showBookingDetail === 'function') {
          showBookingDetail(currentId, booking.type)
        }
      }
    })
  }
  const dateEl = document.getElementById('date')

  function showTooltip(el, text) {
    let tooltip = document.getElementById('courtTooltip')

    if (!tooltip) {
      tooltip = document.createElement('div')
      tooltip.id = 'courtTooltip'
      tooltip.className = 'court-tooltip'
      document.body.appendChild(tooltip)
    }

    tooltip.innerText = text

    const rect = el.getBoundingClientRect()

    tooltip.style.top = rect.top - 30 + 'px'
    tooltip.style.left = rect.left + 'px'

    tooltip.style.display = 'block'
  }

  function hideTooltip() {
    const tooltip = document.getElementById('courtTooltip')
    if (tooltip) tooltip.style.display = 'none'
  }

  function formatDateInput(date) {
    const y = date.getFullYear()
    const m = String(date.getMonth() + 1).padStart(2, '0')
    const d = String(date.getDate()).padStart(2, '0')
    return `${y}-${m}-${d}`
  }

  window.mergeSelectedSlots = function (selected) {
    const grouped = {}

    selected.forEach(item => {
      const [court_id, court_name, time] = item.split('|')

      if (!grouped[court_name]) {
        grouped[court_name] = []
      }

      grouped[court_name].push(timeToMinutes(time))
    })

    const result = []

    for (const court in grouped) {
      const times = grouped[court].sort((a, b) => a - b)

      let start = times[0]
      let prev = times[0]
      const ranges = []

      for (let i = 1; i < times.length; i++) {
        if (times[i] === prev + 30) {
          prev = times[i]
        } else {
          ranges.push(`${minutesToTime(start)} - ${minutesToTime(prev + 30)}`)
          start = times[i]
          prev = times[i]
        }
      }

      ranges.push(`${minutesToTime(start)} - ${minutesToTime(prev + 30)}`)
      result.push(`${court}: ${ranges.join(' & ')}`)
    }

    return result
  }

  function minutesToTime(min) {
    const h = String(Math.floor(min / 60)).padStart(2, '0')
    const m = String(min % 60).padStart(2, '0')
    return `${h}:${m}`
  }

  window.updateSelectedInfo = function () {
    if (!btnCreate || !selectedText) return
    if (!selected.length) {
      selectedText.innerText = 'Chưa chọn khung giờ'
      btnCreate.classList.add('hidden')
      return
    }

    const dateValue = document.getElementById('date').value
    const dateText = formatDateWithWeekday(dateValue)

    const mergedSlots = mergeSelectedSlots(selected)

    selectedText.innerText = (dateText ? dateText + ' — ' : '') + mergedSlots.join(' | ')
    btnCreate.classList.remove('hidden')
  }

  window.setDateAndLoad = function (val) {
    if (!dateEl) return
    dateEl.value = val
    try {
      dateEl.setAttribute('value', val)
    } catch (e) {}
    selected.length = 0
    document.querySelectorAll('.slot').forEach(el => el.classList.remove('selected', 'booked'))
    updateSelectedInfo()
    loadBookedSlots(val)
  }

  // khi chọn ngày mời
  if (dateEl && dateEl.value) {
    loadBookedSlots(dateEl.value)
  }

  // date change
  if (dateEl) {
    dateEl.addEventListener('change', () => {
      setDateAndLoad(dateEl.value)
      // bắn sự kiện cho module khác
      document.dispatchEvent(
        new CustomEvent('booking:dateChanged', { detail: { date: dateEl.value } })
      )
    })

    // lắng nghe thay đổi ngày
    document.addEventListener('booking:dateChanged', e => {
      // lấy ngày từ event
      const d = e?.detail?.date
      if (!d) return
      if (dateEl.value !== d) {
        // đồng bộ UI và load lại dữ liệu
        setDateAndLoad(d)
        try {
          dateEl.setAttribute('value', d)
        } catch (err) {}
      }
    })
    // khôi phục ngày đã chọn
    const savedDate = getDateFromStorage()
    if (savedDate && dateEl) {
      dateEl.value = savedDate
    }

    // load dữ liệu theo ngày
    if (dateEl && dateEl.value) {
      loadBookedSlots(dateEl.value)
    }
  }

  const fp = flatpickr('.date-full', {
    altInput: true,
    altFormat: 'l, d/m/Y',
    dateFormat: 'Y-m-d',
    locale: 'vn',
    enableTime: false,
    disableMobile: true,

    onReady(selectedDates, dateStr) {
      if (dateStr) {
        saveDateToStorage(dateStr)
        setDateAndLoad(dateStr)
        document.dispatchEvent(
          new CustomEvent('booking:dateChanged', { detail: { date: dateStr } })
        )
      }
    },

    onChange(selectedDates, dateStr) {
      saveDateToStorage(dateStr)
      setDateAndLoad(dateStr)
      document.dispatchEvent(new CustomEvent('booking:dateChanged', { detail: { date: dateStr } }))
    },
  })

  // Prev / Next / Today
  const btnPrev = document.getElementById('btnPrevDate')
  const btnNext = document.getElementById('btnNextDate')
  const btnToday = document.getElementById('btnToday')

  if (btnPrev) {
    btnPrev.addEventListener('click', () => {
      const cur = fp.selectedDates[0] || new Date()
      cur.setDate(cur.getDate() - 1)

      const newDate = formatDateInput(cur)
      fp.setDate(newDate, true)
      saveDateToStorage(newDate)

      document.dispatchEvent(new CustomEvent('booking:dateChanged', { detail: { date: newDate } }))
    })
  }
  if (btnNext) {
    btnNext.addEventListener('click', () => {
      const cur = fp.selectedDates[0] || new Date()
      cur.setDate(cur.getDate() + 1)

      const newDate = formatDateInput(cur)
      fp.setDate(newDate, true)
      saveDateToStorage(newDate)

      document.dispatchEvent(new CustomEvent('booking:dateChanged', { detail: { date: newDate } }))
    })
  }

  if (btnToday) {
    btnToday.addEventListener('click', () => {
      const today = new Date()
      const newDate = formatDateInput(today)

      fp.setDate(newDate, true)
      saveDateToStorage(newDate)

      document.dispatchEvent(new CustomEvent('booking:dateChanged', { detail: { date: newDate } }))
    })
  }

  function calcDuration(start, end) {
    const [sh, sm] = start.split(':').map(Number)
    const [eh, em] = end.split(':').map(Number)
    return (eh * 60 + em - (sh * 60 + sm)) / 60
  }

  function appendRow(index, type, data, ids = { detail: [], share: [] }) {
    const tbody = document.querySelector('table tbody')
    const now = new Date()
    if (type === 1) {
      type_text = 'Sân lẻ'
      type_style = 'bg-success'
    }

    if (type === 2) {
      type_text = 'Sân cố định'
      type_style = 'bg-info'
    }
    if (type === 3) {
      type_text = 'Tham gia sân ghép'
      type_style = 'bg-warning'
    }

    let status_style = ''
    let status_text = ''

    const activationDate = new Date(data.activation_date.replace(' ', 'T'))

    if (activationDate <= now) {
      status_style = 'badge-success'
      status_text = 'Đã thanh toán'
    } else {
      status_style = 'badge-primary'
      status_text = 'Đặt cọc'
    }

    const tr = document.createElement('tr')

    tr.innerHTML = `
        <td class="text-center">${index}</td>
         <td class="text-start">${data.court_name}</td>
        <td class="customer">${data.customer_name}</td>
        <td  class="text-center">
            <span class="badge ${type_style}">
                ${type_text}
            </span>
        </td>
       
        <td class="text-center time">
            ${data.start_time.slice(0, 5)} - ${data.end_time.slice(0, 5)}
        </td>
        <td class="text-end duration">
            ${calcDuration(data.start_time, data.end_time)} giờ
        </td>
        <td class="text-end price">
            ${formatMoney(data.price)}
        </td>
        <td class="text-center">
            <span class='badge-custome ${status_style}'>${status_text}</span>
        </td>
     
    `

    tbody.appendChild(tr)

    return {
      el: tr,
      start_time: data.start_time,
      end_time: data.end_time,
      price: data.price,
      slots: data.slots,
      ids: ids,
    }
  }

  function renderListBookedSlots(data) {
    const tbody = document.querySelector('table tbody')
    tbody.innerHTML = ''
    const entries = []
    data.forEach(item => {
      if (item.type === 3 && item.share_slot_booking?.length > 1) {
        item.share_slot_booking.forEach(share => {
          entries.push({
            court_id: item.court_id,
            court_name: item.court?.name || '',
            start_time: item.start_time,
            end_time: item.end_time,
            price: Number(share.amount) || 0,
            customer_name: share.customer?.name ?? 'Khách lẻ',
            type: item.type,
            date: item.date,
            detail_ids: [],
            share_ids: [share.id],
            slots: share.slots || 0,
            activation_date: item.booking.activation_date || null,
          })
        })
      } else if (
        item.type == 3 &&
        (!item.share_slot_booking || item.share_slot_booking.length === 0)
      ) {
        // nếu là booking share chưa có người tham gia thì bỏ qua
        return
      } else if (item.type != 3) {
        entries.push({
          court_id: item.court_id,
          court_name: item.court?.name || '',
          start_time: item.start_time,
          end_time: item.end_time,
          price: Number(item.price) || 0,
          customer_name: item.customer?.name ?? 'Khách lẻ',
          type: item.type,
          date: item.date,
          detail_ids: [item.id],
          share_ids: [],
          slots: item.slots || null,
          activation_date: item.booking.activation_date || null,
        })
      }
    })

    // sắp xếp thời gian theo sân tập và thời gian bắt đầu
    entries.sort((a, b) => {
      if (a.court_id !== b.court_id) return (a.court_id || 0) - (b.court_id || 0)
      const am = timeToMinutes(a.start_time)
      const bm = timeToMinutes(b.start_time)
      return am - bm
    })

    const groups = []

    entries.forEach(entry => {
      const last = groups.length ? groups[groups.length - 1] : null
      if (
        last &&
        last.court_id === entry.court_id &&
        last.type === entry.type &&
        last.customer_name === entry.customer_name &&
        last.date === entry.date &&
        last.end_time === entry.start_time
      ) {
        // merge
        last.end_time = entry.end_time
        last.price = last.price + entry.price
        last.slots = (last.slots || 0) + (entry.slots || 0)
        last.detail_ids = last.detail_ids.concat(entry.detail_ids)
        last.share_ids = last.share_ids.concat(entry.share_ids)
      } else {
        groups.push(Object.assign({}, entry))
      }
    })

    // render dữ liệu đã group
    let index = 1
    groups.forEach(g => {
      const ids = { detail: g.detail_ids || [], share: g.share_ids || [] }
      const rowData = {
        customer_name: g.customer_name,
        court_name: g.court_name || '',
        start_time: g.start_time,
        end_time: g.end_time,
        price: g.price,
        slots: g.slots,
        activation_date: g.activation_date,
      }

      const row = appendRow(index++, g.type, rowData, ids)
    })
  }

  function attachSlotHover(slot, bookingId, isShared) {
    const tooltip = document.getElementById('slotTooltip')

    if (!tooltip) return

    slot.onmouseenter = function (e) {
      let html = ''

      if (isShared) {
        const s = window.sharedSessions[bookingId]

        if (!s) return

        html = `<div class="booking-tooltip-card">
         <div class="tt-header">
            <div class="tt-title">Chi tiết lịch đặt</div>
              <span class="badge bg-warning">
                Sân ghép
              </span>
            </div>

            <div class="tt-row">
              <i class="ri-barcode-fill"></i>
              ${s.booking_code || ''}
            </div>

            <div class="tt-row">
              <i class="ri-map-pin-line"></i> ${slot.dataset.court}<br>
            </div>

            <div class="tt-row">
              <i class="ri-time-line"></i> ${s.start_time.slice(0, 5)} - ${s.end_time.slice(0, 5)}<br>
            </div>

            <div class="tt-row">
              <i class="ri-user-3-line"></i>  ${s.count}/${s.slots}<br>
            </div>

            <div class="tt-row">
              <i class=" ri-money-dollar-box-line"></i> ${formatMoney(s.price)}<br>
            </div>  
                <hr>
               <div class="tt-footer">
            <div>Tạo lúc: ${main.formatDate(s.created_at, true) || ''}</div>
            <div>Người tạo: ${s.create_by_name}</div>
            <div class="text-info small mt-1"><i class="mdi mdi-information-outline pe-1"></i>Nhấn để đăng kí sân ghép</div>

          </div>
     </div>
      `
      } else {
        const b = window.bookingDataMap[bookingId]

        if (!b) return

        let typeLabel = ''
        let typeClass = ''

        if (b.type == 1) {
          typeLabel = 'Sân lẻ'
          typeClass = 'type-single'
        }

        if (b.type == 2) {
          typeLabel = 'Sân cố định'
          typeClass = 'type-fixed'
        }
        html = `
        <div class="booking-tooltip-card">

          <div class="tt-header">
            <div class="tt-title">Chi tiết lịch đặt</div>
            <span class="tt-type ${typeClass}">
              ${typeLabel}
            </span>
          </div>

          <div class="tt-user">
            <div class="tt-avatar">
              <i class="ri-user-3-line"></i>
            </div>

            <div class="tt-user-info">
              <div class="tt-name">${b.customer.name || 'Khách lẻ'}</div>
              <div class="tt-code">${b.customer.code || ''}</div>
            </div>
          </div>
            <div class="tt-row">
            <i class="ri-barcode-fill"></i>
            ${b.booking?.code || ''}
          </div>

          <div class="tt-row">
            <i class="ri-calendar-line"></i>
            ${main.formatDate(b.date) || ''}
          </div>

          <div class="tt-row">
            <i class="ri-time-line"></i>
            ${b.start_time.slice(0, 5)} - ${b.end_time.slice(0, 5)}
          </div>

          <div class="tt-row">
            <i class="ri-map-pin-line"></i>
            ${slot.dataset.court}
          </div>

          <hr>

          <div class="tt-footer">
            <div>Tạo lúc: ${main.formatDate(b.created_at, true) || ''}</div>
            <div>Người tạo: ${b.booking?.create_by?.name || b.booking?.create_by?.user_name || 'Không xác định'}</div>
          </div>

        </div>
        `
      }

      tooltip.innerHTML = html

      tooltip.classList.remove('d-none')

      tooltip.style.left = e.clientX + 15 + 'px'
      tooltip.style.top = e.clientY + 15 + 'px'
    }

    slot.onmousemove = function (e) {
      tooltip.style.left = e.clientX + 15 + 'px'
      tooltip.style.top = e.clientY + 15 + 'px'
    }

    slot.onmouseleave = function () {
      tooltip.classList.add('d-none')
    }
  }

  // refill date range khi mở modal chỉnh sửa đặt lịch sân
  $('#editRecurringBookingModal').on('shown.bs.modal', function () {
    const input = document.querySelector('#editRecurringBookingModal .date-range-min')

    if (input && input._flatpickr) {
      input._flatpickr.setDate(input.value, false)
    }
  })

  $('#editShareBookingModal').on('shown.bs.modal', function () {
    const input = document.querySelector('#editShareBookingModal .date-single-min')

    if (input && input._flatpickr) {
      input._flatpickr.setDate(input.value, false)
    }
  })
})

window.showBookingDetail = function (bookingId, type) {
  main_layout.show_loader()

  $.ajax({
    url: `/admin/booking/show/${bookingId}`,
    type: 'GET',
    dataType: 'json',

    success: function (result) {
      main_layout.hide_loader()

      type = Number(type)
      const status = result.booking.timeStatus
      // ===== SÂN LẺ =====
      if (type === 1) {
        const modal = $('#singleBookingDetailModal')

        modal.find('.booking-created-at').text(result.booking.created_at ?? '')
        modal.find('.booking-note').text(result.booking.note ?? '')
        modal.find('.booking-created-by').text(result.booking.created_by ?? 'Không xác định')
        modal.find('.booking-code').text(result.booking.code ?? '')

        modal
          .find('.booking-status')
          .attr('class', `booking-status badge-custome ${status?.class ?? 'badge-secondary'}`)
          .text(status?.text ?? '')

        let tbody = ''
        ;(result.details ?? []).forEach((d, i) => {
          tbody += `
                        <tr>
                            <td class="text-center">${i + 1}</td>
                            <td class="text-start">${d.court_name}</td>
                            <td class="text-center">${d.date}</td>
                            <td class="text-center">${d.time}</td>
                            <td class="text-center">${d.duration} giờ</td>
                        </tr>
                    `
        })
        renderCustomer(modal, result.customer)
        modal.find('tbody').html(tbody)
        modal.modal('show')
      }

      // ===== SÂN CỐ ĐỊNH =====
      if (type === 2) {
        const modal = $('#recurringBookingDetailModal')

        modal.find('.booking-created-at').text(result.booking.created_at ?? '')
        modal.find('.booking-range-time').text(result.booking.range_date ?? '')
        modal.find('.booking-note').text(result.booking.note ?? '')
        modal.find('.booking-created-by').text(result.booking.created_by ?? 'Không xác định')
        modal.find('.booking-code').text(result.booking.code ?? '')

        const btnEditRecurring = modal.find('#btnEditRecurring')
        if (result.booking.can_edit) {
          btnEditRecurring.show()

          btnEditRecurring.off('click').on('click', function () {
            $('#recurringBookingDetailModal').modal('hide')
            showEditBookingModal(type, bookingId)
          })
        } else {
          btnEditRecurring.hide()
        }

        modal
          .find('.booking-status')
          .attr('class', `booking-status badge-custome ${status?.class ?? 'badge-secondary'}`)
          .text(status?.text ?? '')

        let tbody = ''
        ;(result.details ?? []).forEach((d, i) => {
          tbody += `
                        <tr>
                            <td class="text-center">${i + 1}</td>
                            <td class="text-start">${d.court_name}</td>
                            <td class="text-center">${d.date}</td>
                            <td class="text-center">${d.time}</td>
                            <td class="text-center">${d.duration} giờ</td>
                        </tr>
                    `
        })
        renderCustomer(modal, result.customer)
        modal.find('tbody').html(tbody)
        modal.modal('show')
      }

      // ===== SÂN GHÉP =====
      if (type === 3) {
        const modal = $('#shareBookingDetailModal')
        modal.find('.booking-created-at').text(result.booking.created_at ?? '')
        modal.find('.booking-court-name').text(result.booking.court_name ?? '')
        modal.find('.booking-detail-date').text(result.booking.date ?? '')
        modal.find('.booking-date-range').text(result.booking.time ?? '')
        modal.find('.booking-code').text(result.booking.code ?? '')
        modal.find('.booking-count').text(result.booking.count ?? '')
        modal.find('.booking-slots').text(result.booking.slots ?? '')
        modal.find('.booking-price').text(formatMoney(result.booking.price) ?? '')

        const btnJoin = modal.find('#btnJoinShareBooking')
        const btnEdit = modal.find('#btnEditShareBooking')

        // nếu chưa đầy slot
        if (status.text == 'Còn hạn' || status.text == 'Chưa diễn ra') {
          if ((result.booking.count ?? 0) < (result.booking.slots ?? 0)) {
            btnJoin.show()

            btnJoin.off('click').on('click', function () {
              $('#shareBookingDetailModal').modal('hide')
              openShareModal(bookingId)
            })
          } else {
            btnJoin.hide()
          }
        }

        if (
          result.booking.count == 0 &&
          result.booking.can_edit &&
          (status.text != 'Còn hạn' || status.text != 'Chưa diễn ra')
        ) {
          btnEdit.show()
          btnEdit.off('click').on('click', function () {
            $('#shareBookingDetailModal').modal('hide')
            showEditBookingModal(type, bookingId)
          })
        } else {
          btnEdit.hide()
        }

        modal
          .find('.booking-status')
          .attr('class', `booking-status badge-custome ${status?.class ?? 'badge-secondary'}`)
          .text(status?.text ?? '')

        let tbody = ''
        if (!result.booking.can_edit) {
          $('.th-action').hide()
        } else {
          $('.th-action').show()
        }

        ;(result.participants ?? []).forEach((p, i) => {
          let actionColumn = ''

          if (result.booking.can_edit) {
            actionColumn = `
             
        <td class="text-center"
            onclick="deleteShareSlotBooking('${p.share_slot_booking_id}')">
            <a class="icon-delete icon-action" title="Hủy lịch đặt">
                <i class="mdi mdi-delete"></i>
            </a>
        </td> `
          }

          tbody += `
        <tr id="${p.share_slot_booking_id}">
            <td class="text-center">${i + 1}</td>
            <td class="text-start">${p.name}</td>
            <td class="text-center">${p.code ?? '-'}</td>
            <td class="text-center">${p.phone ?? '-'}</td>
            <td class="text-center">${p.slots}</td>
            <td class="text-center">${formatMoney(result.booking.price * p.slots)}</td>
            <td class="text-center">${p.created_at}</td>
            <td class="text-center"><span class="badge ${p.status_class}">${p.status_text}</span>
          </td>
            ${canCancelBooking ? actionColumn : ''}
        </tr>
    `
        })

        modal.find('tbody').html(tbody)
        modal.modal('show')
      }
    },

    error: function () {
      main_layout.hide_loader()
      main_layout.alert_main('Không tải được chi tiết đặt lịch', 'error')
    },
  })
}

function renderCustomer(modal, customer) {
  if (!customer) return

  console.log('customer', customer)

  modal.find('.customer-name').text(customer.name ?? 'Khách lẻ')
  modal.find('.customer-code').text(customer.code ? `Mã hội viên: ${customer.code}` : '')

  modal
    .find('.customer-email')
    .html(
      customer.email
        ? `<i class="ri-mail-line"></i> ${customer.email}`
        : `<i class="ri-mail-line"></i> Không có dữ liệu`
    )

  modal
    .find('.customer-phone')
    .html(
      customer.phone
        ? `<i class="ri-phone-line"></i> ${customer.phone}`
        : `<i class="ri-phone-line"></i> Không có dữ liệu`
    )

  modal
    .find('[name="customer_avatar"]')
    .attr('src', customer.url_image ?? '/assets/images/user-default.jpg')
}

function groupConflicts(conflicts) {
  const result = {}

  conflicts.forEach(item => {
    const dateKey = item.date
    const bookingKey = item.booking_id

    if (!result[dateKey]) result[dateKey] = {}
    if (!result[dateKey][bookingKey]) {
      result[dateKey][bookingKey] = {
        booking_id: item.booking_id,
        booking_code: item.booking_code,
        booking_type: item.booking_type,
        customer_name: item.customer_name ?? null,
        customer_phone: item.customer_phone ?? null,
        details: [],
      }
    }

    result[dateKey][bookingKey].details.push(item)
  })

  return result
}

function renderConflictList(conflicts) {
  const container = document.getElementById('conflictList')
  if (!container) {
    return
  }
  container.innerHTML = ''

  const grouped = groupConflicts(conflicts)

  const dateTpl = document.getElementById('tpl-conflict-date')
  const bookingTpl = document.getElementById('tpl-conflict-booking')
  const detailTpl = document.getElementById('tpl-conflict-detail')

  Object.entries(grouped).forEach(([date, bookings]) => {
    // DATE
    const dateNode = dateTpl.content.cloneNode(true)
    dateNode.querySelector('.date-label').innerText = main.formatDate(date)
    container.appendChild(dateNode)

    Object.values(bookings).forEach(booking => {
      // BOOKING
      const bookingNode = bookingTpl.content.cloneNode(true)
      bookingNode.querySelector('.booking-code').innerText = booking.booking_code

      const BOOKING_TYPE_CONFIG = {
        1: {
          label: 'Sân lẻ',
          class: 'bg-success',
        },
        2: {
          label: 'Sân cố định',
          class: 'bg-primary',
        },
        3: {
          label: 'Sân ghép',
          class: 'bg-warning',
        },
      }

      const typeEl = bookingNode.querySelector('.booking-type')
      const config = BOOKING_TYPE_CONFIG[booking.booking_type]

      if (config) {
        typeEl.innerText = config.label
        typeEl.classList.add(config.class)
      }

      // ===== CUSTOMER (ẩn nếu type = 3) =====
      if (booking.booking_type !== 3 && booking.customer) {
        const customerEl = bookingNode.querySelector('.booking-customer')
        customerEl.innerText = `Khách hàng: ${booking.customer.name ?? ''} - ${booking?.customer?.phone ?? ''}`
        customerEl.classList.remove('d-none')
      }

      const detailsContainer = bookingNode.querySelector('.booking-details')

      // ===== DETAILS =====
      booking.details.forEach(d => {
        const detailNode = detailTpl.content.cloneNode(true)
        detailNode.querySelector('.time').innerText = `${d.start_time} - ${d.end_time}`
        detailNode.querySelector('.court-name').innerText = d.court_name
        // detailNode.querySelector('.slot-badge').innerText = `${d.slots} phút`

        detailsContainer.appendChild(detailNode)
      })

      container.appendChild(bookingNode)
    })
  })

  $('#conflictBookingModal').modal('show')
  main_layout.hide_loader()
}

function deleteShareSlotBooking(id) {
  Swal.fire({
    title: '',
    text: 'Bạn có chắc chắn hủy đăng kí tham gia này không ?',
    type: 'warning',
    showCancelButton: true,
    confirmButtonColor: '#d33',
    cancelButtonColor: '#3085d6',
    confirmButtonText: 'Có',
    cancelButtonText: 'Không',
  }).then(result => {
    if (result.value) {
      main_layout.show_loader()
      $.ajax({
        url: 'booking/delete/share-slot/' + id,
        type: 'DELETE',
        headers: headersClient,
        data: {
          _token: main.token,
        },
        success: function (result) {
          main_layout.hide_loader()
          if ('success' in result) {
            $('#' + id).remove()
            Swal.fire({
              text: 'Hủy đăng ký tham gia thành công !',
              icon: 'success',
              timer: 1500,
            })

            let bookingRow = $('#' + result.booking_id)

            // cập nhật số người
            bookingRow
              .find('td')
              .eq(9)
              .text(result.new_slots + ' người')

            // cập nhật tiền
            bookingRow.find('td').eq(10).text(formatMoney(result.new_amount))

            $('#shareBookingDetailModal .booking-count').text(result.new_slots)

            $('#table-participants tbody tr').each(function (index) {
              $(this)
                .find('td:first')
                .text(index + 1)
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
  })
}

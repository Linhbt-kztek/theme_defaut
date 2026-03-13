document.addEventListener('DOMContentLoaded', function () {
  const header = document.getElementById('timelineHeader')
  const body = document.getElementById('timelineBody')
  const container = document.getElementById('timelineContainer')
  const selectedText = document.getElementById('selectedText')
  const btnCreate = document.getElementById('btnCreate')
  const btnSaveBooking = document.getElementById('btnSaveBooking')

  let isDragging = false
  // let ctrlPressed = false

  // document.addEventListener('keydown', e => {
  //   if (e.ctrlKey) ctrlPressed = true
  // })
  // document.addEventListener('keyup', e => {
  //   if (!e.ctrlKey) ctrlPressed = false
  // })
  document.addEventListener('mouseup', () => {
    isDragging = false
  })

  const startHour = 0
  const endHour = 24
  const slots = []

  function generateTimeSlots() {
    for (let h = startHour; h < endHour; h++) {
      slots.push(`${h}:00`)
      slots.push(`${h}:30`)
    }
  }

  function renderHeader() {
    if (!header) return
    const spacer = document.createElement('div')
    header.appendChild(spacer)

    slots.forEach((t, index) => {
      const div = document.createElement('div')
      div.innerText = t
      header.appendChild(div)
    })
  }

  window.toggleSelect = function (slot) {
    if (slot.classList.contains('booked')) return
    if (slot.classList.contains('shared-yellow')) return

    const key = `${slot.dataset.court_id}|${slot.dataset.court}|${slot.dataset.time}`

    if (slot.classList.contains('selected')) {
      slot.classList.remove('selected')
      const idx = selected.indexOf(key)
      if (idx > -1) selected.splice(idx, 1)
    } else {
      slot.classList.add('selected')
      selected.push(key)
    }

    updateSelectedInfo()
  }

  window.formatDateWithWeekday = function (dateStr) {
    if (!dateStr) return ''

    let date

    // Nếu là DD/MM/YYYY
    if (dateStr.includes('/')) {
      const [d, m, y] = dateStr.split('/').map(Number)
      date = new Date(y, m - 1, d)
    } else {
      // YYYY-MM-DD
      date = new Date(dateStr)
    }

    if (isNaN(date.getTime())) return ''

    const days = ['Chủ nhật', 'Thứ Hai', 'Thứ Ba', 'Thứ Tư', 'Thứ Năm', 'Thứ Sáu', 'Thứ Bảy']

    const dd = String(date.getDate()).padStart(2, '0')
    const mm = String(date.getMonth() + 1).padStart(2, '0')
    const yyyy = date.getFullYear()

    return `${days[date.getDay()]} ${dd}/${mm}/${yyyy}`
  }
  if (btnCreate) {
    btnCreate.onclick = () => {
      const dateEl = document.getElementById('date')
      const dateValue = dateEl ? dateEl.value : ''

      bookingTemp.date = dateValue
      bookingTemp.day_of_week = formatDateWithWeekday(dateValue)
      buildBookingTemp()

      // hiển thị text
      const modalTimeEl = document.getElementById('modalTime')
      if (modalTimeEl) modalTimeEl.value = selectedText.innerText

      // hidden inputs
      const dateInputEl = document.querySelector('[name="date"]')
      if (dateInputEl) dateInputEl.value = bookingTemp.date
      const dayOfWeekInputEl = document.querySelector('[name="day_of_week"]')
      if (dayOfWeekInputEl) dayOfWeekInputEl.value = bookingTemp.day_of_week
      calculatePrice(1)

      $('#singleBookingModal').modal('show')
    }
  }

  if (btnSaveBooking) {
    btnSaveBooking.onclick = () => {
      $('#bookingModal').modal('show')
    }
  }

  const courtFilter = document.getElementById('courtFilter')
  window.renderCourts = function () {
    // clear body
    if (!body) return
    body.innerHTML = ''

    // compute selected court ids
    let selectedCourtIds = null
    if (courtFilter) {
      const opts = Array.from(courtFilter.selectedOptions || [])
      selectedCourtIds = opts.filter(o => o.value).map(o => String(o.value))
      if (selectedCourtIds.length === 0) selectedCourtIds = null
    }

    const toRender = selectedCourtIds
      ? courts.filter(c => selectedCourtIds.includes(String(c.id)))
      : courts

    toRender.forEach(court => {
      const row = document.createElement('div')
      row.className = 'timeline-row'

      const name = document.createElement('div')
      name.className = 'court-name'
      name.innerText = court.name
      row.appendChild(name)
      slots.forEach(time => {
        const slot = document.createElement('div')
        slot.className = 'slot'

        slot.dataset.court = court.name
        slot.dataset.court_id = court.id
        slot.dataset.time = time
        slot.dataset.type = court.type

        slot.addEventListener('mousedown', e => {
          if (e.button === 0) {
            isDragging = true
          }
        })
        slot.addEventListener('mouseenter', () => {
          if (isDragging) {
            hasDragged = true
            toggleSelect(slot)
          }
        })
        document.addEventListener('mouseup', () => {
          isDragging = false
        })

        row.appendChild(slot)
      })

      body.appendChild(row)
    })

    const dateElForBooked = document.getElementById('date')

    if (dateElForBooked && dateElForBooked.value && typeof loadBookedSlots === 'function') {
      loadBookedSlots(dateElForBooked.value)
    }
  }

  window.timeToMinutes = function (time) {
    const [h, m] = time.split(':').map(Number)
    return h * 60 + m
  }

  generateTimeSlots()
  renderHeader()
  renderCourts()

  setTimeout(() => {
    const index = slots.indexOf('7:00')
    if (!container) return
    container.scrollLeft = index * 80
  }, 100)

  const dateElForBooked = document.getElementById('date')
  if (dateElForBooked && dateElForBooked.value && typeof loadBookedSlots === 'function') {
    loadBookedSlots(dateElForBooked.value)
  }

  function add30Minutes(time) {
    const [h, m] = time.split(':').map(Number)
    const total = h * 60 + m + 30
    const hh = String(Math.floor(total / 60)).padStart(2, '0')
    const mm = String(total % 60).padStart(2, '0')
    return `${hh}:${mm}`
  }

  function buildBookingTemp() {
    bookingTemp.courts = {}

    selected.forEach(item => {
      const [court_id, court_name, start] = item.split('|')
      const end = add30Minutes(start)

      if (!bookingTemp.courts[court_id]) {
        bookingTemp.courts[court_id] = {
          court_id: court_id,
          court_name: court_name,
          rangeTime: [],
        }
      }

      bookingTemp.courts[court_id].rangeTime.push({
        start_time: start,
        end_time: end,
        minutes: 30,
      })
    })

    return bookingTemp
  }
})

// xử lý load trang
const DATE_STORAGE_KEY = 'schedule_selected_date'
function saveDateToStorage(date) {
  if (!date) return
  localStorage.setItem(DATE_STORAGE_KEY, date)
}

function getDateFromStorage() {
  return localStorage.getItem(DATE_STORAGE_KEY)
}

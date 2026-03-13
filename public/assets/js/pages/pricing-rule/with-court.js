document.addEventListener('DOMContentLoaded', () => {
  let currentType = getActiveType()
  let editingType = null
  let originalPricingData = {}

  syncButtons()

  // TAB CLICK
  document.getElementById('pricingTabs').addEventListener('click', e => {
    const tab = e.target.closest('li[data-value]')
    if (!tab) return

    setActiveTab(tab)
    currentType = tab.dataset.value

    if (editingType && editingType !== currentType) {
      return
    }

    syncButtons()
  })

  // ACTION BUTTONS (EDIT, SAVE)
  document.getElementById('actionButtons').addEventListener('click', e => {
    const btn = e.target.closest('button[data-type]')
    if (!btn) return

    const type = btn.dataset.type

    if (btn.classList.contains('btn-edit')) {
      editingType = type
      enableEditMode(type)
      const oldData = collectTableData(type)
      originalPricingData[type] = JSON.parse(JSON.stringify(oldData))
    }

    if (btn.classList.contains('btn-save')) {
      const data = collectTableData(type)
      saveCourtPriceAjax(data)
      disableEditMode(type, data)
      editingType = null
    }

    syncButtons()
  })

  function syncButtons() {
    document.querySelectorAll('#actionButtons button').forEach(btn => {
      const matchType = btn.dataset.type === currentType
      const isEdit = btn.classList.contains('btn-edit')
      const isSave = btn.classList.contains('btn-save')

      btn.classList.toggle(
        'd-none',
        !matchType ||
          (isEdit && editingType === currentType) ||
          (isSave && editingType !== currentType)
      )
    })
  }

  function enableEditMode(type) {
    document.querySelectorAll(`table[data-type="${type}"] tbody tr`).forEach(row => {
      row.querySelector('.price-cell span')?.classList.add('d-none')
      row.querySelector('.price-input')?.classList.remove('d-none')

      row.querySelector('.status-cell .badge')?.classList.add('d-none')
      row.querySelector('.status-select')?.classList.remove('d-none')
    })
  }

  function disableEditMode(type, newData) {
    const now = new Date().toLocaleString('vi-VN', {
      hour: '2-digit',
      minute: '2-digit',
      day: '2-digit',
      month: '2-digit',
      year: 'numeric',
      hour12: false,
    })
    const oldData = originalPricingData[type] || []

    document.querySelectorAll(`table[data-type="${type}"] tbody tr`).forEach((row, index) => {
      const oldItem = oldData[index]
      const newItem = newData[index]

      const isChanged =
        oldItem.cost_per_hour !== newItem.cost_per_hour || oldItem.is_use !== newItem.is_use

      row.querySelector('.price-cell').innerHTML = `
                <span>${newItem.cost_per_hour.toLocaleString()} ₫</span>
                <div class="price-input d-none">
                    <input type="number" class="form-control text-end price-input-field"
                        value="${newItem.cost_per_hour}">
                </div>
            `

      row.querySelector('.status-cell').innerHTML = `
                ${
                  newItem.is_use === 1
                    ? '<span class="badge bg-success">Hoạt động</span>'
                    : '<span class="badge bg-danger">Không hoạt động</span>'
                }
                <select class="form-select form-select-sm status-select d-none">
                    <option value="1" ${newItem.is_use === 1 ? 'selected' : ''}>Hoạt động</option>
                    <option value="2" ${
                      newItem.is_use === 2 ? 'selected' : ''
                    }>Không hoạt động</option>
                </select>
            `

      if (isChanged) {
        row.querySelector('.updated-at').innerHTML = `<span>${now}</span>`
      }
    })

    // Cập nhật lại original sau khi save thành công
    originalPricingData[type] = JSON.parse(JSON.stringify(newData))
  }
})

function getActiveType() {
  return document.querySelector('#pricingTabs li.active')?.dataset.value
}

function setActiveTab(tab) {
  document.querySelectorAll('#pricingTabs li').forEach(li => li.classList.remove('active'))

  tab.classList.add('active')
  const tabId = tab.dataset.tab
  if (tabId) {
    document.querySelectorAll('.pricing-tab-content').forEach(el => el.classList.add('d-none'))

    document.getElementById(tabId)?.classList.remove('d-none')
  }
}

function toggleButtons(type, isEditing) {
  document.querySelector(`.btn-edit[data-type="${type}"]`)?.classList.toggle('d-none', isEditing)

  document.querySelector(`.btn-save[data-type="${type}"]`)?.classList.toggle('d-none', !isEditing)
}

function collectTableData(type) {
  let data = []
  const activeTab = document.querySelector(`#pricingTabs li[data-value="${type}"]`)

  document.querySelectorAll(`table[data-type="${type}"] tbody tr`).forEach(row => {
    data.push({
      object: row.dataset.day ?? row.dataset.courtId,
      cost_per_hour: Number(row.querySelector('.price-input-field')?.value || 0),
      is_use: Number(row.querySelector('.status-select')?.value || 2),
      type: Number(type),
    })
  })

  return data
}

function saveCourtPriceAjax(data) {
  main_layout.show_loader()
  $.ajax({
    url: pricing_rule_index.url_save,
    method: 'POST',
    contentType: 'application/json',
    headers: {
      'X-CSRF-TOKEN': main.token,
    },
    data: JSON.stringify({ items: data }),
    success: function (res) {
      if (res.status) {
        main_layout.alert_main('Lưu thành công', 'success', 'center')
        main_layout.hide_loader()
      }
    },
    error: function (err) {},
  })
}

document.addEventListener('input', function (e) {
  if (e.target.classList.contains('price-input-field') || e.target.name === 'price_per_hour' || e.target.name === 'cost_per_hour') {
    let value = e.target.value

    // Loại bỏ dấu -
    value = value.replace(/-/g, '')

    // Nếu nhỏ hơn 0 thì ép về 0
    if (Number(value) < 0) {
      value = 0
    }

    e.target.value = value
  }
})

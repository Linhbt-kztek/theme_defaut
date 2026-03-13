function showHolidayRuleModal() {
  $('input[name="pricing_rule_id"]').val('')
  $('input[name="date"]').val('')
  $('input[name="price_per_hour"]').val('')

  $('#holidayRuleCreateModal').modal('show')
}

function submit_holiday_rule(type) {
  let $modal = ''

  if (type == 'edit') {
    $modal = $('#holidayRuleEditModal')
    let id = $modal.find('input[name="pricing_rule_id"]').val() ?? null
    url = pricing_rule_index.url_edit.replace('__ID__', id)
  } else {
    $modal = $('#holidayRuleCreateModal')
    url = pricing_rule_index.url_store
  }

  let dateValue = $modal.find('input[name="date"]').val()
  let priceValue = $modal.find('input[name="price_per_hour"]').val()
  let data = new FormData($modal.find('#holidayForm')[0])

  if (!dateValue) {
    main_layout.alert_main('Ngày áp dụng không được bỏ trống', 'error', 'center')
    return
  }

  if (!priceValue) {
    main_layout.alert_main('Chi phí không được bỏ trống', 'error', 'center')
    return
  }

  $.ajax({
    url: url,
    type: 'POST',
    data: data,
    processData: false,
    contentType: false,
    success: function (result) {
      main_layout.hide_loader()

      if (result.status == 200) {
        $modal.modal('hide')
        main_layout.alert_main(result.message, 'success', 'center')
        location.reload()
      } else if (result.status == 90 && result.message == 'conflict_range_day') {
        showExitPricingRule(result.data_conflict)
      } else {
        main_layout.alert_main(result.message, 'error', 'center')
      }
    },
  })
}
function showExitPricingRule(data) {
  if (!data || data.length === 0) return

  // Sort từ tương lai -> quá khứ
  data.sort((a, b) => {
    const [d1, m1, y1] = a.object.split('-')
    const [d2, m2, y2] = b.object.split('-')

    const date1 = new Date(`${y1}-${m1}-${d1}`)
    const date2 = new Date(`${y2}-${m2}-${d2}`)

    return date2 - date1
  })

  let html = ''

  data.forEach((item, index) => {
    html += `
      <tr>
        <td class="text-center">${index + 1}</td>
        <td class="text-center">${item.object.replace(/-/g,'/')}</td>
        <td class="text-end">${Number(item.cost_per_hour).toLocaleString('vi-VN')}</td>
      </tr>
    `
  })

  $('#conflictTableBody').html(html)
  $('#conflictModal').modal('show')
}
function showEditHolidayForm(id) {
  let url = pricing_rule_index.url_show.replace('__ID__', id)

  $.ajax({
    url: url,
    type: 'GET',
    success: function (result) {
      if (result.status !== 200) {
        main_layout.alert_main(result.message, 'error', 'center')
        return
      }
      const data = result.data
      const $modal = $('#holidayRuleEditModal')

      $modal.find('input[name="pricing_rule_id"]').val(id)
      $modal.find('input[name="price_per_hour"]').val(data.cost_per_hour)
      $modal.find('input[name="type"]').val(data.type)
      $modal.find('input[name="date"]').val(data.object)

      $modal.find('input[name="is_use"]').prop('checked', Number(data.is_use) === 1)

      $modal.modal('show')
    },
  })
}
document.addEventListener('DOMContentLoaded', () => {
  $('#holidayRuleEditModal').on('shown.bs.modal', function () {
    const input = document.querySelector('#holidayRuleEditModal .date-single')

    if (input && input._flatpickr) {
      input._flatpickr.setDate(input.value, false)
    }
  })
})

function showRangeTimeRuleModal() {
  $('#rangeTimeRuleModal').on('hidden.bs.modal', function () {
    this.querySelector('form').reset()
    $('input[name="pricing_rule_id"]').val('')
  })

  $('#rangeTimeRuleModal').modal('show')
}

function submit() {
  let id = $('input[name="pricing_rule_id"]').val() ?? null

  if (id) {
    url = pricing_rule_index.url_edit.replace('__ID__', id)
  } else {
    url = pricing_rule_index.url_store
  }
  if ($('#end_time').val() <= $('#start_time').val()) {
    main_layout.alert_main('Giờ kết thúc phải lớn hơn giờ bắt đầu', 'error', 'center')
    return
  }

  let data = new FormData($('#rangeTimeForm')[0])
  $.ajax({
    url: url,
    type: 'POST',
    data: data,
    processData: false,
    contentType: false,
    success: function (result) {
      main_layout.hide_loader()
      if (result.status == 200) {
        $('#rangeTimeRuleModal').modal('hide')
        main_layout.alert_main(result.message, 'success', 'center')
        location.reload()
      } else {
        main_layout.alert_main(result.message, 'error', 'center')
      }
    },
  })
}

function showEditRangeTimeForm(id) {
  $('#myModalLabel').text('Cập nhật biểu phí theo khung giờ')
  $('input[name="pricing_rule_id"]').val(id)

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
      console.log(data)

      // GIÁ
      $('input[name="cost_per_hour"]').val(data.cost_per_hour)

      // TRẠNG THÁI
      $('#flexSwitchCheckCheckedDisabled').prop('checked', Number(data.is_use) === 1)

      // THỜI GIAN (cắt giây cho input[type=time])
      $('#start_time').val(data.start_time.slice(0, 5))
      $('#end_time').val(data.end_time.slice(0, 5))

      // TYPE (ẩn nhưng vẫn set cho chắc)
      $('input[name="type"]').val(data.type)

      $('#rangeTimeRuleModal').modal('show')
    },
    error: function () {
      main_layout.alert_main('Không tải được dữ liệu', 'error')
    },
  })
}

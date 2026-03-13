function showCourtForm() {
  $('#court_id').val('')
  resetCourtForm()

  $('#courtFormModal').modal('show')
  $('#courtFormModal #myModalLabel').text('Thêm mới sân tập')
  $('#btn-save').text('Thêm')
}

function submit() {
  main_layout.show_loader()
  let id = $('input[name="court_id"]').val() ?? null

  if (id) {
    url = court_index.url_edit.replace('__ID__', id)
  } else {
    url = court_index.url_store
  }

  let data = new FormData($('#saveFormModal')[0])
  $.ajax({
    url: url,
    type: 'POST',
    data: data,
    processData: false,
    contentType: false,
    success: function (result) {
      main_layout.hide_loader()

      if (result.status == 200) {
        $('#courtFormModal').modal('hide')

        main_layout.alert_main(result.message, 'success', 'center')
        location.reload()
      } else {
        main_layout.alert_main(result.message, 'error', 'center')
      }
    },
  })
}

function showEditCountForm(id) {
  $('#courtFormModal #myModalLabel').text('Cập nhật sân tập')
  $('#btn-save').text('Cập nhật')
  $('input[name="court_id"]').val(id)

  let url = court_index.url_show.replace('__ID__', id)

  $.ajax({
    url: url,
    type: 'GET',
    success: function (result) {
      main_layout.hide_loader()

      if (result.status !== 200) {
        main_layout.alert_main(result.message, 'error', 'center')
        return
      }

      const data = result.data

      $('#name').val(data.name ?? '')
      $('#note').val(data.note ?? '')
      $('#flexSwitchCheckCheckedDisabled').prop('checked', data.status == 1)
      $('.court-type').prop('checked', false)

      if (data.type == 1 || data.type == 3) {
        $('#court_normal').prop('checked', true)
      }

      if (data.type == 2 || data.type == 3) {
        $('#court_team').prop('checked', true)
      }
      $('.open-type').prop('checked', false)
      $(`.open-type[value="${data.open_type}"]`).prop('checked', true)

      if (data.open_type == 2) {
        $('#open_time, #close_time').prop('disabled', false)

        $('#open_time').val(data.open_time?.substring(0, 5))
        $('#close_time').val(data.close_time?.substring(0, 5))
      } else {
        $('#open_time').val('00:00').prop('disabled', true)
        $('#close_time').val('23:59').prop('disabled', true)
      }

      $('#courtFormModal').modal('show')
    },
  })
}

function validateCourtType() {
  const checked = document.querySelectorAll('.court-type:checked')
  if (checked.length === 0) {
    alert('Vui lòng chọn ít nhất 1 loại sân')
    return false
  }
  return true
}

function resetCourtForm() {
  const form = $('#saveFormModal')

  /* ========== INPUT TEXT / TEXTAREA ========== */
  form.find('input[type="text"], textarea').val('')

  /* ========== STATUS SWITCH ========== */
  $('#flexSwitchCheckCheckedDisabled').prop('checked', false)

  /* ========== TYPE (checkbox) ========== */
  $('.court-type').prop('checked', false)
  document.getElementById('court_normal').checked = true

  /* ========== OPEN TYPE (radio) ========== */
  $('.open-type').prop('checked', false)
  $('.open-type[value="1"]').prop('checked', true) // mặc định cả ngày

  /* ========== TIME ========== */
  $('#open_time').val('00:00').prop('disabled', true)

  $('#close_time').val('23:59').prop('disabled', true)

  /* ========== HIDDEN ID ========== */
  $('input[name="court_id"]').val('')

  /* ========== LABEL + BUTTON ========== */
  $('#modalLabel').text('Thêm mới sân tập')
}

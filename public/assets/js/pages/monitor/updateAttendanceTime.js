function startCheckin(meeting_id) {
  main_layout.show_loader()

  let url = '/admin/meeting/start-checkin/' + meeting_id
  let data = { time: new Date().toISOString(), _token: main.token }

  $.ajax({
    url: url,
    type: 'POST',
    data: data,
    headers: headersClient,
    success: function (result) {
      main_layout.hide_loader()
      $('#confirmModal').modal('hide')
      if (result.status === 200) {
        $('#checkin-time').text(
          new Date(result.checkin_time).toLocaleTimeString('vi-VN', {
            hour: '2-digit',
            minute: '2-digit',
          })
        )

        Swal.fire({ text: result.message, icon: 'success', timer: 1500 })

        $('#checkin-action').html(`
                    <button 
                        class="btn btn-red btn-color-white btn-sm" 
                        style="width:fit-content"
                        onclick="endMeeting('${meeting_id}')">
                        Kết thúc phiên họp
                    </button>
                `)
      } else {
        Swal.fire({ text: result.message, icon: 'error', timer: 3000 })
      }
    },
    error: function () {
      main_layout.hide_loader()

      Swal.fire({ text: 'Có lỗi xảy ra khi bắt đầu check-in', icon: 'error', timer: 3000 })
    },
  })
}

//  kết thúc phiên họp
function endSession(meeting_id) {
  main_layout.show_loader()
  let url = '/admin/meeting/end-session/' + meeting_id
  let data = { time: new Date().toISOString(), _token: main.token }
  $.ajax({
    url: url,
    type: 'POST',
    headers: headersClient,
    data: data,
    success: function (result) {
      main_layout.hide_loader()

      $('#confirmModal').modal('hide')
      if (result.status === 200) {
        Swal.fire({ text: result.message, icon: 'success', timer: 1500 })
        $('#end-time-session').text(result.end_time)
        $('#checkin-action').html(`
                `)
      } else {
        main_layout.hide_loader()

        Swal.fire({ text: result.message, icon: 'error', timer: 3000 })
      }
    },
    error: function () {
      Swal.fire({ text: 'Có lỗi xảy ra khi kết thúc phiên họp', icon: 'error', timer: 3000 })
    },
  })
}

function showConfirmModal(meeting_id, type = null) {
  $('#confirmModal').modal('show')
  if (type === 1) $('#confirmModal #title').text('Bạn có chắc chắn cho phép điểm danh sớm?')
  if (type === 2) $('#confirmModal #title').text('Bạn có chắc chắn kết thúc cuộc họp sớm?')
  $('#btn-action').data('meeting_id', meeting_id)
  $('#btn-action').data('type', type)
}

window.handleAttendaceTime = function (el) {
  const meeting_id = $(el).data('meeting_id')
  const type = $(el).data('type')

  if (type === 1) startCheckin(meeting_id)
  if (type === 2) endSession(meeting_id)
}

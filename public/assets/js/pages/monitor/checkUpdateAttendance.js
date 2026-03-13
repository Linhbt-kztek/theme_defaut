

let monitorCheckUpdateAttendance = {
  checkUpdate: "/admin/monitor/checkUpdate",
  staff_time_checked: '',
  guest_time_checked: '',
  meeting_detail_id: '',
  end_time: '',
  run_checkUpdate: true
}

function runEverySecond() {
  if (monitorCheckUpdateAttendance.run_checkUpdate) {
    const intervalId = setInterval(checkUpdate, 2000)
  }
}

function checkUpdate() {
  if (monitorCheckUpdateAttendance.run_checkUpdate && monitorCheckUpdateAttendance.meeting_detail_id != '') {
    $.ajax({
      url: monitorCheckUpdateAttendance.checkUpdate,
      type: 'get',
      data: {
        meeting_detail_id: monitorCheckUpdateAttendance.meeting_detail_id,
      },
      success: function (result) {
        if (result.has_event) {
          if (
            monitorCheckUpdateAttendance.guest_time_checked != result.event_with_meeting_detail.staff &&
            result.event_with_meeting_detail.staff != ''
          ) {
            getListMember()
            monitorCheckUpdateAttendance.guest_time_checked = result.event_with_meeting_detail.staff
          }
        }
      },
    })
  }
}

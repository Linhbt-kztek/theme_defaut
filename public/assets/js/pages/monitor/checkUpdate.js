function runEverySecond() {
  if (monitorCheckUpdate.run_checkUpdate) {
    checkUpdate()
    const intervalId = setInterval(checkUpdate, 1000)
  }
}

function checkUpdate() {
  if (monitorCheckUpdate.run_checkUpdate && monitorCheckUpdate.meeting_detail_id != '') {
    $.ajax({
      url: monitorCheckUpdate.checkUpdate,
      type: 'get',
      data: {
        meeting_detail_id: monitorCheckUpdate.meeting_detail_id,
      },
      success: function (result) {
        if (result.has_event) {
            
          if (
            monitorCheckUpdate.staff_time_checked != result.event_with_meeting_detail.guest &&
            result.event_with_meeting_detail.guest != ''
          ) {
            fetchData()
            monitorCheckUpdate.staff_time_checked = result.event_with_meeting_detail.guest
          }

          if (
            monitorCheckUpdate.guest_time_checked != result.event_with_meeting_detail.staff &&
            result.event_with_meeting_detail.staff != ''
          ) {
            fetchData()
            monitorCheckUpdate.guest_time_checked = result.event_with_meeting_detail.staff
          }
        }
      },
    })
  }
}

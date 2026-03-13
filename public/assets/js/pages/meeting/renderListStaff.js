document.addEventListener('DOMContentLoaded', function () {
  $(document).on('click', '.open-participants-modal', function () {
    const meetingId = $(this).data('meeting-id')

    const meeting = meeting_index.meetingsData.data.find(m => m.id == meetingId)

    if (!meeting) return

    const list = meeting.participants.map(p => ({
      name: p.staff?.name,
      code: p.staff?.code,
      cccd: p.staff?.identification,
      position: p.staff?.position,
      agencies_value: p.staff?.agencies_value || p.staff?.agency?.name,
      phone: p.staff?.phone,
    }))

    renderModalList('#participantsModal', list, 'Danh sách người tham gia')
    $('#participantsModal').modal('show')
  })

  $(document).on('click', '.open-contact-modal', function () {
    const meetingId = $(this).data('meeting-id')

    const meeting = meeting_index.meetingsData.data.find(m => m.id == meetingId)

    if (!meeting) return

    const list = meeting.contact.map(p => ({
      name: p.staff?.name,
      code: p.staff?.code,
      cccd: p.staff?.identification,
      position: p.staff?.position,
      agencies_value: p.staff?.agencies_value || p.staff?.agency?.name,
      phone: p.staff?.phone,
    }))

    renderModalList('#participantsModal', list, 'Danh sách người liên hệ')
    $('#participantsModal').modal('show')
  })

  $(document).on('click', '.open-host-modal', function () {
    const meetingId = $(this).data('meeting-id')

    const meeting = meeting_index.meetingsData.data.find(m => m.id == meetingId)

    if (!meeting) return

    const list = meeting.host.map(p => ({
      name: p.staff?.name,
      code: p.staff?.code,
      cccd: p.staff?.identification,
      position: p.staff?.position,
      agencies_value: p.staff?.agencies_value || p.staff?.agency?.name,
      phone: p.staff?.phone,
    }))

    renderModalList('#participantsModal', list, 'Danh sách người chủ trì')
    $('#participantsModal').modal('show')
  })

  $(document).on('click', '.open-guest-modal', function () {
    const meetingId = $(this).data('meeting-id')

    const meeting = meeting_index.meetingsData.data.find(m => m.id == meetingId)

    if (!meeting) return

    const list = meeting.guest.map(p => ({
      name: p?.name,
      gender: p.gender == 1 ? 'Nam' : p.gender == 2 ? 'Nữ' : '-',
      cccd: p?.cccd,
      birthday: p?.birthday,
      position: p?.position,
      agencies_value: p.agencies_value,
      phone: p.phone || '-',
    }))


    renderModalList('#guestsModal', list)
    $('#guestsModal').modal('show')
  })
})

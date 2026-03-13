

let screenFunction = {
  getDetail: function (id, showDetail) {
    $.ajax({
      url: screenPage.url_show + '/' + id,
      type: 'GET',
      success: function (response) {
        if (showDetail) {
          main_layout.hide_loader()
          screenFunction.showDetailScreen(response.data)
        } else {
          $('#form_screen input[name=name]').val(response.data.name)
          $('#form_screen select[name=conferenceRoom_id]').val(response.data.conference_room_id)
          $('#form_screen input[name=user_name]').val(response.data.user.user_name)
          $('#form_screen input[name=user_name]').attr('disabled', true)

          input = `<input type="hidden" value="` + response.data.id + `" name="id">`
          $('#form_screen').append(input)
        }
      },
    })
  },
  showDetailScreen: function (data) {
    $('#screen_detail .screen_id').val(data.id)

    $('#screen_detail .screen_name').text(data.name)
    previewFromUrl(data.url_defaut, data.isVideo)
    $('#screen_detail .flex-shrink-0 i').text(main.formatDate(data.updated_at, true))
  },
  showformBtn: function (date, screen_id) {
    url = screenPage.url_showform + `?date=${date}&screen_id=${screen_id}`
    $('#showformBtnSchedule').attr('href', url)
  },
}

function showForm(id = '') {
  $('#form_screen input[name=id]').remove()
  $('#form_screen input[name=name]').val('')
  $('#form_screen select[name=conferenceRoom_id]').val('')
  $('#form_screen input[name=user_name]').val('')
  $('#form_screen input[name=password]').val('')
  $('#form_screen input[name=user_name]').attr('disabled', false)

  if (id != '') {
    $('#create_screen .modal-title').text('Chỉnh sửa thông tin màn hình')
    screenFunction.getDetail(id, false)
  } else {
    $('#create_screen .modal-title').text('Thêm màn hình')
  }
  $('#create_screen').modal('show')
}

function save() {
  main_layout.show_loader()
  const data = new FormData(document.getElementById('form_screen'))
  $.ajax({
    url: screenPage.url_save,
    type: 'POST',
    data: data,
    processData: false,
    contentType: false,
    dataType: 'json',
    success: function (response) {
      main_layout.hide_loader()
      main_layout.alert_main(response.message, response.status == 200 ? 'success' : 'error')
      if (response.status == 200) {
        $('#create_screen').modal('hide')
        showDataSreen()
      }
    },
  })
}

function showDataSreen() {
  main_layout.show_loader()

  $.ajax({
    url: screenPage.url_get_data,
    type: 'GET',
    success: function (response) {
      html = $('#screen_item_template').html()
      html_append = ''
      index = 0
      first_element = {}
      response.data.forEach(element => {
        if (index == 0) {
          first_element = element
        }
        index++
        html_append += html
          .replaceAll('[id]', element.id)
          .replaceAll('[id]', element.id)
          .replaceAll('[name]', element.name)
          .replaceAll('[room]', element.conference_room != null ? element.conference_room.name : '')
          .replaceAll('[device_name]', element.screen_device_name)
          .replaceAll('[url]', screenPage.url_delete + '/' + element.id)
      })

      $('#list_screen_items').empty()
      main_layout.hide_loader()
      $('#list_screen_items').append(html_append)

      $('.custom-card').removeClass('active-item')
      $('#' + first_element.id + ' .custom-card').addClass('active-item')
      screenFunction.showDetailScreen(first_element)

    },
  })
}

function showDetail(id) {
  main_layout.show_loader()
  $('.custom-card').removeClass('active-item')
  $('#' + id + ' .custom-card').addClass('active-item')
  screenFunction.getDetail(id, true)
  $('#screen_detail').removeClass('d-none')

  screenPage.screen_id = id
  screenFunction.showformBtn(screenPage.date, id)
  getListSchedule()
}

function getListSchedule(date = '') {
  if (date == '') {
    date = $('#screen_date').val()
    saveLocalStogate()
  }
  if (screenPage.screen_id != '') {
    localStorage.setItem('screen.date', date)
    screenFunction.showformBtn(date, screenPage.screen_id)
    $.ajax({
      url: screenPage.url_getListSchedule,
      type: 'GET',
      data: {
        date: date != '' ? date : screenPage.date,
        screen_id: screenPage.screen_id,
      },
      success: function (response) {
        html = $('#schedule_item_template').html()
        html_append = ''
        if (response.data && response.data.length > 0) {
          response.data.forEach(element => {
            html_append += html
              .replaceAll('[id]', element.id)
              .replaceAll('idscheduledelete', element.id)
              .replaceAll(
                '[time]',
                main.formatDate(element.start_time, false, true) +
                ' - ' +
                main.formatDate(element.end_time, false, true)
              )
              .replaceAll(
                '[content]',
                element.classify == 2 ? element.content : (element.classify == 3?'Hiển thị danh sách thành viên chưa tham gia':'Hiển thị lịch họp')
              )
          })
        }

        $('#schedule_list_item').empty()
        $('#schedule_list_item').append(html_append)
      },
    })
  } else {
    main_layout.alert_main('Vui lòng chọn màn hình!', 'error')
  }
}

function saveLocalStogate(getItem = false) {
  if (getItem) {
    screenPage.date = localStorage.getItem('screen.date')
    screenPage.screen_id = localStorage.getItem('screen.screen_id')
  } else {
    localStorage.setItem('screen.date', $('#screen_date').val())
    localStorage.setItem('screen.screen_id', screenPage.screen_id)
  }
}

// function setDefault() {
//   screen_id = localStorage.getItem('screen.screen_id')
//   if (screen_id) {
//     $('#screen_date').val(localStorage.getItem('screen.date'))
//     saveLocalStogate(true)
//     showDetail(screen_id)
//   }
// }

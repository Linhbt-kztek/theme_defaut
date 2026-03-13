let shedulePage = {
  resources: {
    file_chose_id: ''
  }
}

function showFormAddResources() {
  $('#formAddResources').modal('show');
}

function uploadFile() {
  form = document.getElementById('form_upload_file');
  formData = new FormData(form);
  const dropzoneElement = document.querySelector('.dropzone');
  const myDropzone = Dropzone.forElement(dropzoneElement);

  total_size = 0;
  myDropzone.getAcceptedFiles().forEach(file => {
    // fileSizeMB = (file.size / 1024 / 1024).toFixed(2);
    total_size += file.size;
    formData.append('file[]', file);
  });


  if (total_size < (1024 * 1024 * 1024)) {
    main_layout.show_loader();
    $.ajax({
      url: schedule_form.url_save_file,
      type: 'POST',
      data: formData,
      processData: false,
      contentType: false,
      dataType: 'json',
      success: function (response) {
        main_layout.hide_loader();
        if (response.status == 200) {
          $('#formAddResources').modal('hide');
          showFile(response.data)
        }
        main_layout.alert_main(response.status == 200 ? 'Upload dữ liệu thành công' : response.message, response.status == 200 ? 'success' : 'error', 'center')
      }
    });
  } else {
    main_layout.alert_main('Tổng dung lượng file lớn hơn 1G');
  }
}

function showFile(data) {
  html_image = '';
  html_videos = '';
  html_file = ''

  html = $('#file_item_template').html();
  data.forEach(element => {
    if (element.content.length > 30) {
      const head = element.content.slice(0, 10);
      const tail = element.content.slice(-10);
      name = head + ' ... ' + tail;
    } else {
      name = element.content;
    }

    html_append = html.replaceAll('[id]', element.id)
      .replaceAll('id_-item', element.id)
      .replaceAll('[active]', shedulePage.resources.file_chose_id == element.id ? 'active-item' : '')
      .replaceAll('[name]', name);

    if (element.type == 1) {
      html_image += html_append;
    }
    if (element.type == 2) {
      html_videos += html_append;
    }
    if (element.type == 3) {
      html_file += html_append;
    }
  });

  $('#image_tab .list-resources').append(html_image);
  $('#video_tab .list-resources').append(html_videos);
  $('#file_tab .list-resources').append(html_file);
}

function getAllFiles() {
  $.ajax({
    url: schedule_form.url_get_file,
    type: 'GET',
    processData: false,
    contentType: false,
    dataType: 'json',
    success: function (response) {
      main_layout.hide_loader();
      if (response.status == 200) {
        $('#image_tab .list-resources').empty();
        $('#video_tab .list-resources').empty();
        $('#file_tab .list-resources').empty();
        showFile(response.data)
      }
    }
  });
}

function chose_resource_item(id) {
  $('.tab-content .custom-card').removeClass('active-item');
  $('#' + id + ' .custom-card').addClass('active-item');
  shedulePage.resources.file_chose_id = id;

  name_text = $('#' + id + ' .text-truncate').text();
  input = `<input type="hidden" value="` + id + `" name="resource_item">`;

  $('#chose_resource_item input').remove();
  $('#chose_resource_item').append(input);
  $('#chose_resource_item a').text(name_text);
}

function saveform() {
  main_layout.show_loader();
  form = document.getElementById('form_data_schedule');
  formData = new FormData(form);

  $.ajax({
    url: schedule_form.url_save_form,
    type: 'POST',
    data: formData,
    processData: false,
    contentType: false,
    dataType: 'json',
    success: function (response) {
      main_layout.hide_loader();
      main_layout.alert_main(response.message, response.status == 200 ? 'success' : 'error', 'center');

      if (response.status == 200) {
        setTimeout(() => {
          // location.reload();
          changeDate()
        }, 1000);
      }
    }
  });
}

function showModalMeetingDetail() {
  schedule_form.meetingDetail = [];
  $('#modalMeetingDetail').modal('show');
  $('#listMeetingDetail').empty();
  date = $('#form_data_schedule input[name=date]').val();
  $.ajax({
    url: schedule_form.getMeetingDetail,
    type: 'GET',
    data: {
      date: date,
      screen_id: screenPage.screen_id,
    },
    success: function (response) {
      if (response.status == 200) {
        html = $('#itemTemplateMeetingDetail').html();
        html_append = '';

        if (response.data.meeting_details.length > 0) {
          response.data.meeting_details.forEach(element => {
            html_append += html.replaceAll('[timer]', main.formatDate(element.start_time, false, true) + ' - ' + main.formatDate(element.end_time, false, true))
              .replaceAll('[name]', element.meeting.name)
              .replaceAll('[id]', element.id);
          });
          schedule_form.meetingDetail = response.data.meeting_details;
        } else {
          html_append = ' <center><p class="text-danger">Không có phiên họp nào trong ngày!</p></center>';
        }

        title = 'Danh sách phiên họp ' + response.data.conference_room.name + ' ngày ' + date;
        $('#modalMeetingDetail .modal-title').text(title);
        $('#listMeetingDetail').append(html_append);
      }
    }
  });
}

function confimMeetingDetail(status) {
  $('#modalMeetingDetail').modal('hide');
  $('#form_data_schedule input[name=meeting_detail_id]').remove();
  if (status) {
    const selectedValue = document.querySelector('input[name="flexRadioDefault"]:checked')?.value;

    html = `<input type="hidden" name="meeting_detail_id" value="` + selectedValue + `"></input>`;

    schedule_form.meetingDetail.forEach(element => {
      if (element.id == selectedValue) {
        data = element;
      }
    });

    checkin = main.formatDate(data.checkin, false, true);
    start_time = main.formatDate(data.start_time, false, true);
    end_time = main.formatDate(data.end_time, false, true);

    $('#form_data_schedule input[name=start_time]').val(checkin);
    $('#form_data_schedule input[name=end_time]').val(end_time);

    $('#form_data_schedule input[name=start_time]').attr('disabled', true);
    $('#form_data_schedule input[name=end_time]').attr('disabled', true);


    $('#chose_meeting_detail').append(html);
    $('#chose_meeting_detail a').text(start_time + ' - ' + end_time + ': ' + data.meeting.name);
    $('#chose_meeting_detail').removeClass('d-none');
    $('#more_option').removeClass('hide_div');
    $('#more_option .form-check-input').attr('disabled', false);

    $('#flexRadioDefault3').parent().removeClass('d-none');
  } else {
    $('#form_data_schedule input[name=start_time]').attr('disabled', false);
    $('#form_data_schedule input[name=end_time]').attr('disabled', false);
    $('#chose_meeting_detail').addClass('d-none');

    $('#more_option').addClass('hide_div');
    $('#more_option .form-check-input').attr('disabled', true);
    
    $('#flexRadioDefault3').parent().addClass('d-none');
  }
}

function changeDate() {
  const new_date = $('#form-input-date').val()

  $('#form_data_schedule input[name=date]').val(new_date)
  $('#date-view').text(new_date)
  getListSchedule(new_date)
  // console.log(new_date);

}

function viewResource(id) {
  $.ajax({
    url: screenPage.url_getDetailResource + '/' + id,
    type: 'get',
    dataType: 'json',
    success: function (response) {
      console.log(response);

    }
  });
}
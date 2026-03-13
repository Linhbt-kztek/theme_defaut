$(document).ready(function () {
  $('#createFormModal select[name=agencies_id]').multiselect({
    includeSelectAllOption: true,
    enableFiltering: true,
    buttonContainer: '<div class="btn-group input-group"/>',
    buttonClass: 'form-control',
    enableCaseInsensitiveFiltering: true,
  })
})

function showFormModal(id = '') {
  {
    $('#createStaffPreviewAvatar').attr('src', '/images/default-user.jpg')
    $('#name').val('')
    $('#code').val('')
    $('#user_name').val('')
    $('#user_name').attr('disabled', false)
    $('#createFormModal select[name=agencies_id]').val('')
    $('#createFormModal select[name=role]').val('')
    $('#phone').val('')
    $('#email').val('')
    $('#birthday').val('')
    $('#address').val('')

    $('#identification').val('')
    $('#position').val('')

    $('#idFormStaff input[name=id]').remove()
    $('#createModalLabel').text('Thêm mới thành viên')
  }

  if (id != '') {
    url = 'staff/show' + '/' + id
    $.ajax({
      url: url,
      type: 'GET',
      success: function (response) {
        $('#createStaffPreviewAvatar').attr('src', response.data.url_image)
        $('#name').val(response.data.name)
        $('#code').val(response.data.code)
        $('#user_name').val(response.data.user.user_name)
        $('#user_name').attr('disabled', true)

        birthday =
          response.data.year_of_birth +
          '-' +
          (response.data.month_of_birth < 10
            ? '0' + response.data.month_of_birth
            : response.data.month_of_birth) +
          '-' +
          (response.data.day_of_birth < 10
            ? '0' + response.data.day_of_birth
            : response.data.day_of_birth)

        $('#createFormModal select[name=agencies_id]').val(response.data.agencies_id)
        $('#createFormModal select[name=role]').val(response.data.user.role_id)
        $('#phone').val(response.data.phone)
        $('#email').val(response.data.email)
        $('#birthday').val(birthday)
        $('#address').val(response.data.address)

        $('#identification').val(response.data.identification)
        $('#position').val(response.data.position)

        $('#createFormModal select[name=agencies_id]').multiselect('refresh')

        inputId = `<input type="hidden" name="id" value="` + id + `">`
        $('#idFormStaff').append(inputId)
      },
    })
    $('#createModalLabel').text('Cập nhật thông tin thành viên')
  }

  $('#createFormModal').modal('show')
}

function previewImageAvatar(event) {
  var reader = new FileReader()
  reader.onload = function () {
    $('#createStaffPreviewAvatar').attr('src', reader.result)
  }
  profile_image = event.target.files[0]
  reader.readAsDataURL(event.target.files[0])
}

function saveData() {
  main_layout.show_loader()
  const form = document.getElementById('idFormStaff')
  const data = new FormData(form)
  const url = staff_index.url_save_data
  const staff_email = $('#email').val()
  if (!staff_email || staff_email == null || staff_email == '') {
    main_layout.alert_main('Vui lòng nhập địa chỉ Email', 'error', 'center')
    main_layout.hide_loader()
    return
  }

  $.ajax({
    url: url,
    type: 'POST',
    data: data,
    processData: false,
    contentType: false,
    success: function (response) {
      main_layout.hide_loader()
      main_layout.alert_main(
        response.message,
        response.status == 200 ? 'success' : 'error',
        'center'
      )
      if (response.status == 200) {
        $('#createFormModal').modal('hide')
        setTimeout(() => location.reload(), 1000)
      }
    },
  })
}

function approve(id, status) {
  if (status) {
    $('#approve_name').text('')
    $('#approve_code').text('')
    $('#approve_agency').text('')
    $('#approve_phone').text('')
    $('#approve_identification').text('')
    $('#approve_position').text('')
    $('#approve_email').text('')
    $('#approve_address').text('')
    $('#approve_user_name').val('')
    $('#approve_staff_id').val('')

    url = 'staff/show' + '/' + id
    $.ajax({
      url: url,
      type: 'GET',
      success: function (response) {
        $('#approve_name').text(response.data.name)
        $('#approve_code').text(response.data.code)
        $('#approve_agency').text(response.data.agency.name)
        $('#approve_phone').text(response.data.phone)
        $('#approve_identification').text(response.data.identification)
        $('#approve_position').text(response.data.position)
        $('#approve_email').text(response.data.email)
        $('#approve_address').text(response.data.address)

        $('#approve_staff_id').val(response.data.id)
      },
    })

    $('#modalApprove').modal('show')
  } else {
    Swal.fire({
      title: '',
      text: 'Bạn có chắc chắn ' + (status ? 'muốn phê duyệt' : 'không muốn phê duyệt') + '?',
      type: 'warning',
      showCancelButton: true,
      confirmButtonColor: '#d33',
      cancelButtonColor: '#3085d6',
      confirmButtonText: 'Có',
      cancelButtonText: 'Không',
    }).then(result => {
      if (result.value) {
        data = {
          _token: main.token,
          id: id,
          status: status,
        }

        main_layout.show_loader()
        approveAction(data, status)
      }
    })
  }
}

function confirmApprove() {
  $('#approve_allert_form').addClass('d-none')
  user_name = $('#approve_user_name').val()

  if (user_name != '' && user_name != undefined) {
    id = $('#approve_staff_id').val()
    data = {
      _token: main.token,
      id: id,
      user_name: user_name,
      status: 1,
    }
    main_layout.show_loader()
    approveAction(data, 1)
  } else {
    $('#approve_allert_form').removeClass('d-none')
  }
}

function approveAction(data, status) {
  $.ajax({
    url: staff_index.url_approve,
    type: 'post',
    data: data,
    success: function (result) {
      main_layout.hide_loader()
      main_layout.alert_main(result.message, result.status ? 'success' : 'error', 'center')
      if (status) {
        if (result.status) {
          $('#modalApprove').modal('hide')
          setTimeout(() => {
            location.reload()
          }, 1000)
        }
      } else {
        setTimeout(() => {
          location.reload()
        }, 1000)
      }
    },
  })
}
function handleResizePage(perSize) {
  let key_search = $('#key_search').val()
  let is_approve = $('select[name="is_approve"]').val()
  let search_confim = $('#search_confim').val()

  let baseUrl = $('#pageLimit').data('url')

  let url =
    baseUrl +
    '?key_search=' +
    encodeURIComponent(key_search) +
    '&is_approve=' +
    is_approve +
    '&search_confim=' +
    search_confim +
    '&limit=' +
    perSize

  window.location.href = url
}

window.showDeleteModal = function (id) {
  $('#deleteStaffModal').modal('show')
  $('#deleteStaffModal #staff_id').val(id)
}

window.handleDeleteStaff = function () {
  let staff_id = $('#deleteStaffModal #staff_id').val()
  let url = 'staff/delete/' + staff_id
  main_layout.show_loader()
  $.ajax({
    url: url,
    type: 'DELETE',
    headers: headersClient,
    data: {
      _token: main.token,
    },
    success: function (result) {
      $('#deleteStaffModal').modal('hide')

      if (result['status'] == 200) {
        $('#' + staff_id).remove();
        updateRowIndex('#list_staff');
        main_layout.hide_loader()
        Swal.fire({
          text: 'Xóa thành công !',
          icon: 'success',
          timer: 1500,
        })
      } else {
        Swal.fire({
          text: result['error'],
          icon: 'error',
          timer: 1500,
        })
      }
    },
    Error: function (result) {
      main_layout.hide_loader()
      Swal.fire({
        text: 'Xóa không thành công!',
        icon: 'error',
        timer: 1500,
      })
    },
  })
}

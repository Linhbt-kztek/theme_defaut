$(document).ready(function () {
    $('.submitDeleteForm').on('click', function (e) {
        e.preventDefault();
        var form = $(this).parents().children('form');
        Swal.fire({
            title: '',
            text: "Bạn có chắc chắn muốn xoá không ?",
            type: 'warning',
            showCancelButton: true,
            confirmButtonColor: '#d33',
            cancelButtonColor: '#3085d6',
            confirmButtonText: 'Có',
            cancelButtonText: 'Không'
        }).then((result) => {
            if (result.value) {
                form.submit();
            }
        });
    });
});

function showModalList() {
    $('#myModal').modal('show');
    $.ajax({
        url: ticketType.url_category_getListData,
        type: 'GET',
        success: function (result) {
            main_layout.hide_loader();
            if (result.status) {
                html = $('#html_tr').html();
                html_append = '';
                result.data.forEach(category => {
                    html_append += html.replaceAll('[name]', category.name)
                        .replaceAll('[id]', category.id)
                        .replaceAll('[count]', category.ticket_types.length)
                        .replaceAll('[time]', main.formatDate(category.created_at));
                });

                $('#tbody_category_list').empty();
                $('#tbody_category_list').append(html_append);
                main_layout.setIndex('#tbody_category_list tr')

            } else {
                main_layout.alert_main(result.message, 'error');
                setTimeout(() => {
                    location.reload;
                }, 1500);
            }
        }
    })
}

function createItemCategory() {
    html = $('#html_tr_input').html();
    html_append = html.replaceAll('[id]', main.timestamp());
    $('#tbody_category_list').append(html_append);
    main_layout.setIndex('#tbody_category_list tr')
}

function saveItemCategory(key, id) {
    name_category = $('input[name=name_category]').val();

    if (name_category != '' && name_category != undefined) {
        $.ajax({
            url: ticketType.url_category_save,
            type: 'POST',
            data: {
                _token: main.token,
                name: name_category,
                id: id
            },
            success: function (result) {
                main_layout.hide_loader();
                if (result.status) {
                    showModalList();
                } else {
                    main_layout.alert_main(result.message, 'error');
                    setTimeout(() => {
                        location.reload;
                    }, 1500);
                }
            }
        })
    }
}

function deleteitemCategory(key, id = '') {
    if (id != '') {
        main_layout.show_loader();
        $.ajax({
            url: ticketType.url_category_delete,
            type: 'POST',
            data: {
                _token: main.token,
                id: id
            },
            success: function (result) {
                main_layout.hide_loader();
                if (result.status) {
                    $('#' + key).remove();
                    main_layout.setIndex('#tbody_category_list tr');
                } else {
                    main_layout.alert_main(result.message, 'error');
                    setTimeout(() => {
                        location.reload();
                    }, 1500);
                }
            }
        })
    } else {
        $('#' + key).remove();
    }

}

function editItemCategory(key, id) {
    const col = document.querySelectorAll('#' + key + ' td');
    col.forEach((row, index) => {
        if (index == 1) {
            name = row.textContent;
        }
    });

    html_tr_input_child = $('#html_tr_input').html();
    html_append = html_tr_input_child
        .replaceAll('<tr id="category_item_[id]">', '')
        .replaceAll('[id]', id)
        .replaceAll('</tr>', '');

    $('#' + key).empty();
    $('#' + key).append(html_append);
    $('input[name=name_category]').val(name);
    main_layout.setIndex('#tbody_category_list tr');
}
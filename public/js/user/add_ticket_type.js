$(document).ready(function () {
    showTicketType();
    appendDataChosedItem();
    $('#area_id_select').multiselect({
        includeSelectAllOption: true,
        enableFiltering: true,
        buttonContainer: '<div class="btn-group input-group">',
        enableCaseInsensitiveFiltering: true,
    });
});

let addTicketType = {
    chosedItem: function (data) {
        html = $('#html_tr_item').html()
            .replaceAll('[id]', data.id)
            .replaceAll('[name]', data.name.length > 55 ? data.name.substring(0, 55) + ' ...' : data.name)
            .replaceAll('[price]', main_layout.formattedNumber(parseInt(data.price_offline)) + 'đ');
        $('#chosed_item').append(html);
        main_layout.setIndex('#chosed_item tr');
    }
}

function showTicketType() {
    company_id = $('#area_id_select').val();

    html = `<div class="col-4 btn btn-item" id="div_item_[id_ticket_type]">
                <div class="card">
                    <div class="card-header card_header_item" style="background-color: #f3f3f3"
                        onclick="choseTicketType('[id_ticket_type]')">
                        [name_ticket_type]
                    </div>
                    <div class="card-body detail_item" onclick="findDataInModal('[id_ticket_type]')">
                        <a href="#"><i class="ri-eye-line label-icon align-middle"></i> Xem chi tiết</a>
                    </div>
                </div>
            </div>`;

    html_append = '';
    doomPageData.ticket_type.forEach(element => {
        if (company_id != '' && company_id != undefined) {
            check = false;
            element.areas.forEach(area => {
                if (company_id == area.type_id) {
                    check = true;
                }
            });
        } else {
            check = !(doomPageData.list_chosed.includes(element.id))
        }

        if (check) {
            html_append += html.replaceAll('[id_ticket_type]', element.id)
                .replaceAll('[name_ticket_type]', element.name);
        }
    });

    $('#list_ticket').empty();
    $('#list_ticket').append(html_append);
    searchAndHide();
}

function searchAndHide() {
    const searchTerm = document.getElementById('searchText').value.toLowerCase()
    const headerItems = document.querySelectorAll('.card_header_item')
    const col6Wrapper = document.querySelector('.btn-item')

    let found = false
    let count = 0;
    headerItems.forEach(item => {
        const itemText = item.textContent.toLowerCase()
        btnItemParent = item.closest('.btn-item')

        if (itemText.includes(searchTerm)) {
            found = true
            btnItemParent.classList.remove('d-none')
            count++;
        } else {
            btnItemParent.classList.add('d-none')
        }
    })
    $('#count_ticket').text(count + '/' + doomPageData.ticket_type.length);
}

function choseTicketType(id) {
    data = doomPageData.ticket_type.find(item => item.id === id);
    if (data == undefined) {
        main_layout.alert_main('Xảy ra lỗi vui lòng thử lại!', 'error', 'center');
        setTimeout(() => {
            location.reload();
        }, 1000);
    } else {
        addTicketType.chosedItem(data);
        doomPageData.list_chosed.push(id);
        showTicketType()
    }
}

function removeChosed(id) {
    doomPageData.list_chosed = doomPageData.list_chosed.filter(item => item !== id);
    $('#chosed_item_' + id).remove();
    main_layout.setIndex('#chosed_item tr')
    showTicketType()
}

function appendDataChosedItem() {
    doomPageData.list_chosed.forEach(element => {
        data = doomPageData.ticket_type.find(item => item.id === element);
        if (data != undefined) {
            addTicketType.chosedItem(data);
        }
    });
}

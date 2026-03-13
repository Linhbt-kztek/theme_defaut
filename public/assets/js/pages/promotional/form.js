$(document).ready(function () {
    changeConditionType()
    changeType()
})

function submit() {
    has_error = false;

    const expiry_date = $('input[name=expiry_date]').val();

    if (expiry_date && expiry_date.includes(' đến ')) {
        const parts = expiry_date.split(' đến ');
        if (parts.length === 2) {
            const expiry_date_form = parts[0].trim();
            const expiry_date_to = parts[1].trim();

            const startDateTime = main.parseDate(expiry_date_form);
            const endDateTime = main.parseDate(expiry_date_to);

            if (startDateTime && endDateTime && !(startDateTime < endDateTime)) {
                has_error = true;
                message = 'Thời gian áp dụng chưa hợp lý vui lòng kiểm tra lại!';
            }
        } else {
            // Không đúng định dạng, có thể cảnh báo hoặc bỏ qua
            has_error = true;
            message = 'Vui lòng nhập đúng định dạng ngày tháng!';
        }
    }

    if (!has_error) {
        condition_type = $('select[name=condition_type]').val();
        condition_value = $('input[name=condition_value]').val();

        if ((condition_type != '' && condition_type != undefined && condition_value != '' && condition_value != undefined)) {
            if (condition_type == 1 && parseInt(condition_value) < 1000) {
                has_error = true;
                message = 'Điều kiện áp dụng: giá trị tối thiểu là 1000 VNĐ!';
            }
        } else {
            has_error = true;
            message = 'Điều kiện áp dụng: không được để trống!';
        }
    }

    if (!has_error) {
        type = $('select[name=type]').val();
        rate = $('input[name=rate]').val();

        if ((type != '' && type != undefined && rate != '' && rate != undefined)) {
            if (type == 1 && parseInt(rate) > 100) {
                has_error = true;
                message = 'Tùy chỉnh giảm giá: giảm % đơn hàng không được vượt quá 100%!';
            }
            if (type == 2 && parseInt(rate) < 1000) {
                has_error = true;
                message = 'Tùy chỉnh giảm giá: giảm giá đơn hàng tối thiểu là 1000 VNĐ!!';
            }
        } else {
            has_error = true;
            message = 'Tùy chỉnh giảm giá không được để trống!';
        }
    }

    if (!has_error) {
        promotional_name = $('input[name=name]').val();
        if (promotional_name == '' || promotional_name == undefined) {
            has_error = true;
            message = 'Tên chương trình khuyến mãi không được để trống!';
        }
    }

    if (has_error) {
        main_layout.alert_main(message, 'error');
    } else {
        // form_data = $('#form_submit').serializeArray();
        $('#form_submit').submit();
    }
}

function changeConditionType() {
    condition_type = $('select[name=condition_type]').val();
    text = condition_type == 1 ? 'VNĐ' : 'Vé';
    $('#condition_type_text').text(text);
}

function changeType() {
    type = $('select[name=type]').val();
    text = type == 2 ? 'VNĐ' : '%';
    $('#type_text').text(text);
}
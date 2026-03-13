function showHistoryAction(card_id) {
    main_layout.show_loader()
    $.ajax({
        url: cardIndexDoom.getAllEventWithCardUrl,
        type: 'GET',
        data: {
            card_id: card_id
        },
        success: function (result) {
            main_layout.hide_loader()
            $('#showCardHistoryLabel').text('Lịch sử hoạt động')
            if (result.status == 200) {
                // Chuyển object result.data thành mảng
                const dataArray = Object.values(result.data);

                // Sắp xếp mảng theo thuộc tính 'time' giảm dần
                dataArray.sort((a, b) => new Date(b.time) - new Date(a.time));

                html_template = $('#cardHistoryActionTemplate').html();

                html_append = '';
                for (const element of dataArray) {
                    const content = 'Khách hàng ' + (element.is_event_in ? 'vào' : 'ra') + ' tại ' + element.area;
                    html_append += html_template
                        .replaceAll('[content]', content)
                        .replaceAll('[image]', element.image ?? '/images/noimage.jpg')
                        .replaceAll('[action]', element.is_event_in ? 'Sự kiện vào' : 'Sự kiện ra')
                        .replaceAll('[action_class]', element.is_event_in ? 'primary' : 'success')
                        .replaceAll('[time]', main.formatDateTime(element.time));
                }

                $('#history-items-container').empty().append(html_append);
                $('#showCardHistoryModal').modal('show');
            }
        },
    })
}
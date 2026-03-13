function createLayout() {
    layout.width = $('#with_layout').val();
    layout.height = $('#height_layout').val();

    $('#with_layout').addClass('d-none');
    $('#height_layout').addClass('d-none');
    $('#createLayoutBtn').addClass('d-none');
    $('#order_divison b').text('Điền thông số thứ tự ứng với màu sắc màn hình (Số thứ tự càng nhỏ sẽ được chèn lên trên cùng)!');

    var html_append = layout.html
        .replaceAll('[width]', layout.width / layout.ratio + 2)
        .replaceAll('[height]', layout.height / layout.ratio + 2)
        .replaceAll('[count]', layout.count);
    $('#layout_div').append(html_append);
    $('#deleteLayout').removeClass('d-none');
    $('#div_layout_default').removeClass('d-none');

    doom.callBack();
}

function createDivision() {
    var color = doom.getRandomColor();

    layout.count = layout.count + 1;
    var html_append = division.html
        .replaceAll('[color]', color)
        .replaceAll('[count]', layout.count);

    $('#parent').append(html_append);
    doom.callBack();

    $('#order_divison').append(division.html_order_division
        .replaceAll('[division_id]', layout.count)
        .replaceAll('[color]', color));


}

function saveLayout(key) {
    if (key == 'create') {
        doom.url = form.url_layout_create;

        doom.data = {
            _token: main.token,
            company_id: $('#company_id').val(),
            witht_layout: layout.width,
            height_layout: layout.height,
            division: division.divisionLocal()
        };

        doom.type = 1;
    } else {
        doom.url = form.url_layout_update;

        doom.data = {
            _token: main.token,
            id: $('#id_layout_edits').val(),
            company_id: $('#company_id').val(),
            witht_layout: layout.width,
            height_layout: layout.height,
            division: division.divisionLocal()
        };

        doom.type = 10;
    }

    doom.method = 'POST';

    doom.callApi();
}

function selectCompany() {

    var company_id = $('#company_id').val();
    if (company_id != null) {
        $('#card_body').removeClass('d-none');
        doom.url = form.urlGetLayoutByCompanyId(company_id);
        doom.method = 'GET';
        doom.data = {
            _token: main.token,
        };
        doom.type = 2;
        doom.callApi();

        setTimeout(() => {
            doom.url = form.urlGetCategoryByCompanyId;
            doom.method = 'post';
            doom.data = {
                _token: main.token,
                company_id: company_id,
                type: 2,
                with: 'resources'
            };
            doom.type = 5;
            doom.callApi();


        }, 500);


    }
}

function deleteLayouts(layout_id) {
    doom.url = form.urlDeleteLayout(layout_id);
    doom.method = 'delete';
    doom.data = {
        _token: main.token,
    };
    doom.type = 3;
    doom.callApi();
}

function choseLayout(layout_id, type = 4) {
    if (type == 4) {
        doom.changLayoutButton(layout_id);
        layout.id = layout_id;
    }
    doom.type = type;
    doom.url = form.urlGetLayoutById(layout_id);
    doom.method = 'get';
    doom.data = {
        _token: main.token,
    };

    doom.callApi();
}

function showFormCreateLayout() {

    $('#exampleModalPopovers').remove();
    $('#this_modal').append(doom.html_modal.replaceAll('[url]', 'create'));
    $('#exampleModalPopovers').modal('show');

}

function showFormEditLayout(data) {
    var input_id = `<input type="hidden" id="id_layout_edits" value="[layout_id]" name="layout_id">`;
    $('#exampleModalPopovers').remove();
    $('#this_modal').append(doom.html_modal.replaceAll('[url]', 'update'));
    $('#exampleModalPopovers').append(input_id.replaceAll('[layout_id]', data.id));

    document.getElementById("with_layout").value = data.width;
    document.getElementById("height_layout").value = data.height;
    createLayout();

    setTimeout(() => {
        data.division.forEach(element => {
            var color = doom.getRandomColor();
            layout.count = layout.count + 1;
            var html_append = division.html
                .replaceAll('top: 10px', 'top: ' + element.x / layout.ratio + 'px')
                .replaceAll('left: 10px', 'left: ' + element.y / layout.ratio + 'px')
                .replaceAll('width: 100px', 'width: ' + element.width / layout.ratio + 'px')
                .replaceAll('height: 100px', 'height: ' + element.height / layout.ratio + 'px')

                .replaceAll('[color]', color)
                .replaceAll('[count]', layout.count);

            $('#parent').append(html_append);

            $('#order_divison').append(division.html_order_division
                .replaceAll('[division_id]', layout.count)
                .replaceAll('[color]', color));
            document.getElementById("order_division_" + layout.count).value = element.order;
            doom.callBack();
        });
    }, 200);


    $('#createLayoutModal').remove();
    $('#deleteLayout').remove();
    $('#exampleModalPopovers').modal('show');

    setTimeout(() => {
        for (let index = 1; index < layout.count; index++) {
            orderDivision(index)
        }
    }, 200);
}

function afterChoseLayout(data) {
    var html_layout = '';
    var html_division = '';
    var width = '';
    var height = '';
    var html_append = '';
    var division_list_id = [];

    $('.row_timeline_layout').remove();
    $('#timeline_list_division').removeClass('d-none');

    html_layout = layout.timeline_html
        .replaceAll('[layout_id]', data.id)
        .replaceAll('[width]', data.width / doom.timeline_radio)
        .replaceAll('[height]', data.height / doom.timeline_radio);

    data.division.forEach(item => {
        html_append = html_layout.replaceAll('[division_id]', item.id);
        $('#timeline_list_division').append(html_append);

        width = item.width / doom.timeline_radio;
        height = item.height / doom.timeline_radio;
        x = item.x / doom.timeline_radio;
        y = item.y / doom.timeline_radio;

        html_division = division.item_html
            .replaceAll('[x]', x)
            .replaceAll('[y]', y)
            .replaceAll('[width]', width)
            .replaceAll('[height]', height)
            .replaceAll('[color]', doom.getRandomColor());

        $('#timeline_division_' + item.id).append(html_division);
        division_list_id.push(item.id)
    });
    setTimeout(function () {
        doom.DragAndDropLibrary(division_list_id);
    }, 800);


}

function selectCategoryResources(data) {
    var list_category_id = [];
    $('#tablist_category_div').removeClass('d-none');
    $('#tablist_category .nav-item').remove();
    $('#tabcontent_category div').remove();
    var countResource = 0;
    try {
        data.forEach(cate => {
            var html_tab = '';
            var html_tab_pane = '';
            list_category_id.push(cate.id);

            html_tab = resources.category_html
                .replaceAll('[category_name]', cate.name)
                .replaceAll('[category_id]', cate.id);


            $('#tablist_category').append(html_tab);

            html_tab_pane = resources.category_tab_pane
                .replaceAll('[category_name]', cate.name)
                .replaceAll('[category_id]', cate.id);


            $('#tabcontent_category').append(html_tab_pane);

            if (cate.resources.length > 0) {
                cate.resources.forEach(resource => {
                    for (let index = 1; index < 4; index++) {
                        if (resource.type == index) {
                            var html_resource_item = '';
                            html_resource_item = resources.resource_html
                                .replaceAll('[color]', doom.getRandomColor())
                                .replaceAll('[resource_name]', resource.name)
                                .replaceAll('[resource_id]', resource.id)
                            $('#resources_' + cate.id + '_type_' + resource.type).append(html_resource_item);
                            countResource = countResource + 1;
                        }
                    }
                });
            }
        });
    } catch (error) {
        setTimeout(() => {
            console.log(error);
            choseLayout(parseInt(form.layout_id));
        }, 500);
    }

    resources.count = countResource;
    doom.category_list = list_category_id;

}

function deleteAllResources(division_id) {
    $('#ul_division_id_' + division_id + ' .item_resources').remove();
}

function showModalOptions(key) {

    $(".modal-sm .modal-footer .btn-primary").attr('onclick', "modalResources(" + key + ",'edit')");
    $(".modal-sm .modal-footer .btn-danger").attr('onclick', "modalResources(" + key + ",'delete')");
    $('#bs-example-modal-sm').modal('show');
}

function modalResources(key, action) {
    $('#bs-example-modal-sm').modal('hide');
    if (action == 'delete') {
        $("#div_" + key).remove();
    }

    if (action == 'edit') {
        var id = document.getElementById(key).getAttribute('data_id');

        doom.url = form.urlGetResourcesById(id);
        doom.method = 'get';
        doom.data = {
            _token: main.token,
        };
        doom.type = 6;
        doom.callApi();

        resources.key = key;

        setTimeout(function () {

            var p_title = '';
            var html = '';

            // var data_array = document.querySelectorAll('#div_' + key + ' input');
            var div_id = document.getElementById('div_' + key);
            var data_array = div_id.getElementsByTagName('input');
            var valArray = [];

            data_array.forEach(inputElement => {
                valArray.push({
                    name: inputElement.getAttribute('name'),
                    value: $('#div_' + key + ' #' + inputElement.getAttribute('name')).val()
                    // value: $('#' + inputElement.getAttribute('id')).val()
                });

            });

            switch (resources.data.type) {
                case 1:
                    p_title = 'Tài liệu dạng text';
                    html = resources.input_type_1;

                    break;
                case 2:
                    p_title = 'Tài liệu hình ảnh';
                    html = resources.input_type_2;
                    // $('#input_type').remove();
                    break;
                case 3:
                    p_title = 'Tài liệu videos';
                    html = resources.input_type_3;
                    // $('#input_type').remove();
                    break;
                default:
                    console.log('không có gì cả');
                    break;
            }
            html = html.replaceAll('[id]', resources.data.id)

            valArray.forEach(element => {
                if (element.name == 'effect') {
                    html = html.replaceAll('value="' + element.value + '"', 'value="' + element.value + '" selected')
                }
                if (element.name == 'run_time') {
                    html = html.replaceAll('name="run_time"', 'name="run_time" value="' + element.value + '"')
                }
            });

            $('#input_type').remove();


            $('#title_header h5').text(resources.data.name)
            $('#title_header p').text(p_title)
            $('#input_options').append(html);

            $('#exampleModalgrid').modal('show');
        },
            1000)

    }
}

function previewItem() {
    var myForm = document.getElementById("form_data_modal");
    var formData = new FormData(myForm);

    switch (resources.data.type) {
        case 1:
            var html = `<div id="marquee_preview_item" style="background-color:black; color: #fff; height: 70px;">
                                <marquee direction="[direction_value]">
                                    <h2>[Content]</h2>
                                </marquee>
                            </div>`;

            html = html.replaceAll('[Content]', resources.data.content);

            for (var [key, value] of formData.entries()) {
                if (key = 'effect') {
                    html = html.replaceAll('[direction_value]', value);
                }
            }
            $('#marquee_preview_item').remove();
            $('#input_type').append(html);

            break;
        case 2:

            var image = form.urlResources(resources.data.content);

            var html = `<img src="` + image + `" alt="">`;

            // html = html.replaceAll('[Content]', resources.data.content);
            $('#marquee_preview_item').remove();
            $('#input_type').append(html);
            break;
        default:
            var videos = form.urlResources(resources.data.content);

            var html = `<video controls autoplay>
                            <source src="` + videos + `" type="video/mp4">
                        </video>`;

            $('#marquee_preview_item').remove();
            $('#input_type').append(html);
            break;
    }
}

function getFormValues() {

    var myForm = document.getElementById("form_data_modal");
    var formData = new FormData(myForm);
    var html = `<input type="hidden" id="[name]" name="[name]" value="[value]">`;
    var htmls = '';

    for (var [key, value] of formData.entries()) {
        htmls = htmls + html.replaceAll('[name]', key).replaceAll('[value]', value);
        $('#div_' + resources.key + ' #' + key).remove();
    }

    $('#div_' + resources.key).append(htmls);
    $('#exampleModalgrid').modal('hide');
}

function getAllValue() {
    try {

        var list_division = layout.data.division;

        var list_input_value = [];
        var list_div_value = [];
        var list_division_value = [];

        list_division.forEach(division => {
            var list_division_items_id = document.querySelectorAll('#ul_division_id_' + division.id + ' div');

            list_div_value = [];
            list_division_items_id.forEach((div) => {
                var list_items_id = document.querySelectorAll('#' + div.id + ' input');
                list_input_value = [];

                list_items_id.forEach((input) => {

                    list_input_value.push({
                        [input.name]: input.value
                    });
                });
                list_div_value.push({
                    [div.id]: list_input_value
                });
            });
            list_division_value.push({
                [division.id]: list_div_value
            });
        });

        return list_division_value;
    } catch (error) {
        Swal.fire({
            position: 'top-end',
            icon: 'error',
            title: "Vui lòng chọn layout!",
            showConfirmButton: false,
            timer: 2000,
            showCloseButton: false
        });
    }
}

function submitForm(text) {
    doom.text_type_action = text;
    var allVal = getAllValue();
    var name = $('.mb-3 input').val();
    var company_id = document.getElementById("company_id").value;

    doom.url = form.urlSubmitForm();
    doom.method = 'post';
    if (text == 'create') {
        doom.data = {
            _token: main.token,
            name: name,
            layout_id: layout.id,
            company_id: company_id,
            data_division: allVal
        };
    } else {
        doom.data = {
            _token: main.token,
            id: $('#data_id_campain').val(),
            name: name,
            layout_id: layout.id,
            company_id: company_id,
            data_division: allVal
        };
    }

    doom.type = 7;
    doom.nextPage = true;
    doom.callApi();
}

function callDataResourcesWhenEdits() {
    var html = `<div data-post-id="1" data-test="1" class="item_resources ui-draggable ui-draggable-handle" style="width: 120px; right: auto; height: 39px; bottom: auto;" id="div_[timestamp]">
                    <a type="button" class="event-date btn" data_id="[resource_id]" style="width: 120px;white-space: normal;background-color: [color]; color: #fff;" id="[timestamp]" ondblclick="showModalOptions([timestamp])">[name]</a>
                `;
    var html_input = `<input type="hidden" id="[name]" name="[name]" value="[value]"></input>`;


    data = form.dataReresources;
    data.forEach(element => {
        var timestamp = main.timestamp() + element.id;
        var html_inputs = '';

        html_append = html.replaceAll('[timestamp]', timestamp)
            .replaceAll('[resource_id]', element.resource_id)
            .replaceAll('[color]', doom.getRandomColor())
            .replaceAll('[name]', element.resource_name);

        html_inputs = html_inputs + html_input.replaceAll('[name]', 'order').replaceAll('[value]', element.order);
        html_inputs = html_inputs + html_input.replaceAll('[name]', 'id').replaceAll('[value]', element.resource_id);

        for (let key in element.data_input) {
            if (key == 'duration') {
                html_inputs = html_inputs + html_input.replaceAll('[name]', 'run_time').replaceAll('[value]', element.data_input[key]);
            } else {
                html_inputs = html_inputs + html_input.replaceAll('[name]', key).replaceAll('[value]', element.data_input[key]);

            }
        }

        html_append = html_append + html_inputs + '</div>';
        document.getElementById('ul_division_id_' + element.division_id).innerHTML += html_append;
    });
}

function addDivision(key) {
    var width = $('#with_layout').val();
    var height = $('#height_layout').val();
    var html_append = '';
    var division_append = '';

    switch (key) {
        case 1:
            // 1:1
            var data = {
                division1: {
                    width: width / layout.ratio / 2,
                    height: height / layout.ratio,
                    top: 0,
                    left: 0
                },
                division2: {
                    width: width / layout.ratio / 2,
                    height: height / layout.ratio,
                    top: 0,
                    left: width / layout.ratio / 2
                }
            };
            break;
        case 2:
            var data = {
                division1: {
                    width: width / layout.ratio / 2,
                    height: height / layout.ratio,
                    top: 0,
                    left: 0
                },
                division2: {
                    width: width / layout.ratio / 2,
                    height: height / layout.ratio / 2,
                    top: 0,
                    left: width / layout.ratio / 2
                },
                division3: {
                    width: width / layout.ratio / 2,
                    height: height / layout.ratio / 2,
                    top: height / layout.ratio / 2,
                    left: width / layout.ratio / 2
                },
            };
            break;
        case 3:
            var data = {
                division1: {
                    width: width / layout.ratio / 2,
                    height: height / layout.ratio,
                    top: 0,
                    left: width / layout.ratio / 2
                },
                division2: {
                    width: width / layout.ratio / 2,
                    height: height / layout.ratio / 2,
                    top: 0,
                    left: 0
                },
                division3: {
                    width: width / layout.ratio / 2,
                    height: height / layout.ratio / 2,
                    top: height / layout.ratio / 2,
                    left: 0
                }
            };
            break;
        case 4:
            var data = {
                division2: {
                    width: width / layout.ratio / 2,
                    height: height / layout.ratio - height / layout.ratio / 10,
                    top: 0,
                    left: 0
                },
                division3: {
                    width: width / layout.ratio / 2,
                    height: height / layout.ratio - height / layout.ratio / 10,
                    top: 0,
                    left: width / layout.ratio / 2
                },
                division1: {
                    width: width / layout.ratio,
                    height: height / layout.ratio / 10,
                    top: height / layout.ratio - height / layout.ratio / 10,
                    left: 0
                },
            };
            break;
        case 5:
            var data = {
                division2: {
                    width: width / layout.ratio / 2,
                    height: height / layout.ratio - height / layout.ratio / 10,
                    top: height / layout.ratio / 10,
                    left: 0
                },
                division3: {
                    width: width / layout.ratio / 2,
                    height: height / layout.ratio - height / layout.ratio / 10,
                    top: height / layout.ratio / 10,
                    left: width / layout.ratio / 2
                },
                division1: {
                    width: width / layout.ratio,
                    height: height / layout.ratio / 10,
                    top: 0,
                    left: 0
                },
            };
            break;
        case 6:
            var data = {
                division1: {
                    width: width / layout.ratio / 2,
                    height: height / layout.ratio,
                    top: 0,
                    left: 0
                },
                division2: {
                    width: width / layout.ratio / 2,
                    height: height / layout.ratio,
                    top: 0,
                    left: width / layout.ratio / 2
                },
                division3: {
                    width: width / layout.ratio,
                    height: height / layout.ratio / 10,
                    top: height / layout.ratio - height / layout.ratio / 5,
                    left: 0
                }
            };
            break;
        case 7:
            var data = {
                division1: {
                    width: width / layout.ratio / 2,
                    height: height / layout.ratio,
                    top: 0,
                    left: 0
                },
                division2: {
                    width: width / layout.ratio / 2,
                    height: height / layout.ratio,
                    top: 0,
                    left: width / layout.ratio / 2
                },
                division3: {
                    width: width / layout.ratio,
                    height: height / layout.ratio / 10,
                    top: height / layout.ratio / 10,
                    left: 0
                }
            };
            break;
        case 8:
            var data = {
                division1: {
                    width: width / layout.ratio / 2,
                    height: height / layout.ratio / 2,
                    top: 0,
                    left: 0
                },
                division2: {
                    width: width / layout.ratio / 2,
                    height: height / layout.ratio / 2,
                    top: 0,
                    left: width / layout.ratio / 2
                },
                division3: {
                    width: width / layout.ratio / 2,
                    height: height / layout.ratio / 2,
                    top: height / layout.ratio / 2,
                    left: width / layout.ratio / 2
                },
                division4: {
                    width: width / layout.ratio / 2,
                    height: height / layout.ratio / 2,
                    top: height / layout.ratio / 2,
                    left: 0
                }
            };
            break;
        default:
            break;
    }

    for (let key_data in data) {
        var color = doom.getRandomColor();
        layout.count = layout.count + 1;
        html_append = html_append + division.html
            .replaceAll('top: 10px;', 'top: ' + data[key_data].top + 'px;')
            .replaceAll('left: 10px;', 'left: ' + data[key_data].left + 'px;')
            .replaceAll('width: 100px;', 'width: ' + data[key_data].width + 'px;')
            .replaceAll('height: 100px;', 'height: ' + data[key_data].height + 'px;')
            .replaceAll('[color]', color)
            .replaceAll('[count]', layout.count);

        division_append = division_append + division.html_order_division
            .replaceAll('[division_id]', layout.count)
            .replaceAll('[color]', color);
    }


    $('#order_divison div').remove();
    $('#parent div').remove();

    $('#order_divison').append(division_append);
    $('#parent').append(html_append);

    doom.callBack();
}

function showLayoutDefault() {
    var layout_default = document.getElementById("layout_default");
    $('#order_divison b').text('Điền thông số thứ tự ứng với màu sắc màn hình (Số thứ tự càng nhỏ sẽ được chèn lên trên cùng)!');
    if (layout_default.classList.contains("d-none")) {
        $('#layout_default').removeClass('d-none');
        $('#i_showLayoutDefault').removeClass('ri-arrow-down-s-line');
        $('#i_showLayoutDefault').addClass('ri-arrow-up-s-line');
        $('#div_layout_default .text_header').removeClass('d-none');
        $('#div_layout_default .text_header center h5').text('Một số bố cục màn hình cơ bản');
    } else {
        $('#layout_default').addClass('d-none');
        $('#i_showLayoutDefault').removeClass('ri-arrow-up-s-line');
        $('#i_showLayoutDefault').addClass('ri-arrow-down-s-line');
        $('#div_layout_default .text_header center h5').text('');
        $('#div_layout_default .text_header').addClass('d-none');
    }

}

function orderDivision(key) {
    var index = $('#order_division_' + key).val();
    var div = document.getElementById('child' + key);
    if (div != null) {
        div.style.zIndex = index;
    }
}

function previewAll() {

    doom.url = form.urlGetDataPreview;
    doom.data = {
        _token: main.token,
        data: getAllValue()
    };
    doom.type = 11;
    doom.method = 'POST';

    doom.callApi();
}

function showPreviewAll(data) {
    $('#modalPreview').modal('show');

    // create layout preview
    var div = document.createElement("div");
    div.style.width = layout.data.width / layout.ratioPreview;
    div.style.height = layout.data.height / layout.ratioPreview;
    // div.style.background = 'black';
    div.style.position = 'relative';
    div.id = 'layout_preview';

    $('#modal-body-preview').append(div);

    for (let division in layout.data.division) {
        var dataDivision = layout.data.division[division];

        var divDivision = document.createElement("div");
        divDivision.classList.add("division_modal");
        divDivision.style.width = dataDivision.width / layout.ratioPreview;
        divDivision.style.height = dataDivision.height / layout.ratioPreview;
        divDivision.style.top = dataDivision.x / layout.ratioPreview;
        divDivision.style.left = dataDivision.y / layout.ratioPreview;
        divDivision.id = 'div_divsion_preview' + dataDivision.id;

        $('#layout_preview').append(divDivision);
    }


    for (let division_id in data) {
        var resource = data[division_id]
        var delay = 0;
        for (let keyData in resource) {
            setTimeout(functionDelay, delay, [resource[keyData], division_id]);
            delay = delay + resource[keyData].duration;

        }
    }
}

function functionDelay([item, division_id]) {
    for (let division in layout.data.division) {
        if (division_id == layout.data.division[division].id) {
            var height_division_preview = layout.data.division[division].height / layout.ratioPreview;
            var width_division_preview = layout.data.division[division].width / layout.ratioPreview;
        }
    }

    if (item.type == 1) {
        var file = item.content;
    }

    if (item.type == 2) {
        var file = document.createElement("img");
        file.src = form.urlResources(item.content);
        file.height = height_division_preview;
        file.width = width_division_preview;
    }

    if (item.type == 3) {
        var file = document.createElement("video");
        file.height = height_division_preview;
        file.width = width_division_preview;
        file.src = form.urlResources(item.content);
        file.muted = true;
        file.play();
    }
    appendChildPreviewAll(file, division_id, item.type);
}


function appendChildPreviewAll(file, division_id, type) {
    div = document.getElementById('div_divsion_preview' + division_id);
    div.innerHTML = '';

    if (type == 1) {
        $('#div_divsion_preview' + division_id).append('<marquee><h2 style="color: #fff">' + file + '</h2></marquee>');
    } else {
        div.append(file);
    }
}

function hidePreviewAll() {
    $('#modal-body-preview div').remove();
    $('#modalPreview').modal('hide');
}

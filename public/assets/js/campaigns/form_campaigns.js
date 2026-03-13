$(document).ready(() => {
    doom.callBack();
    if (form.company_id != '') {
        selectCompany();
    }
});

let layout = {
    id: '',
    data: '',
    width: 0,
    height: 0,
    count: 0,
    ratio: 2,
    ratioPreview: 2,
    button: null,
    timeline_html: `<div class="row row_timeline_layout">
                        <div id="timeline_division_[division_id]" class="col-1"
                            style="text-align: center; position: relative; width: [width]; height: [height]; border: 1px solid black;">
                        </div>
                        <div id="hori-timeline[division_id]" class="hori-timeline col-10" dir="ltr">
                            <div class="list-inline events timeline" dragleave="dragleave(event)" ondrop="drop(event)" ondragover="allowDrop(event)" id="ul_division_id_[division_id]">
                            </div>
                        </div>
                        <div class="col-1" style="width:50px;">
                            <a title="Xóa toàn bộ nội dung" class="button-inside-image btn-sm btn btn-outline-danger waves-effect waves-light" onclick="deleteAllResources([division_id])">
                                <i class="ri-delete-bin-line"></i>
                            </a>
                        </div>
                    </div>`,
    item_html: `<div class="dropdown col-1 dropdown_items" id="div_layout_[id]">
                    <button class="btn btn-secondary button_items" style="min-width: 130px;" id="dropdownMenuButton"
                        data-bs-toggle="dropdown">
                        [width_main] x [height_main]
                    </button>
                    <div class="dropdown-menu" aria-labelledby="dropdownMenuButton">
                        <div style="text-align: right; min-width: 240px;">
                            <a>Màn hình: [width_main] x [height_main]</a>
                            <a class="button-inside-image btn-sm btn btn-outline-success waves-effect waves-light"
                                onclick="choseLayout([id])">
                                <i class="ri-check-line"></i>
                            </a>
                            <a class="button-inside-image btn-sm btn btn-outline-primary waves-effect waves-light"
                                onclick="choseLayout([id],9)">
                                <i class="ri-pencil-line"></i>
                            </a>
                            <a class="button-inside-image btn-sm btn btn-outline-danger waves-effect waves-light"
                                onclick="deleteLayouts([id])">
                                <i class="ri-delete-bin-line"></i>
                            </a>
                        </div>
                        <div id="item_layout_[id]" 
                            style="text-align: center; position: relative; width: [width]; height: [height]; border: 1px solid black;">
                        </div>
                    </div>
                </div>`,

    html: `<div id="parent" style="position: relative; width: [width]; height: [height]; border: 1px solid black;">
                <a id="parent_add" class="button-inside-image btn-sm btn btn-outline-success waves-effect waves-light"
                    onclick="createDivision()">
                    <i class="ri-add-line"></i>
                </a>
            </div>`
}

let division = {
    item_html: `<div
                    style="position: absolute; top: [x]px; left: [y]px; width: [width]px; height: [height]px; border: 1px solid [color]; background-color: [color];">
                </div>`,

    html: `<div id="child[count]" class="child"
                style="position: absolute; top: 10px; left: 10px; width: 100px; height: 100px; border: 1px solid [color]; background-color: [color];">
                <a id='child_delete[count]' class="button-inside-image btn-sm btn btn-outline-danger waves-effect waves-light"
                    onclick="doom.deleteHtml('child[count]')">
                    <icon class="ri-delete-bin-line"></icon>
                </a>
            </div>`,

    divisionLocal: function () {
        var datadivisionLocal = [];
        for (let index = 1; index < layout.count + 1; index++) {
            if (document.getElementById("child" + index)) {

                var child = $("#child" + index);
                datadivisionLocal.push({
                    Position: {
                        x: child.position().top * layout.ratio,
                        y: child.position().left * layout.ratio
                    },
                    Size: {
                        width: (child.width() + 2) * layout.ratio,
                        height: (child.height() + 2) * layout.ratio
                    },
                    Order: $('#order_division_' + index).val()
                });
            }
        }

        return datadivisionLocal;
    },
    html_order_division: `<div class="input-group col-2" id="order_divison_item_[division_id]"
                            style="max-width: 110px; padding: 3px;">
                            <span class="input-group-text" style="background-color:[color]"></span>
                            <input type="number" id="order_division_[division_id]" name="order_division_[division_id]" class="form-control"
                                    value="" placeholder="Stt" onchange="orderDivision([division_id])">
                        </div>`
}

let resources = {
    count: 100,
    data: '',
    key: '',
    category_html: `<li class="nav-item">
                        <a class="nav-link" data-bs-toggle="tab" href="#category_tab_[category_id]" role="tab"
                            aria-selected="false">
                            [category_name]
                        </a>
                    </li>`,
    category_tab_pane: `<div class="tab-pane" id="category_tab_[category_id]" role="tabpanel">
                            <h5>[category_name]</h5>
                            <div class="row">
                                <div class="col-4">
                                    <b>Tài liệu text</b>
                                    <div class="row mb-3 div_resources" id="resources_[category_id]_type_1">
                                    </div>
                                </div>
                                <div class="col-4">
                                    <b>Tài liệu hình ảnh</b>
                                    <div class="row mb-3 div_resources" id="resources_[category_id]_type_2">
                                    </div>
                                </div>
                                <div class="col-4">
                                    <b>Tài liệu videos</b>
                                    <div class="row mb-3 div_resources" id="resources_[category_id]_type_3">
                                    </div>
                                </div>
                            </div>
                        </div>`,

    resource_html: `<div data-post-id="1" data-test="1" class="item_resources">
                        <a type="button" class="event-date btn" data_id="[resource_id]" style="width: 120px;white-space: normal;background-color: [color]; color: #fff;">[resource_name]</a>
                        <input name="order" id="number" value="" type="hidden">
                        <input name="id" value="[resource_id]" type="hidden">
                    </div>`,
    input_type_1: `<div id="input_type" data-id=[id] class="row g-3">
                    <div class="col-xxl-6">
                        <div>
                            <label class="form-label">Chọn hiệu ứng</label>
                            <select class="form-control" name="effect" id="">
                                <option disabled value="">chọn hiệu ứng</option>
                                <option value="left">Chạy từ phải qua trái</option>
                                <option value="right">Chạy từ trái qua phải</option>
                                <option value="up">Chạy từ trên xuống dưới</option>
                                <option value="down">Chạy từ dưới lên trên</option>
                            </select>
                        </div>
                    </div>

                    <div class="col-xxl-6">
                        <div>
                            <label for="lastName" class="form-label">Thời lượng</label>
                            <input type="number" class="form-control" id="run_time" name="run_time"
                                placeholder="Thời lượng (giây)">
                        </div>
                    </div>
                </div>`,
    input_type_2: `<div id="input_type" data-id=[id] class="row g-3">
                <div class="col-xxl-6">
                    <div>
                        <label for="firstName" class="form-label">Chọn hiệu ứng bắt đầu</label>
                        <select class="form-control" name="effect_start" id="">
                            <option disabled value="">chọn hiệu ứng</option>
                            <option value="left">Chạy từ phải qua trái</option>
                            <option value="right">Chạy từ trái qua phải</option>
                            <option value="up">Chạy từ trên xuống dưới</option>
                            <option value="down">Chạy từ dưới lên trên</option>
                        </select>
                    </div>
                </div>

                <div class="col-xxl-6">
                    <div>
                        <label for="firstName" class="form-label">Chọn hiệu ứng kết thúc</label>
                        <select class="form-control" name="effect_end" id="">
                            <option disabled value="">chọn hiệu ứng</option>
                            <option value="left">Chạy từ phải qua trái</option>
                            <option value="right">Chạy từ trái qua phải</option>
                            <option value="up">Chạy từ trên xuống dưới</option>
                            <option value="down">Chạy từ dưới lên trên</option>
                        </select>
                    </div>
                </div>
                
                <div class="col-xxl-6">
                    <div>
                        <label for="lastName" class="form-label">Thời lượng</label>
                        <input type="number" class="form-control" id="run_time" name="run_time"
                            placeholder="Thời lượng (giây)">
                    </div>
                </div>
            </div>`,
    input_type_3: `<div id="input_type" class="row g-3">
                    </div>`



}

let doom = {
    text_type_action: '',
    nextPage: '',
    category_list: [],
    url: '',
    method: '',
    data: '',
    type: 0,
    item_radio: 8,
    timeline_radio: 15,
    listDivison: null,

    getRandomColor: function () {
        const letters = '0123456789ABCDEF';
        let color;
        do {
            color = '#';
            for (let i = 0; i < 6; i++) {
                color += letters[Math.floor(Math.random() * 16)];
            }
        } while (color === '#FFFFFF' || color === 'rgba(0,0,0,0)');
        return color;
    },

    callBack: function () {
        $(".child").draggable({
            containment: "#parent"
        });
        $(".child").resizable();
    },

    deleteHtml: function (key) {
        if (key != 'parent') {
            $('#' + key).remove();
            $('#order_divison_item_' + key.substring(5)).remove();
        } else {
            $('#' + key).remove();
            $('#with_layout').removeClass('d-none');
            $('#height_layout').removeClass('d-none');
            $('#createLayoutBtn').removeClass('d-none');
            $('#deleteLayout').addClass('d-none');
            $('#div_layout_default').addClass('d-none');
            $('#order_divison div').remove();
            $('#order_divison b').text('');
        }

    },

    callApi: function () {
        $.ajax({
            url: doom.url,
            method: doom.method,

            data: JSON.stringify(doom.data),
            contentType: "application/json; charset=utf-8",
            dataType: "json",
            success: function (data) {
                switch (doom.type) {
                    case 1:
                        $('#exampleModalPopovers').modal('hide');
                        doom.appendHtmlLayout(data);
                        doom.deleteHtml('parent');
                        break;
                    case 2:
                        $('.dropdown_items').remove();
                        data.forEach(element => {
                            doom.appendHtmlLayout(element);
                        });
                        if (form.company_id != '') {
                            choseLayout(parseInt(form.layout_id));
                        }
                        break;
                    case 3:
                        $('#div_layout_' + data).remove();
                        break;
                    case 4:
                        layout.data = data;
                        afterChoseLayout(data);
                        break;
                    case 5:
                        selectCategoryResources(data);
                        break;
                    case 6:
                        resources.data = data;
                    case 7:
                        if (doom.nextPage == true) {
                            if (doom.text_type_action == 'update') {
                                var message = 'Cập nhật nội dung thành công!';
                            } else {
                                var message = 'Tạo mới nội dung thành công!';
                            }
                            localStorage.setItem('alert-success', message);
                            location.replace(form.urlIndex);
                        }
                        break;
                    case 8:
                        addResourcesWhenEdits(data);
                        break;
                    case 9:
                        showFormEditLayout(data);
                        break;
                    case 10:
                        $('#exampleModalPopovers').modal('hide');
                        alert('Cập nhật layout thành công!');
                        break;
                    case 11:
                        showPreviewAll(data);
                        break;

                    default:
                        console.log('chưa chuyền dữ liệu cho doom.type');
                        break;
                }
            },
            error: function (xhr, status, error) {
                console.log(doom.type);
                console.log(error, 'show lỗi');
            }
        });
    },
    appendHtmlLayout: function (data) {
        var width = '';
        var height = '';
        var x = '';
        var y = '';

        var html = '';
        var html_division = '';

        width = data.width / doom.item_radio;
        height = data.height / doom.item_radio;
        html = html + layout.item_html
            .replaceAll('[id]', data.id)
            .replaceAll('[width_main]', data.width)
            .replaceAll('[height_main]', data.height)
            .replaceAll('[width]', width)
            .replaceAll('[height]', height);

        $('#list_item_layout').append(html);
        data.division.forEach(item => {
            width = item.width / doom.item_radio;
            height = item.height / doom.item_radio;
            x = item.x / doom.item_radio;
            y = item.y / doom.item_radio;

            html_division = html_division + division.item_html
                .replaceAll('[x]', x)
                .replaceAll('[y]', y)
                .replaceAll('[width]', width)
                .replaceAll('[height]', height)
                .replaceAll('[color]', doom.getRandomColor());

        });
        $('#item_layout_' + data.id).append(html_division);
    },
    changLayoutButton: function (id) {
        if (layout.button != null) {
            layout.button.style.backgroundColor = "#5b71b9";
        }
        layout.button = document.querySelector("#div_layout_" + id + " #dropdownMenuButton");

        layout.button.style.backgroundColor = "red";
    },
    DragAndDropLibrary: function (listDivison = []) {
        if (listDivison.length > 0) {
            if (doom.category_list.length > 0) {
                doom.category_list.forEach(category_id => {
                    for (let index = 1; index <= resources.count + 1; index++) {
                        $("#resources_" + category_id + "_type_" + index + " > div").draggable({
                            connectToSortable: '.ui-sortable',
                            cursor: 'pointer',
                            helper: 'clone',
                            revert: 'true'
                        });
                    }
                });
            }
            /********* Sortable ***********/
            listDivison.forEach(divison => {
                $('#ul_division_id_' + divison).sortable({
                    revert: true,
                    update: function (event, ui) {
                        var id = event.target.id;

                        $('#' + id).children().each(function (i) {

                            $(this).attr('id', i);

                            var x = $(this).find('.hidden');

                            // $(this).find("input").val(i);
                            $(this).find("input").first().val(i);

                            var key = main.timestamp() + i;

                            $(this).first().attr('id', "div_" + key);

                            $(this).find("a").first()
                                .attr('id', key)
                                .attr('ondblclick', "showModalOptions(" + key + ")");

                            $(this).find(x).removeClass("hidden").addClass("remove");
                            $(this).find(".remove").attr('id', i);
                        });
                    }
                });
            });
            if (form.company_id != '') {
                callDataResourcesWhenEdits();
            }
        }
    },
    html_modal: `<div class="modal fade" id="exampleModalPopovers" tabindex="-1" aria-labelledby="exampleModalPopoversLabel"
                    aria-modal="true">
                    <div class="modal-dialog modal-xl">
                    <div class="modal-content">
                                <div class="modal-header">
                                    <div class="col-3" style="padding-right: 0px">
                                        <input id="with_layout" type="number" name="with_layout" value="" class="form-control"
                                            placeholder="with">
                                    </div>
                                    <div class="col-3" style="padding-right: 0px">
                                        <input id="height_layout" type="number" name="height_layout" value=""
                                            class="form-control" placeholder="height">
                                    </div>
                                    <div class="col-3" id="createLayoutModal" style="padding-right: 0px">
                                        <a href="#" role="button" class="btn btn-secondary popover-test"
                                            id="createLayoutBtn" onclick="createLayout()">Tạo
                                            layout</a>
                                    </div>
                                        <a type="button" class="btn-close" onclick="$('#exampleModalPopovers').remove();"
                                        data-bs-dismiss="modal" aria-label="Close"></a>                        
                                </div>
                                <div id="div_layout_default" class="d-none">
                                    <div class="text_header d-none"><center><h5></h5></center></div>
                                    <div class="modal-header d-none" id="layout_default">
                                        <div class="col-1" style="text-align: center; position: relative; width: 100px; height: 50px;" onclick="addDivision(1)">
                                            <div style="position: absolute; top: 0px; left: 0px; width: 50px; height: 50px; border: 1px solid #DC4E14; background-color: #DC4E14;">
                                            </div>
                                            <div style="position: absolute; top: 0px; left: 50px; width: 50px; height: 50px; border: 1px solid #3719dd; background-color: #3719dd;">
                                            </div>
                                        </div>

                                        <div class="col-1" style="text-align: center; position: relative; width: 100px; height: 50px;" onclick="addDivision(2)">
                                            <div style="position: absolute; top: 0px; left: 0px; width: 50px; height: 50px; border: 1px solid #DC4E14; background-color: #DC4E14;">
                                            </div>
                                            <div style="position: absolute; top: 0px; left: 50px; width: 50px; height: 25px; border: 1px solid #3719dd; background-color: #3719dd;">
                                            </div>
                                            <div style="position: absolute; top: 25px; left: 50px; width: 50px; height: 25px; border: 1px solid #6f669f; background-color: #6f669f;">
                                            </div>
                                        </div>

                                        <div class="col-1" style="text-align: center; position: relative; width: 100px; height: 50px;" onclick="addDivision(3)">
                                            <div style="position: absolute; top: 0px; left: 0px; width: 50px; height: 25px; border: 1px solid #DC4E14; background-color: #DC4E14;">
                                            </div>
                                            <div style="position: absolute; top: 25px; left: 0px; width: 50px; height: 25px; border: 1px solid #0dad6a; background-color: #0dad6a;">
                                            </div>
                                            <div style="position: absolute; top: 0px; left: 50px; width: 50px; height: 50px; border: 1px solid #3719dd; background-color: #3719dd;">
                                            </div>
                                        </div>

                                        <div class="col-1" style="text-align: center; position: relative; width: 100px; height: 50px;" onclick="addDivision(4)">
                                            <div style="position: absolute; top: 0px; left: 0px; width: 50px; height: 45px; border: 1px solid #DC4E14; background-color: #DC4E14;">
                                            </div>
                                            <div style="position: absolute; top: 0px; left: 50px; width: 50px; height: 45px; border: 1px solid #0dad6a; background-color: #0dad6a;">
                                            </div>
                                            <div style="position: absolute; top: 45px; left: 0px; width: 100px; height: 5px; border: 1px solid #3719dd; background-color: #3719dd;">
                                            </div>
                                        </div>

                                        <div class="col-1" style="text-align: center; position: relative; width: 100px; height: 50px;" onclick="addDivision(5)">
                                            <div style="position: absolute; top: 5px; left: 0px; width: 50px; height: 45px; border: 1px solid #DC4E14; background-color: #DC4E14;">
                                            </div>
                                            <div style="position: absolute; top: 5px; left: 50px; width: 50px; height: 45px; border: 1px solid #0dad6a; background-color: #0dad6a;">
                                            </div>
                                            <div style="position: absolute; top: 0px; left: 0px; width: 100px; height: 5px; border: 1px solid #3719dd; background-color: #3719dd;">
                                            </div>
                                        </div>

                                        <div class="col-1" style="text-align: center; position: relative; width: 100px; height: 50px;" onclick="addDivision(6)">
                                            <div style="position: absolute; top: 0px; left: 0px; width: 50px; height: 50px; border: 1px solid #DC4E14; background-color: #DC4E14;">
                                            </div>
                                            <div style="position: absolute; top: 0px; left: 50px; width: 50px; height: 50px; border: 1px solid #0dad6a; background-color: #0dad6a;">
                                            </div>
                                            <div style="position: absolute; top: 40px; left: 0px; width: 100px; height: 5px; border: 1px solid #3719dd; background-color: #3719dd;">
                                            </div>
                                        </div>

                                        <div class="col-1" style="text-align: center; position: relative; width: 100px; height: 50px;" onclick="addDivision(7)">
                                            <div style="position: absolute; top: 0px; left: 0px; width: 50px; height: 50px; border: 1px solid #DC4E14; background-color: #DC4E14;">
                                            </div>
                                            <div style="position: absolute; top: 0px; left: 50px; width: 50px; height: 50px; border: 1px solid #0dad6a; background-color: #0dad6a;">
                                            </div>
                                            <div style="position: absolute; top: 5px; left: 0px; width: 100px; height: 5px; border: 1px solid #3719dd; background-color: #3719dd;">
                                            </div>
                                        </div>

                                        <div class="col-1" style="text-align: center; position: relative; width: 100px; height: 50px;" onclick="addDivision(8)">
                                            <div style="position: absolute; top: 0px; left: 0px; width: 50px; height: 25px; border: 1px solid #DC4E14; background-color: #DC4E14;">
                                            </div>
                                            <div style="position: absolute; top: 25px; left: 0px; width: 50px; height: 25px; border: 1px solid #0dad6a; background-color: #0dad6a;">
                                            </div>
                                            <div style="position: absolute; top: 0px; left: 50px; width: 50px; height: 25px; border: 1px solid #3719dd; background-color: #3719dd;">
                                            </div>
                                            <div style="position: absolute; top: 25px; left: 50px; width: 50px; height: 25px; border: 1px solid #6f669f; background-color: #6f669f;">
                                            </div>
                                        </div>
                                    </div>
                                    <a onclick="showLayoutDefault()" class="btn circle-btn"><i id="i_showLayoutDefault" class="ri-arrow-down-s-line"></i></a>
                                </div>
                                <div class="modal-body">
                                    <div id="layout_div" style="text-align: center">
                
                                    </div>
                                </div>
                                <div class="modal-body row" id="order_divison" style="padding-left: 20px; padding-right: 10px;">
                                <b style="color: red;"></b>
                                </div>

                                <div class="modal-footer">
                                    <a id="deleteLayout" onclick="doom.deleteHtml('parent')" type="button"
                                        class="btn btn-danger d-none">Xóa layout</a>
                                    <a onclick="saveLayout('[url]')" type="button" class="btn btn-primary">Lưu
                                        layout</a>
                                </div>
                            </div>
                    </div>
                </div>`,
}

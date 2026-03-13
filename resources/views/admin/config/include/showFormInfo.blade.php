<form id="form_info" action="{{ route('config.update') }}" enctype="multipart/form-data" method="post">
    @csrf
    <input type="hidden" name="id" value="{{ $id }}" />
    <input type="hidden" name="logo_old" value="{{ @$data['logo'] }}" />
    <div class="card">
        <div class="card-header title_content">
            <b class="text-center">Cấu hình thông tin</b>
        </div>
        <div class="card-body">
            <div class="row">
                <div class="card col-6">
                    <div class="card-body border border-dashed border-start-0 border-end-0">
                        <div class="row mb-3">
                            <div class="col-6">
                                <div class="form_input">
                                    <label class="form-label">Hotline <span
                                            style="color: red;font-weight: bold">*</span>
                                    </label>
                                    <input class="form-control" name="hotline" type="text"
                                        value="{{ old('hotline', isset($data['hotline']) ? $data['hotline'] : '') }}"
                                        placeholder="">
                                    @if ($errors->has('hotline'))
                                        <div class="bg-danger text-white text-center py-1">
                                            <span>{{ $errors->first('hotline') }}</span>
                                        </div>
                                    @endif
                                </div>

                            </div>

                            <div class="col-6">
                                <div class="form_input">
                                    <label class="form-label">Email <span
                                            style="color: red;font-weight: bold">*</span></label>
                                    <input class="form-control" name="email" type="text"
                                        value="{{ old('email', isset($data['email']) ? $data['email'] : '') }}"
                                        placeholder="">
                                    @if ($errors->has('email'))
                                        <div class="bg-danger text-white text-center py-1">
                                            <span>{{ $errors->first('email') }}</span>
                                        </div>
                                    @endif
                                </div>

                            </div>
                        </div>
                        <div class="row mb-3">
                            <div class="col-6">
                                <div class="form_input">
                                    <label class="form-label">Địa chỉ <span
                                            style="color: red;font-weight: bold">*</span></label>
                                    <input class="form-control" name="address" type="text"
                                        value="{{ old('address', isset($data['address']) ? $data['address'] : '') }}"
                                        placeholder="">
                                    @if ($errors->has('address'))
                                        <div class="bg-danger text-white text-center py-1">
                                            <span>{{ $errors->first('address') }}</span>
                                        </div>
                                    @endif
                                </div>
                            </div>

                            <div class="col-2">
                                <div class="form_input">
                                    <label class="form-label">HSD (Tháng) <span
                                            style="color: red;font-weight: bold">*</span></label>
                                    <input class="form-control" name="expiry_date" type="number"
                                        value="{{ old('expiry_date', isset($data['expiry_date']) ? $data['expiry_date'] : '') }}"
                                        placeholder="">
                                    @if ($errors->has('expiry_date'))
                                        <div class="bg-danger text-white text-center py-1">
                                            <span>{{ $errors->first('expiry_date') }}</span>
                                        </div>
                                    @endif
                                </div>
                            </div>

                            <div class="col-4">
                                <div class="form_input">
                                    <label class="form-label">Thời gian hoạt động <span
                                            style="color: red;font-weight: bold">*</span></label>

                                    <input class="form-control" name="operating_time" type="text"
                                        value="{{ old('operating_time', isset($data['operating_time']) ? $data['operating_time'] : '') }}"
                                        placeholder="">

                                    @if ($errors->has('operating_time'))
                                        <div class="bg-danger text-white text-center py-1">
                                            <span>{{ $errors->first('operating_time') }}</span>
                                        </div>
                                    @endif
                                </div>
                            </div>

                        </div>
                        <div class="row mb-3">
                            <div class="col-6">
                                <div class="form_input">
                                    <label class="form-label">Facebook </label>
                                    <input class="form-control" name="facebook" type="text"
                                        value="{{ old('facebook', isset($data['facebook']) ? $data['facebook'] : '') }}"
                                        placeholder="">
                                    @if ($errors->has('facebook'))
                                        <div class="bg-danger text-white text-center py-1">
                                            <span>{{ $errors->first('facebook') }}</span>
                                        </div>
                                    @endif
                                </div>
                            </div>

                            <div class="col-6">
                                <div class="form_input">
                                    <label class="form-label">Instagram </label>
                                    <input class="form-control" name="instagram" type="text"
                                        value="{{ old('instagram', isset($data['instagram']) ? $data['instagram'] : '') }}"
                                        placeholder="">
                                    @if ($errors->has('instagram'))
                                        <div class="bg-danger text-white text-center py-1">
                                            <span>{{ $errors->first('instagram') }}</span>
                                        </div>
                                    @endif
                                </div>
                            </div>
                        </div>
                        <div class="row mb-3">
                            <div class="col-6">
                                <div class="form_input">
                                    <label class="form-label">Tiktok</label>
                                    <input class="form-control" name="tiktok" type="text"
                                        value="{{ old('tiktok', isset($data['tiktok']) ? $data['tiktok'] : '') }}"
                                        placeholder="">
                                    @if ($errors->has('tiktok'))
                                        <div class="bg-danger text-white text-center py-1">
                                            <span>{{ $errors->first('tiktok') }}</span>
                                        </div>
                                    @endif
                                </div>
                            </div>

                            <div class="col-6">
                                <div class="form_input">
                                    <label class="form-label">Zalo</label>
                                    <input class="form-control" name="zalo" type="text"
                                        value="{{ old('zalo', isset($data['zalo']) ? $data['zalo'] : '') }}"
                                        placeholder="">
                                    @if ($errors->has('zalo'))
                                        <div class="bg-danger text-white text-center py-1">
                                            <span>{{ $errors->first('zalo') }}</span>
                                        </div>
                                    @endif
                                </div>
                            </div>
                        </div>
                        <div class="row mb-3">
                            <div class="col-4">
                                <div class="form_input">
                                    <label class="form-label">Chính sách thanh toán <span
                                            style="color: red;font-weight: bold">*</span></label>
                                    <textarea rows="5" class="form-control" name="payment_policy">{{ old('payment_policy', $data['payment_policy'] ?? '') }}</textarea>
                                    @if ($errors->has('payment_policy'))
                                        <div class="bg-danger text-white text-center py-1">
                                            <span>{{ $errors->first('payment_policy') }}</span>
                                        </div>
                                    @endif
                                </div>
                            </div>

                            <div class="col-4">
                                <div class="form_input">
                                    <label class="form-label">Chính sách hủy <span
                                            style="color: red;font-weight: bold">*</span></label>
                                    <textarea rows="5" class="form-control" name="cancellation_policy">{{ old('cancellation_policy', $data['cancellation_policy'] ?? '') }}</textarea>
                                    @if ($errors->has('cancellation_policy'))
                                        <div class="bg-danger text-white text-center py-1">
                                            <span>{{ $errors->first('cancellation_policy') }}</span>
                                        </div>
                                    @endif
                                </div>
                            </div>

                            <div class="col-4">
                                <div class="form_input">
                                    <label class="form-label">Chính sách hoàn tiền <span
                                            style="color: red;font-weight: bold">*</span></label>
                                    <textarea rows="5" class="form-control" name="refund_policy">{{ old('refund_policy', $data['refund_policy'] ?? '') }}</textarea>
                                    @if ($errors->has('refund_policy'))
                                        <div class="bg-danger text-white text-center py-1">
                                            <span>{{ $errors->first('refund_policy') }}</span>
                                        </div>
                                    @endif
                                </div>
                            </div>
                        </div>
                        <div class="row mb-3">

                            <div class="col-6">
                                <label class="form-label">&nbsp;</label>
                                <div class="form-check">
                                    <label class="form-label"></label>
                                    <input class="form-check-input" type="checkbox"
                                        name="auto_send_electronic_tickets"
                                        @if (!empty($data['auto_send_electronic_tickets'])) checked @endif value="1">
                                    <label class="form-check-label">
                                        Tự động gửi vé điện tử khi mua vé online
                                    </label>
                                </div>
                                <br>
                                <div class="form_input">
                                    <label class="form-label">Logo <span
                                            style="color: red;font-weight: bold">*</span></label>
                                    <input class="form-control" type="file" name="logo">
                                    @if ($errors->has('logo'))
                                        <div class="bg-danger text-white text-center py-1">
                                            <span>{{ $errors->first('logo') }}</span>
                                        </div>
                                    @endif
                                </div>
                            </div>

                            <div class="col-6">
                                <div class="form_input">
                                    <img src="{{ $logo_link }}" width="100px" alt="" />
                                </div>
                            </div>
                        </div>

                    </div>
                </div>
                <div class="card col-6">
                    <div class="card-body border border-dashed border-start-0 border-end-0">
                        <div class="mb-3">
                            <div class="form_input">
                                <label class="form-label">
                                    <b>Tiêu đề trang chủ:</b>
                                </label>
                                <input class="form-control" type="text" name="home_content[title]"
                                    value="{{ @$data['home_content']['title'] }}"
                                    placeholder="Tiêu đề trang chủ ...">
                            </div>
                        </div>
                        <div class="mb-3">
                            <div class="table-responsive">
                                <table class="table align-middle table-nowrap mb-0 text-center">
                                    <thead>
                                        <tr>
                                            <th scope="col" style="width: 5%">STT</th>
                                            <th scope="col">Nội dung</th>
                                            <th scope="col" style="width: 5%">
                                                <a href="javasctipt:;" onclick="addColum()">
                                                    <b style="color: green;font-size: 10px;"><i
                                                            class="ri-add-line align-middle"></i>Thêm dữ liệu</b>
                                                </a>
                                            </th>
                                        </tr>
                                    </thead>
                                    <tbody id="list_content">
                                        @if (!empty($data['home_content']['content']))
                                            @foreach ($data['home_content']['content'] as $key_content => $val_content)
                                                <tr id="tr_{{ $key_content }}">
                                                    <td>{{ $key_content + 1 }}</td>
                                                    <td>
                                                        <textarea name="home_content[content][]" class="form-control" cols="30" rows="2">{{ $val_content }}</textarea>
                                                    </td>
                                                    <td>
                                                        <a class="btn btn-sm btn-outline-danger waves-effect waves-light"
                                                            onclick="$('#tr_{{ $key_content }}').remove();arrangeColum()"><i
                                                                class="ri-delete-bin-6-line"></i></a>
                                                    </td>
                                                </tr>
                                            @endforeach
                                        @endif

                                    </tbody>
                                </table>
                            </div>
                        </div>
                    </div>
                </div>
            </div>
        </div>
        <div class="card-footer">
            <div class="col-sm-auto">
                <button class="btn btn-primary" type="button" onclick="$('#form_info').submit()">
                    Cập nhật
                </button>
            </div>
        </div>
    </div>
</form>

<template id="template_html">
    <tr id="tr_[key]">
        <td></td>
        <td>
            <textarea name="[name]" class="form-control" cols="30" rows="2"></textarea>
        </td>
        <td>
            <a class="btn btn-sm btn-outline-danger waves-effect waves-light"
                onclick="$('#tr_[key]').remove();arrangeColum()"><i class="ri-delete-bin-6-line"></i></a>
        </td>
    </tr>
</template>

<script>
    function addColum() {
        key = main.timestamp();
        name = 'home_content[content][]'
        html = $('#template_html').html();
        $('#list_content').append(html.replaceAll('[key]', key).replaceAll('[name]', name));
        arrangeColum()
    }

    function arrangeColum() {
        $('#list_content tr').each(function(index) {
            $(this).find('td:first').text(index + 1);
        });
    }
</script>

@extends('layouts.master')
@section('title')
    @lang('translation.dashboards')
@endsection
@section('content')
    @include('components.breadcrumb')
    <div class="row">
        <div class="col">
            <form id="form_data" action="{{ route($action) }}" method="post" enctype="multipart/form-data">
                @csrf
                <input type="hidden" name="id" value="{{ @$data->id }}">
                <div class="card">
                    <div class="card-body border border-dashed border-start-0 border-end-0">
                        <div id="main_data" class="">
                            <div class="row mb-3">
                                <div class="col-3">
                                    <label class="form-label">Tên<span
                                            style="color:red;font-size:15px;font-weight:bold">*</span></label>
                                    <input class="form-control" name="name" type="text"
                                        value="{{ old('name', isset($data['name']) ? $data['name'] : '') }}"
                                        placeholder="Nhập tên lịch đặt ...">
                                    @if ($errors->has('name'))
                                        <div class="bg-danger text-white text-center py-1">
                                            <span>{{ $errors->first('name') }}</span>
                                        </div>
                                    @endif
                                </div>

                                <!-- <div class="col-3">
                                    <label class="form-label">Mô tả</label>
                                    <input class="form-control" name="description" type="text"
                                        value="{{ old('description', isset($data['description']) ? $data['description'] : '') }}"
                                        placeholder="Nhập tên lịch đặt ...">
                                    @if ($errors->has('description'))
                                        <div class="bg-danger text-white text-center py-1">
                                            <span>{{ $errors->first('description') }}</span>
                                        </div>
                                    @endif
                                </div> -->


                            </div>
                            <hr>
                            @include('admin.role.include.permission')

                        </div>
                        <input id="data_id" type="hidden" name="id" value="{{ @$data->id }}">

                    </div>

                    <div class="card-footer">
                        <div class="col-sm-auto">
                            <a href="{{ route('role.index') }}" class="btn btn-danger waves-effect waves-light">Quay
                                lại</a>
                            @if (!empty($data))
                                <button class="btn  btn-primary" type="submit">Cập nhật</button>
                            @else
                                <button onclick="submitForm()" class="btn  btn-primary" type="submit">Thêm mới</button>
                            @endif

                        </div>
                    </div>
                </div>
            </form>

        </div>
    </div>
    {{-- @if (count($errors) > 0)
        @dd()
    @endif --}}
@endsection
@section('script')
    <script>
        @if (!empty(session('alert-error')))
            Swal.fire({
                position: 'top-end',
                icon: 'error',
                title: "{{ session('alert-error') }}",
                showConfirmButton: false,
                timer: 1500,
                showCloseButton: false
            });
        @endif
    </script>

    <script>
        $(document).ready(() => {
            var id = $('#data_id').val();
            var count = 0;
            if (id != '') {
                for (let key in form.permission) {
                    count++;
                    updateCheckAll(key + count);
                }
            }
        });
        let form = {
            permission: @json($permission, true),
        }

        function checkBoxAll(key) {
            var checkboxAll = document.getElementById("idCheckAll" + key);
            var checkboxes = document.getElementsByClassName(key);
            if (checkboxAll.checked) {
                for (var i = 0; i < checkboxes.length; i++) {
                    checkboxes[i].checked = true;
                }
            } else {
                for (var i = 0; i < checkboxes.length; i++) {
                    checkboxes[i].checked = false;
                }
            }
        }

        function updateCheckAll(key) {
            var checkboxes = document.getElementsByClassName(key);
            var checkAll = document.getElementById("idCheckAll" + key);
            var allChecked = true;
            for (var i = 0; i < checkboxes.length; i++) {
                if (!checkboxes[i].checked) {
                    allChecked = false;
                    break;
                }
            }
            checkAll.checked = allChecked;
        }
    </script>
@endsection

@extends('layouts.master')
@section('title')
    @lang('translation.dashboards')
@endsection
@section('content')
    @include('components.breadcrumb')

    <div class="row">
        <div class="col">
            <div class="h-100">
                <div class="row mb-1">
                    <div class="col-12">
                        <div class="card" id="user-list">
                            <div class="card-header">
                                <div class="row">
                                    <div class="col-md-9">
                                        <form action="{{ url('admin/user/search') }}" method="post">
                                            @csrf
                                            <div class="row g-3 mb-0 align-items-end">
                                                <div class="col-md-auto" style="width: 400px;">
                                                    <div class="floating-label-group">
                                                        <label class="floating-label">Từ khóa</label>
                                                        <div class="form-icon form-icon-custom  w-100">
                                                            <input type="text" class="form-control-icon form-control-custom"
                                                                id="key_search" name="key_search"
                                                                placeholder="Tên đăng nhập | tên | số điện thoạ"
                                                                value="{{ session('search.key_search') ?? '' }}">
                                                            <i class="ri-search-line text-orange icon"></i>
                                                        </div>
                                                    </div>
                                                </div>

                                                <div class="col-sm-auto">
                                                    <input name="confirm_search" type="hidden" value="1" />
                                                    <button class="btn waves-effect waves-light" id="btnSearch"
                                                        type="submit"
                                                        style="background-color:#1e2b37;color:white; border-radius: 8px">
                                                        Tìm kiếm</button>
                                                </div>
                                            </div>
                                        </form>
                                    </div>

                                    <div class="col-md-3 d-flex align-items-end justify-content-end">

                                        @can('create_user')
                                            <form action="{{ route('user_create') }}">
                                                <button class="btn btn-cyan btn-color-white text-nowrap">
                                                    <i class="ri-add-fill"></i><span>Thêm mới</span>
                                                </button>
                                            </form>

                                        @endcan
                                    </div>
                                </div>
                            </div>


                            <div class="card-body">
                                <table class="table table-light-custom align-middle table-nowrap mb-0">
                                    <thead class="table-light text-muted" id="tasksTable">
                                        <thead class="table-light text-muted">
                                            <tr class="text-center">
                                                <th class="sort">STT</th>
                                                <th class="sort">Username</th>
                                                <th class="sort">Tên</th>
                                                <th class="sort">Điện thoại</th>
                                                <th class="sort">Loại tài khoản</th>
                                                <th style="width: 15%">Thao tác</th>
                                            </tr>
                                        </thead>
                                    <tbody class="list form-check-all">
                                        @isset($users)
                                            @foreach ($users as $key => $item)
                                                <tr id="tr_item{{ $item['id'] }}">
                                                    <td class="text-center">{{ $key + 1 }}</td>
                                                    <td style="text-align: left">{{ $item['user_name'] }}</td>
                                                    <td style="text-align: left">{{ $item['name'] }}</td>
                                                    <td style="text-align: center">{{ $item['phone'] }}</td>
                                                    <td style="text-align: center">
                                                        <span class="badge badge-soft-success text-uppercase">
                                                            <i class="ri-admin-line"></i> Tài khoản
                                                            <b>{{ $item['role_name'] }}</b>
                                                        </span>
                                                    </td>
                                                    @canany(['update_user', 'delete_user'])
                                                        <td class="d-flex align-items-center justify-content-center gap-3">
                                                            @can('update_user')
                                                                <a class="icon-edit icon-action"
                                                                    href="{{ url('admin/user/' . $item['id'] . '/edit') }}" title="Sửa">
                                                                    <i class="ri-edit-2-fill"></i>
                                                                </a>
                                                            @endcan
                                                            @can('delete_user')
                                                                @if ($item->user_name != 'admin' and $item->user_name != 'api')
                                                                    <a class="icon-delete icon-action" href="#"
                                                                        onclick="deleteItems('{{ $item['id'] }}','tr_item','{{ route('user_delete', $item['id']) }}')"
                                                                        title="Xóa">
                                                                        <i class="mdi mdi-delete"></i>
                                                                    </a>
                                                                @endif
                                                            @endcan
                                                        </td>
                                                    @endcanany
                                                </tr>
                                            @endforeach
                                        @endisset
                                    </tbody>
                                </table>
                                <!--end table-->
                            </div>
                            <div class="d-flex justify-content-end mt-2">
                                <div class="pagination-wrap hstack gap-2">
                                    {{ $users->links() }}
                                </div>
                            </div>
                        </div>
                    </div>
                </div>
            </div>
            <!--End row -->

        </div> <!-- end .h-100-->

        <div class="overlay hidden lds-dual-ring" id="loader">
        </div>
    </div>
    </div>
@endsection
@section('script')
    <script>
        @if (!empty(session('alert-success')))
            // sweetSuccess('{{ session('alert-success') }}');
            Swal.fire({
                position: 'top-end',
                icon: 'success',
                title: "{{ session('alert-success') }}",
                showConfirmButton: false,
                timer: 1500,
                showCloseButton: false
            });
        @endif
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

        $(document).ready(function () {
            $('.submitForm').on('click', function (e) {
                e.preventDefault();
                var form = $(this).parents('form');
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
                return false;
            });
        });
    </script>
@endsection
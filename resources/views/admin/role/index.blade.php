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
                        <div class="card" id="partner-list">
                            {{-- @can('create_role') --}}
                            <div class="card-header border-0">
                                <div class="d-flex align-items-end justify-content-start">
                                    <form action="{{ route('role.show_create_form') }}">
                                        <button class="btn btn-cyan btn-color-white text-nowrap">
                                            <i class="ri-add-fill"></i><span>Thêm mới</span>
                                        </button>
                                    </form>
                                </div>

                            </div>
                            {{-- @endcan --}}

                            <div class="card-body w-50 p-2">
                                <div class="table-responsive  table-card m-2 mt-0">
                                      <table class="table table-light-custom align-middle table-nowrap mb-0">
                                        <thead class="table-light text-muted">
                                            <tr class="text-center">
                                                <th class="sort" style="width: 4%">STT</th>
                                                <th class="sort">Tên vai trò</th>

                                                <th style="width: 15%">Tháo tác</th>
                                            </tr>
                                        </thead>
                                        <tbody class="list form-check-all">
                                            @isset($data)
                                                @foreach ($data as $key => $item)
                                                    <tr id="tr_{{ $item['id'] }}">
                                                        <td class="text-center">
                                                            <?php        echo (session('search.page') - 1) * $limit + ($key + 1); ?>
                                                        </td>

                                                        <td>{{ @$item['name'] }}</td>

                                                        <td class="text-center">
                                                            <div class="flex-shrink-0 ms-4">
                                                                <ul class="list-inline tasks-list-menu mb-0">
                                                                    @can('update_role')
                                                                        <li class="list-inline-item">
                                                                            <a class="btn btn-outline-primary btn-sm waves-effect waves-light"
                                                                                href="
                                                                                                            {{ route('role.show_edit_form', $item['id']) }}"
                                                                                title="Sửa">
                                                                                <i class="ri-pencil-line "></i>
                                                                            </a>
                                                                        </li>
                                                                    @endcan
                                                                    @can('delete_role')
                                                                        @if($item->name != "admin")
                                                                            <li class="list-inline-item">
                                                                                <a class="btn-sm btn btn-outline-danger waves-effect waves-light"
                                                                                    href="#"
                                                                                    onclick="deleteItems({{ $item['id'] }},'tr_','{{ route('role.delete', $item['id']) }}')"
                                                                                    title="Xóa">
                                                                                    <i class="ri-delete-bin-line"></i>
                                                                                </a>
                                                                            </li>
                                                                        @endif
                                                                    @endcan
                                                                </ul>
                                                            </div>
                                                        </td>
                                                    </tr>
                                                @endforeach
                                            @endisset
                                        </tbody>
                                    </table>
                                    <!--end table-->
                                </div>
                                <div class="d-flex justify-content-end mt-2">
                                    <div class="pagination-wrap hstack gap-2">
                                        {{ $data->links() }}
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
@endsection
    @section('script')
        <script>
            @if (!empty(session('alert-success')))
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
        </script>
    @endsection
@extends('inventory.layout')

@section('title', 'Kho hàng')

@section('inventory_content')
    <div class="page-title-box d-sm-flex align-items-center justify-content-between">
        <div class="page-title-left">
            <ol class="breadcrumb m-0">
                <li class="breadcrumb-item"><a href="{{ route('inventory.dashboard') }}"><b>Quản lý kho</b></a></li>
                <li class="breadcrumb-item active"><b>Kho hàng</b></li>
            </ol>
        </div>
    </div>

    <div class="card" id="warehouse-list">
        <div class="card-header">
            <div class="row">
                <div class="col-md-9">
                    <form method="GET">
                        <div class="row g-3 mb-0 align-items-end">
                            <div class="col-md-auto" style="width: 400px;">
                                <div class="floating-label-group">
                                    <label class="floating-label">Từ khóa</label>
                                    <div class="form-icon form-icon-custom w-100">
                                        <input name="keyword" class="form-control-icon form-control-custom" placeholder="Mã kho | tên kho | địa chỉ" value="{{ $keyword }}">
                                        <i class="ri-search-line text-orange icon"></i>
                                    </div>
                                </div>
                            </div>
                            <div class="col-sm-auto"><button class="btn waves-effect waves-light" style="background-color:#1e2b37;color:white; border-radius: 8px">Tìm kiếm</button></div>
                        </div>
                    </form>
                </div>
                <div class="col-md-3 d-flex align-items-end justify-content-end">
                    <a href="{{ route('inventory.warehouses.create') }}" class="btn btn-cyan btn-color-white text-nowrap"><i class="ri-add-fill"></i><span>Thêm mới</span></a>
                </div>
            </div>
        </div>
        <div class="card-body">
            <table class="table table-light-custom align-middle table-nowrap mb-0">
                <thead class="table-light text-muted"><tr class="text-center"><th>Mã kho</th><th>Tên kho</th><th>Địa chỉ</th><th>Trạng thái</th><th style="width: 15%">Thao tác</th></tr></thead>
                <tbody>
                @forelse($warehouses as $warehouse)
                    <tr>
                        <td>{{ $warehouse->code }}</td>
                        <td>{{ $warehouse->name }}</td>
                        <td>{{ $warehouse->address }}</td>
                        <td>@include('inventory.partials.status', ['status' => $warehouse->status])</td>
                        <td class="d-flex align-items-center justify-content-center gap-3">
                            <a class="icon-edit icon-action" href="{{ route('inventory.warehouses.edit', $warehouse->id) }}" title="Sửa"><i class="ri-edit-2-fill"></i></a>
                            <form method="POST" action="{{ route('inventory.warehouses.destroy', $warehouse->id) }}" class="d-inline" onsubmit="return confirm('Xóa kho này?')">
                                @csrf @method('DELETE')
                                <button class="icon-delete icon-action border-0 bg-transparent" title="Xóa"><i class="mdi mdi-delete"></i></button>
                            </form>
                        </td>
                    </tr>
                @empty
                    <tr><td colspan="5" class="text-center text-muted">Chưa có kho.</td></tr>
                @endforelse
                </tbody>
            </table>
        </div>
        <div class="d-flex justify-content-end mt-2 pe-3">{{ $warehouses->links() }}</div>
    </div>
@endsection

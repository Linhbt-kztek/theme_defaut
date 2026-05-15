@extends('inventory.layout')

@section('title', 'Sản phẩm')

@section('inventory_content')
    <div class="page-title-box d-sm-flex align-items-center justify-content-between">
        <div class="page-title-left">
            <ol class="breadcrumb m-0">
                <li class="breadcrumb-item"><a href="{{ route('inventory.dashboard') }}"><b>Quản lý kho</b></a></li>
                <li class="breadcrumb-item active"><b>Sản phẩm</b></li>
            </ol>
        </div>
    </div>

    <div class="row">
        <div class="col">
            <div class="card" id="product-list">
                <div class="card-header">
                    <div class="row">
                        <div class="col-md-9">
                            <form method="GET">
                                <div class="row g-3 mb-0 align-items-end">
                                    <div class="col-md-auto" style="width: 400px;">
                                        <div class="floating-label-group">
                                            <label class="floating-label">Từ khóa</label>
                                            <div class="form-icon form-icon-custom w-100">
                                                <input type="text" class="form-control-icon form-control-custom" name="keyword" placeholder="SKU | tên sản phẩm | barcode" value="{{ $keyword }}">
                                                <i class="ri-search-line text-orange icon"></i>
                                            </div>
                                        </div>
                                    </div>
                                    <div class="col-sm-auto">
                                        <button class="btn waves-effect waves-light" type="submit" style="background-color:#1e2b37;color:white; border-radius: 8px">Tìm kiếm</button>
                                    </div>
                                </div>
                            </form>
                        </div>
                        <div class="col-md-3 d-flex align-items-end justify-content-end">
                            <a href="{{ route('inventory.products.create') }}" class="btn btn-cyan btn-color-white text-nowrap">
                                <i class="ri-add-fill"></i><span>Thêm mới</span>
                            </a>
                        </div>
                    </div>
                </div>

                <div class="card-body">
            <table class="table table-light-custom align-middle table-nowrap mb-0">
                <thead class="table-light text-muted"><tr class="text-center"><th>SKU</th><th>Barcode</th><th>Tên</th><th>Đơn vị</th><th>Trạng thái</th><th style="width: 15%">Thao tác</th></tr></thead>
                <tbody>
                @forelse($products as $product)
                    <tr>
                        <td>{{ $product->sku }}</td>
                        <td>{{ $product->barcode }}</td>
                        <td>{{ $product->name }}</td>
                        <td>{{ $product->unit }}</td>
                        <td>@include('inventory.partials.status', ['status' => $product->status])</td>
                        <td class="d-flex align-items-center justify-content-center gap-3">
                            <a class="icon-edit icon-action" href="{{ route('inventory.products.edit', $product->id) }}" title="Sửa">
                                <i class="ri-edit-2-fill"></i>
                            </a>
                            <form method="POST" action="{{ route('inventory.products.destroy', $product->id) }}" class="d-inline" onsubmit="return confirm('Xóa sản phẩm này?')">
                                @csrf @method('DELETE')
                                <button class="icon-delete icon-action border-0 bg-transparent" title="Xóa"><i class="mdi mdi-delete"></i></button>
                            </form>
                        </td>
                    </tr>
                @empty
                    <tr><td colspan="6" class="text-center text-muted">Chưa có sản phẩm.</td></tr>
                @endforelse
                </tbody>
            </table>
                </div>
                <div class="d-flex justify-content-end mt-2 pe-3">{{ $products->links() }}</div>
            </div>
        </div>
    </div>
@endsection

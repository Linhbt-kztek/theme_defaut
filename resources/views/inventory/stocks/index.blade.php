@extends('inventory.layout')

@section('title', 'Tồn kho')

@section('inventory_content')
    <div class="page-title-box d-sm-flex align-items-center justify-content-between">
        <div class="page-title-left">
            <ol class="breadcrumb m-0">
                <li class="breadcrumb-item"><a href="{{ route('inventory.dashboard') }}"><b>Quản lý kho</b></a></li>
                <li class="breadcrumb-item active"><b>Tồn kho</b></li>
            </ol>
        </div>
    </div>

    <form class="card card-body border border-dashed border-start-0 border-end-0 mb-3" method="GET">
        <div class="row g-3 align-items-end">
            <div class="col-md-4">
                <input name="keyword" class="form-control" placeholder="Tìm theo sản phẩm hoặc kho" value="{{ $filters['keyword'] ?? '' }}">
            </div>
            <div class="col-md-3">
                <select name="product_id[]" class="form-select inventory-select2" multiple data-placeholder="Tất cả sản phẩm">
                    @foreach($products as $product)
                        <option value="{{ $product->id }}" @selected(in_array((string) $product->id, array_map('strval', (array) ($filters['product_id'] ?? []))))>{{ $product->sku }} - {{ $product->name }}</option>
                    @endforeach
                </select>
            </div>
            <div class="col-md-3">
                <select name="warehouse_id[]" class="form-select inventory-select2" multiple data-placeholder="Tất cả kho">
                    @foreach($warehouses as $warehouse)
                        <option value="{{ $warehouse->id }}" @selected(in_array((string) $warehouse->id, array_map('strval', (array) ($filters['warehouse_id'] ?? []))))>{{ $warehouse->code }} - {{ $warehouse->name }}</option>
                    @endforeach
                </select>
            </div>
            <div class="col-md-2">
                <button class="btn waves-effect waves-light w-100" style="background-color:#1e2b37;color:white; border-radius: 8px">Lọc</button>
            </div>
        </div>
    </form>

    <div class="card">
        <div class="table-responsive">
            <table class="table table-light-custom align-middle table-nowrap mb-0">
                <thead class="table-light text-muted"><tr class="text-center"><th>Sản phẩm</th><th>Kho</th><th class="text-end">Số lượng tồn</th><th class="text-end">Số lượng khả dụng</th></tr></thead>
                <tbody>
                @forelse($stocks as $stock)
                    <tr>
                        <td>{{ optional($stock->product)->name }} <span class="text-muted">({{ optional($stock->product)->sku }})</span></td>
                        <td>{{ optional($stock->warehouse)->name }} <span class="text-muted">({{ optional($stock->warehouse)->code }})</span></td>
                        <td class="text-end">{{ number_format($stock->quantity, 2) }}</td>
                        <td class="text-end">{{ number_format($stock->available_quantity, 2) }}</td>
                    </tr>
                @empty
                    <tr><td colspan="4" class="text-center text-muted">Chưa có tồn kho.</td></tr>
                @endforelse
                </tbody>
            </table>
        </div>
        <div class="d-flex justify-content-end mt-2 pe-3">{{ $stocks->links() }}</div>
    </div>
@endsection

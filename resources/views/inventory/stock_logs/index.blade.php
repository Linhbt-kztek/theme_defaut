@extends('inventory.layout')

@section('title', 'Lịch sử tồn kho')

@section('inventory_content')
    <div class="page-title-box d-sm-flex align-items-center justify-content-between">
        <div class="page-title-left">
            <ol class="breadcrumb m-0">
                <li class="breadcrumb-item"><a href="{{ route('inventory.dashboard') }}"><b>Quản lý kho</b></a></li>
                <li class="breadcrumb-item active"><b>Lịch sử tồn kho</b></li>
            </ol>
        </div>
    </div>

    <form class="card card-body border border-dashed border-start-0 border-end-0 mb-3" method="GET">
        <div class="row g-3 align-items-end">
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
                <select name="type" class="form-select">
                    <option value="">Tất cả thao tác</option>
                    <option value="import" @selected(($filters['type'] ?? '') === 'import')>Nhập kho</option>
                    <option value="export" @selected(($filters['type'] ?? '') === 'export')>Xuất kho</option>
                </select>
            </div>
            <div class="col-md-2"><input type="date" name="from_date" class="form-control" value="{{ $filters['from_date'] ?? '' }}"></div>
            <div class="col-md-2"><input type="date" name="to_date" class="form-control" value="{{ $filters['to_date'] ?? '' }}"></div>
            <div class="col-md-2"><button class="btn waves-effect waves-light w-100" style="background-color:#1e2b37;color:white; border-radius: 8px">Lọc</button></div>
        </div>
    </form>

    <div class="card">
        <div class="table-responsive">
            <table class="table table-light-custom align-middle table-nowrap mb-0">
                <thead class="table-light text-muted"><tr class="text-center"><th>Thời gian</th><th>Loại</th><th>Sản phẩm</th><th>Kho</th><th class="text-end">Tồn trước</th><th class="text-end">Thay đổi</th><th class="text-end">Tồn sau</th></tr></thead>
                <tbody>
                @forelse($stockLogs as $log)
                    <tr>
                        <td>{{ $log->created_at->format('d/m/Y H:i') }}</td>
                        <td><span class="badge {{ $log->type === 'import' ? 'bg-success' : 'bg-warning text-dark' }}">{{ $log->type === 'import' ? 'Nhập kho' : 'Xuất kho' }}</span></td>
                        <td>{{ optional($log->product)->name }} <span class="text-muted">({{ optional($log->product)->sku }})</span></td>
                        <td>{{ optional($log->warehouse)->name }}</td>
                        <td class="text-end">{{ number_format($log->quantity_before, 2) }}</td>
                        <td class="text-end">{{ number_format($log->quantity_change, 2) }}</td>
                        <td class="text-end">{{ number_format($log->quantity_after, 2) }}</td>
                    </tr>
                @empty
                    <tr><td colspan="7" class="text-center text-muted">Chưa có lịch sử tồn kho.</td></tr>
                @endforelse
                </tbody>
            </table>
        </div>
        <div class="d-flex justify-content-end mt-2 pe-3">{{ $stockLogs->links() }}</div>
    </div>
@endsection

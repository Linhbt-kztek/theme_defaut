@extends('inventory.layout')

@section('title', 'Tạo phiếu nhập')

@section('inventory_content')
    <div class="page-title-box d-sm-flex align-items-center justify-content-between">
        <div class="page-title-left">
            <ol class="breadcrumb m-0">
                <li class="breadcrumb-item"><a href="{{ route('inventory.dashboard') }}"><b>Quản lý kho</b></a></li>
                <li class="breadcrumb-item"><a href="{{ route('inventory.import-receipts.index') }}"><b>Phiếu nhập</b></a></li>
                <li class="breadcrumb-item active"><b>Tạo phiếu</b></li>
            </ol>
        </div>
    </div>

    <form method="POST" action="{{ route('inventory.import-receipts.store') }}" class="card">
        @csrf
        <div class="card-body border border-dashed border-start-0 border-end-0">
            <div class="row g-3 mb-3">
                <div class="col-md-4">
                    <label class="form-label">Mã phiếu nhập</label>
                    <input name="code" class="form-control" value="{{ old('code', $defaultCode) }}" required>
                </div>
                <div class="col-md-4">
                    <label class="form-label">Kho nhập</label>
                    <select name="warehouse_id" class="form-select inventory-select2" data-placeholder="Chọn kho" required>
                        <option value="">Chọn kho</option>
                        @foreach($warehouses as $warehouse)
                            <option value="{{ $warehouse->id }}" @selected((string) old('warehouse_id') === (string) $warehouse->id)>{{ $warehouse->code }} - {{ $warehouse->name }}</option>
                        @endforeach
                    </select>
                </div>
                <div class="col-md-4">
                    <label class="form-label">Ghi chú</label>
                    <input name="note" class="form-control" value="{{ old('note') }}">
                </div>
            </div>
            @include('inventory.partials.receipt_items_form', ['type' => 'import'])
            <div class="mt-3 d-flex gap-2">
                <button class="btn btn-cyan btn-color-white">Lưu phiếu</button>
                <a href="{{ route('inventory.import-receipts.index') }}" class="btn btn-light">Quay lại</a>
            </div>
        </div>
    </form>
@endsection

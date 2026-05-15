@extends('inventory.layout')

@section('title', 'Chi tiết phiếu nhập')

@section('inventory_content')
    <div class="page-title-box d-sm-flex align-items-center justify-content-between">
        <div class="page-title-left">
            <ol class="breadcrumb m-0">
                <li class="breadcrumb-item"><a href="{{ route('inventory.dashboard') }}"><b>Quản lý kho</b></a></li>
                <li class="breadcrumb-item"><a href="{{ route('inventory.import-receipts.index') }}"><b>Phiếu nhập</b></a></li>
                <li class="breadcrumb-item active"><b>{{ $receipt->code }}</b></li>
            </ol>
        </div>
        <div class="d-flex gap-2">
            @if($receipt->status === 'draft')
                <form method="POST" action="{{ route('inventory.import-receipts.complete', $receipt->id) }}">@csrf <button class="btn btn-success">Hoàn tất</button></form>
                <form method="POST" action="{{ route('inventory.import-receipts.cancel', $receipt->id) }}" onsubmit="return confirm('Hủy phiếu nhập này?')">@csrf <button class="btn btn-outline-danger">Hủy</button></form>
            @endif
            <a href="{{ route('inventory.import-receipts.index') }}" class="btn btn-light">Quay lại</a>
        </div>
    </div>

    <div class="card mb-3">
        <div class="card-body border border-dashed border-start-0 border-end-0">
            <div class="row g-3">
                <div class="col-md-4"><strong>Kho nhập:</strong> {{ optional($receipt->warehouse)->name }}</div>
                <div class="col-md-4"><strong>Trạng thái:</strong> @include('inventory.partials.receipt_status', ['status' => $receipt->status])</div>
                <div class="col-md-4"><strong>Ghi chú:</strong> {{ $receipt->note }}</div>
            </div>
        </div>
    </div>

    <div class="card">
        <div class="table-responsive">
            <table class="table table-light-custom align-middle table-nowrap mb-0">
                <thead class="table-light text-muted">
                <tr class="text-center"><th>Sản phẩm</th><th class="text-end">Số lượng</th><th class="text-end">Giá nhập</th></tr>
                </thead>
                <tbody>
                @foreach($receipt->items as $item)
                    <tr>
                        <td>{{ optional($item->product)->name }} <span class="text-muted">({{ optional($item->product)->sku }})</span></td>
                        <td class="text-end">{{ number_format($item->quantity, 2) }}</td>
                        <td class="text-end">{{ number_format($item->import_price, 2) }}</td>
                    </tr>
                @endforeach
                </tbody>
            </table>
        </div>
    </div>
@endsection

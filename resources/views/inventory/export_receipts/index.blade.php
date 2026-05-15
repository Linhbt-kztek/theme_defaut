@extends('inventory.layout')

@section('title', 'Phiếu xuất')

@section('inventory_content')
    <div class="page-title-box d-sm-flex align-items-center justify-content-between">
        <div class="page-title-left">
            <ol class="breadcrumb m-0">
                <li class="breadcrumb-item"><a href="{{ route('inventory.dashboard') }}"><b>Quản lý kho</b></a></li>
                <li class="breadcrumb-item active"><b>Phiếu xuất</b></li>
            </ol>
        </div>
    </div>

    <div class="card" id="export-receipt-list">
        <div class="card-header">
            <div class="row">
                <div class="col-md-9">
                    <form method="GET">
                        <div class="row g-3 mb-0 align-items-end">
                            <div class="col-md-auto" style="width: 400px;">
                                <div class="floating-label-group">
                                    <label class="floating-label">Từ khóa</label>
                                    <div class="form-icon form-icon-custom w-100">
                                        <input name="keyword" class="form-control-icon form-control-custom" placeholder="Mã phiếu | kho | ghi chú" value="{{ $keyword }}">
                                        <i class="ri-search-line text-orange icon"></i>
                                    </div>
                                </div>
                            </div>
                            <div class="col-sm-auto"><button class="btn waves-effect waves-light" style="background-color:#1e2b37;color:white; border-radius: 8px">Tìm kiếm</button></div>
                        </div>
                    </form>
                </div>
                <div class="col-md-3 d-flex align-items-end justify-content-end">
                    <a href="{{ route('inventory.export-receipts.create') }}" class="btn btn-cyan btn-color-white text-nowrap"><i class="ri-add-fill"></i><span>Tạo phiếu</span></a>
                </div>
            </div>
        </div>
        <div class="card-body">
            <table class="table table-light-custom align-middle table-nowrap mb-0">
                <thead class="table-light text-muted"><tr class="text-center"><th>Mã phiếu</th><th>Kho xuất</th><th>Trạng thái</th><th>Ngày tạo</th><th style="width: 12%">Thao tác</th></tr></thead>
                <tbody>
                @forelse($receipts as $receipt)
                    <tr>
                        <td>{{ $receipt->code }}</td>
                        <td>{{ optional($receipt->warehouse)->name }}</td>
                        <td>@include('inventory.partials.receipt_status', ['status' => $receipt->status])</td>
                        <td>{{ $receipt->created_at->format('d/m/Y H:i') }}</td>
                        <td class="text-center"><a class="icon-edit icon-action" href="{{ route('inventory.export-receipts.show', $receipt->id) }}" title="Chi tiết"><i class="ri-eye-line"></i></a></td>
                    </tr>
                @empty
                    <tr><td colspan="5" class="text-center text-muted">Chưa có phiếu xuất.</td></tr>
                @endforelse
                </tbody>
            </table>
        </div>
        <div class="d-flex justify-content-end mt-2 pe-3">{{ $receipts->links() }}</div>
    </div>
@endsection

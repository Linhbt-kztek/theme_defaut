@extends('inventory.layout')

@section('title', 'Sửa kho')

@section('inventory_content')
    <div class="page-title-box d-sm-flex align-items-center justify-content-between">
        <div class="page-title-left">
            <ol class="breadcrumb m-0">
                <li class="breadcrumb-item"><a href="{{ route('inventory.dashboard') }}"><b>Quản lý kho</b></a></li>
                <li class="breadcrumb-item"><a href="{{ route('inventory.warehouses.index') }}"><b>Kho hàng</b></a></li>
                <li class="breadcrumb-item active"><b>Sửa</b></li>
            </ol>
        </div>
    </div>
    <div class="card"><div class="card-body border border-dashed border-start-0 border-end-0">
        <form method="POST" action="{{ route('inventory.warehouses.update', $warehouse->id) }}">
            @method('PUT')
            @include('inventory.warehouses._form', ['warehouse' => $warehouse])
        </form>
    </div></div>
@endsection

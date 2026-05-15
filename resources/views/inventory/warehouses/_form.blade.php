@csrf
<div class="row g-3">
    <div class="col-md-4">
        <label class="form-label">Mã kho</label>
        <input name="code" class="form-control" value="{{ old('code', $warehouse->code ?? '') }}" required>
    </div>
    <div class="col-md-5">
        <label class="form-label">Tên kho</label>
        <input name="name" class="form-control" value="{{ old('name', $warehouse->name ?? '') }}" required>
    </div>
    <div class="col-md-3">
        <label class="form-label">Trạng thái</label>
        <select name="status" class="form-select">
            <option value="1" @selected((string) old('status', $warehouse->status ?? 1) === '1')>Hoạt động</option>
            <option value="0" @selected((string) old('status', $warehouse->status ?? 1) === '0')>Ngừng</option>
        </select>
    </div>
    <div class="col-12">
        <label class="form-label">Địa chỉ</label>
        <input name="address" class="form-control" value="{{ old('address', $warehouse->address ?? '') }}">
    </div>
</div>

<div class="mt-3 d-flex gap-2">
    <button class="btn btn-cyan btn-color-white">Lưu</button>
    <a href="{{ route('inventory.warehouses.index') }}" class="btn btn-light">Quay lại</a>
</div>

@csrf
<div class="row g-3">
    <div class="col-md-4">
        <label class="form-label">Mã sản phẩm / SKU</label>
        <input name="sku" class="form-control" value="{{ old('sku', $product->sku ?? '') }}" required>
    </div>
    <div class="col-md-4">
        <label class="form-label">Barcode</label>
        <input name="barcode" class="form-control" value="{{ old('barcode', $product->barcode ?? '') }}">
    </div>
    <div class="col-md-4">
        <label class="form-label">Đơn vị tính</label>
        <input name="unit" class="form-control" value="{{ old('unit', $product->unit ?? 'pcs') }}" required>
    </div>
    <div class="col-md-8">
        <label class="form-label">Tên sản phẩm</label>
        <input name="name" class="form-control" value="{{ old('name', $product->name ?? '') }}" required>
    </div>
    <div class="col-md-4">
        <label class="form-label">Trạng thái</label>
        <select name="status" class="form-select">
            <option value="1" @selected((string) old('status', $product->status ?? 1) === '1')>Hoạt động</option>
            <option value="0" @selected((string) old('status', $product->status ?? 1) === '0')>Ngừng</option>
        </select>
    </div>
    <div class="col-12">
        <label class="form-label">Mô tả</label>
        <textarea name="description" class="form-control" rows="4">{{ old('description', $product->description ?? '') }}</textarea>
    </div>
</div>

<div class="mt-3 d-flex gap-2">
    <button class="btn btn-cyan btn-color-white">Lưu</button>
    <a href="{{ route('inventory.products.index') }}" class="btn btn-light">Quay lại</a>
</div>

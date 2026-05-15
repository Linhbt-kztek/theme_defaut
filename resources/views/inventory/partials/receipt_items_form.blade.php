<div class="table-responsive">
    <table class="table table-bordered align-middle" id="items-table">
        <thead>
        <tr>
            <th>Sản phẩm</th>
            <th style="width: 160px">Số lượng</th>
            @if($type === 'import')
                <th style="width: 180px">Giá nhập</th>
            @endif
            <th style="width: 70px"></th>
        </tr>
        </thead>
        <tbody>
        @php($oldItems = old('items', [['product_id' => '', 'quantity' => '', 'import_price' => '']]))
        @foreach($oldItems as $index => $item)
            <tr>
                <td>
                    <select name="items[{{ $index }}][product_id]" class="form-select inventory-select2 receipt-product-select" data-placeholder="Chọn sản phẩm">
                        <option value="">Chọn sản phẩm</option>
                        @foreach($products as $product)
                            <option value="{{ $product->id }}" @selected((string) ($item['product_id'] ?? '') === (string) $product->id)>
                                {{ $product->sku }} - {{ $product->name }}
                            </option>
                        @endforeach
                    </select>
                </td>
                <td><input type="number" step="0.01" min="0.01" name="items[{{ $index }}][quantity]" class="form-control text-end" value="{{ $item['quantity'] ?? '' }}"></td>
                @if($type === 'import')
                    <td><input type="number" step="0.01" min="0" name="items[{{ $index }}][import_price]" class="form-control text-end" value="{{ $item['import_price'] ?? 0 }}"></td>
                @endif
                <td class="text-center"><button type="button" class="btn btn-sm btn-outline-danger remove-row">Xóa</button></td>
            </tr>
        @endforeach
        </tbody>
    </table>
</div>
<button type="button" class="btn btn-outline-secondary" id="add-row">Thêm dòng</button>

@section('script')
    <script>
        (function () {
            const tableBody = document.querySelector('#items-table tbody');
            const addButton = document.querySelector('#add-row');
            let rowIndex = tableBody.querySelectorAll('tr').length;

            addButton.addEventListener('click', function () {
                const firstRow = tableBody.querySelector('tr');
                const clone = firstRow.cloneNode(true);
                clone.querySelectorAll('.select2-container').forEach(function (container) {
                    container.remove();
                });
                clone.querySelectorAll('select, input').forEach(function (input) {
                    input.name = input.name.replace(/items\[\d+]/, 'items[' + rowIndex + ']');
                    input.value = input.tagName === 'SELECT' ? '' : '';
                    input.removeAttribute('data-select2-id');
                    input.classList.remove('select2-hidden-accessible');
                    input.removeAttribute('aria-hidden');
                    input.removeAttribute('tabindex');
                });
                tableBody.appendChild(clone);
                $(clone).find('.inventory-select2').select2({
                    width: '100%',
                    allowClear: true,
                    placeholder: function () {
                        return $(this).data('placeholder') || 'Lựa chọn';
                    }
                });
                rowIndex++;
            });

            tableBody.addEventListener('click', function (event) {
                if (!event.target.classList.contains('remove-row')) {
                    return;
                }

                if (tableBody.querySelectorAll('tr').length > 1) {
                    event.target.closest('tr').remove();
                }
            });
        })();
    </script>
@endsection

@extends('inventory.layout')

@section('title', 'Dashboard kho')

@section('inventory_content')
    <div class="page-title-box d-sm-flex align-items-center justify-content-between">
        <div class="page-title-left">
            <ol class="breadcrumb m-0">
                <li class="breadcrumb-item active"><b>Dashboard kho</b></li>
            </ol>
        </div>
    </div>

    <div class="row g-3 mb-4">
        <div class="col-md-3">
            <div class="card">
                <div class="card-body">
                    <div class="d-flex justify-content-between">
                        <div>
                            <p class="text-muted mb-1">Tổng sản phẩm</p>
                            <h3 class="mb-0">{{ $totalProducts }}</h3>
                        </div>
                        <div class="avatar-sm">
                            <span class="avatar-title bg-soft-primary text-primary rounded-3">
                                <i class="ri-box-3-line fs-22"></i>
                            </span>
                        </div>
                    </div>
                </div>
            </div>
        </div>
        <div class="col-md-3">
            <div class="card">
                <div class="card-body">
                    <div class="d-flex justify-content-between">
                        <div>
                            <p class="text-muted mb-1">Tổng kho</p>
                            <h3 class="mb-0">{{ $totalWarehouses }}</h3>
                        </div>
                        <div class="avatar-sm">
                            <span class="avatar-title bg-soft-success text-success rounded-3">
                                <i class="ri-store-2-line fs-22"></i>
                            </span>
                        </div>
                    </div>
                </div>
            </div>
        </div>
        <div class="col-md-3">
            <div class="card">
                <div class="card-body">
                    <div class="d-flex justify-content-between">
                        <div>
                            <p class="text-muted mb-1">Phiếu nhập</p>
                            <h3 class="mb-0">{{ $totalImportReceipts }}</h3>
                        </div>
                        <div class="avatar-sm">
                            <span class="avatar-title bg-soft-info text-info rounded-3">
                                <i class="ri-download-2-line fs-22"></i>
                            </span>
                        </div>
                    </div>
                </div>
            </div>
        </div>
        <div class="col-md-3">
            <div class="card">
                <div class="card-body">
                    <div class="d-flex justify-content-between">
                        <div>
                            <p class="text-muted mb-1">Phiếu xuất</p>
                            <h3 class="mb-0">{{ $totalExportReceipts }}</h3>
                        </div>
                        <div class="avatar-sm">
                            <span class="avatar-title bg-soft-warning text-warning rounded-3">
                                <i class="ri-upload-2-line fs-22"></i>
                            </span>
                        </div>
                    </div>
                </div>
            </div>
        </div>
    </div>

    <div class="row">
        <div class="col-xl-8">
            <div class="card">
                <div class="card-header">
                    <h5 class="card-title mb-0">Xu hướng nhập xuất 7 ngày</h5>
                </div>
                <div class="card-body">
                    <div id="inventory-movement-chart" style="min-height: 340px;"></div>
                </div>
            </div>
        </div>
        <div class="col-xl-4">
            <div class="card">
                <div class="card-header">
                    <h5 class="card-title mb-0">Tồn khả dụng theo kho</h5>
                </div>
                <div class="card-body">
                    <div id="warehouse-stock-chart" style="min-height: 340px;"></div>
                </div>
            </div>
        </div>
    </div>

    <div class="row">
        <div class="col-xl-6">
            <div class="card">
                <div class="card-header d-flex align-items-center justify-content-between">
                    <h5 class="card-title mb-0">Số lượng hàng tồn kho</h5>
                    <div class="d-flex align-items-center gap-2">
                        <select id="stock-warehouse-filter" class="form-select form-select-sm inventory-select2" multiple data-placeholder="Tất cả kho" style="min-width: 220px">
                            @foreach ($warehouses as $warehouse)
                                <option value="{{ $warehouse->id }}" @selected(in_array((string) $warehouse->id, array_map('strval', $selectedWarehouseIds ?? [])))>
                                    {{ $warehouse->code }} - {{ $warehouse->name }}
                                </option>
                            @endforeach
                        </select>
                        <button type="button" id="stock-clear-filter" class="btn btn-sm btn-light text-nowrap {{ !empty($selectedWarehouseIds) ? '' : 'd-none' }}">
                            Bỏ lọc
                        </button>
                    </div>
                </div>
                <div class="card-body p-0">
                    <div class="table-responsive">
                        <table class="table table-sm table-light-custom align-middle table-nowrap mb-0">
                            <thead class="table-light text-muted">
                                <tr class="text-center">
                                    <th style="width: 34%">Sản phẩm</th>
                                    <th class="text-end">Tồn kho</th>
                                    <th class="text-end">Khả dụng</th>
                                    <th style="width: 34%">Tỷ lệ khả dụng</th>
                                </tr>
                            </thead>
                            <tbody id="stock-summary-body">
                                <tr>
                                    <td colspan="4" class="text-center text-muted">Đang tải dữ liệu...</td>
                                </tr>
                            </tbody>
                        </table>
                    </div>
                </div>
            </div>
        </div>
        <div class="col-xl-6">
            <div class="card">
                <div class="card-header d-flex align-items-center justify-content-between">
                    <h5 class="card-title mb-0">Sản phẩm sắp hết hàng</h5>
                    <span class="badge bg-soft-warning text-warning">Ngưỡng <= 10</span>
                </div>
                <div class="card-body">
                    <div id="low-stock-chart" style="min-height: 360px;"></div>
                </div>
            </div>
        </div>
    </div>
@endsection

@section('script')
    <script src="{{ url('assets/libs/apexcharts/apexcharts.min.js') }}"></script>
    <script>
        const inventoryChartData = @json($chartData);
        const stockSummaryUrl = "{{ route('inventory.stock-summary') }}";
        const stockWarehouseFilter = document.getElementById('stock-warehouse-filter');
        const stockClearFilter = document.getElementById('stock-clear-filter');
        const stockSummaryBody = document.getElementById('stock-summary-body');

        function formatInventoryNumber(value) {
            return Number(value || 0).toLocaleString('vi-VN', {
                minimumFractionDigits: 2,
                maximumFractionDigits: 2
            });
        }

        function escapeHtml(value) {
            return String(value ?? '')
                .replaceAll('&', '&amp;')
                .replaceAll('<', '&lt;')
                .replaceAll('>', '&gt;')
                .replaceAll('"', '&quot;')
                .replaceAll("'", '&#039;');
        }

        function progressClass(percent) {
            if (percent <= 25) return 'bg-danger';
            if (percent <= 50) return 'bg-warning';
            return 'bg-success';
        }

        function renderStockSummary(items) {
            if (!items.length) {
                stockSummaryBody.innerHTML = '<tr><td colspan="4" class="text-center text-muted">Chưa có dữ liệu tồn kho.</td></tr>';
                return;
            }

            stockSummaryBody.innerHTML = items.map(item => {
                const percent = Number(item.available_percent || 0);

                return `
                    <tr>
                        <td>
                            <div class="fw-semibold small">${escapeHtml(item.name)}</div>
                            <div class="text-muted" style="font-size: 11px;">${escapeHtml(item.sku)}</div>
                        </td>
                        <td class="text-end fw-semibold small">${formatInventoryNumber(item.total_quantity)}</td>
                        <td class="text-end small">${formatInventoryNumber(item.total_available)}</td>
                        <td>
                            <div class="d-flex align-items-center gap-2">
                                <div class="progress flex-grow-1" style="height: 6px;">
                                    <div class="progress-bar ${progressClass(percent)}" role="progressbar" style="width: ${percent}%;" aria-valuenow="${percent}" aria-valuemin="0" aria-valuemax="100"></div>
                                </div>
                                <span class="text-muted" style="width: 42px; font-size: 11px;">${percent}%</span>
                            </div>
                        </td>
                    </tr>
                `;
            }).join('');
        }

        async function loadStockSummary() {
            const warehouseIds = Array.from(stockWarehouseFilter.selectedOptions).map(option => option.value).filter(Boolean);
            const url = new URL(stockSummaryUrl, window.location.origin);

            if (warehouseIds.length) {
                warehouseIds.forEach(id => url.searchParams.append('warehouse_id[]', id));
                stockClearFilter.classList.remove('d-none');
            } else {
                stockClearFilter.classList.add('d-none');
            }

            stockSummaryBody.innerHTML = '<tr><td colspan="4" class="text-center text-muted">Đang tải dữ liệu...</td></tr>';

            try {
                const response = await fetch(url.toString(), {
                    headers: { 'Accept': 'application/json' }
                });
                const result = await response.json();
                renderStockSummary(result.data || []);
            } catch (error) {
                stockSummaryBody.innerHTML = '<tr><td colspan="4" class="text-center text-danger">Không tải được dữ liệu tồn kho.</td></tr>';
            }
        }

        $(stockWarehouseFilter).on('change', loadStockSummary);
        stockClearFilter.addEventListener('click', function () {
            $(stockWarehouseFilter).val(null).trigger('change');
        });
        loadStockSummary();

        new ApexCharts(document.querySelector("#inventory-movement-chart"), {
            chart: { type: 'area', height: 340, toolbar: { show: false } },
            series: [
                { name: 'Nhập kho', data: inventoryChartData.importValues },
                { name: 'Xuất kho', data: inventoryChartData.exportValues }
            ],
            colors: ['#0ab39c', '#f7b84b'],
            dataLabels: { enabled: false },
            stroke: { curve: 'smooth', width: 2 },
            fill: { type: 'gradient', gradient: { shadeIntensity: 1, opacityFrom: 0.32, opacityTo: 0.08 } },
            xaxis: { categories: inventoryChartData.movementLabels },
            yaxis: { labels: { formatter: value => Number(value).toLocaleString('vi-VN') } },
            tooltip: { y: { formatter: value => Number(value).toLocaleString('vi-VN') } },
            legend: { position: 'top' }
        }).render();

        new ApexCharts(document.querySelector("#warehouse-stock-chart"), {
            chart: { type: 'donut', height: 340 },
            series: inventoryChartData.warehouseValues,
            labels: inventoryChartData.warehouseLabels,
            colors: ['#405189', '#0ab39c', '#f7b84b', '#f06548', '#299cdb'],
            legend: { position: 'bottom' },
            dataLabels: { formatter: value => value.toFixed(1) + '%' },
            tooltip: { y: { formatter: value => Number(value).toLocaleString('vi-VN') } }
        }).render();

        new ApexCharts(document.querySelector("#low-stock-chart"), {
            chart: { type: 'bar', height: 360, toolbar: { show: false } },
            series: [{ name: 'Khả dụng', data: inventoryChartData.lowStockValues }],
            colors: ['#f06548'],
            plotOptions: { bar: { horizontal: true, borderRadius: 4, barHeight: '58%' } },
            dataLabels: { enabled: true, formatter: value => Number(value).toLocaleString('vi-VN') },
            xaxis: { categories: inventoryChartData.lowStockLabels },
            tooltip: { y: { formatter: value => Number(value).toLocaleString('vi-VN') } }
        }).render();
    </script>
@endsection

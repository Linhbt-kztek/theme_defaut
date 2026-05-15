<?php

namespace App\Http\Controllers\Web\Inventory;

use App\Http\Controllers\Controller;
use App\Models\ExportReceipt;
use App\Models\ImportReceipt;
use App\Models\Product;
use App\Models\Stock;
use App\Models\StockLog;
use App\Models\Warehouse;
use Carbon\Carbon;
use Illuminate\Http\Request;

class DashboardController extends Controller
{
    public function index(Request $request)
    {
        $warehouseIds = array_filter((array) $request->get('warehouse_id', []));

        $lowStockProducts = Stock::with(['product', 'warehouse'])
            ->whereNull('deleted_at')
            ->when(!empty($warehouseIds), fn ($query) => $query->whereIn('warehouse_id', $warehouseIds))
            ->where('available_quantity', '<=', 10)
            ->orderBy('available_quantity')
            ->limit(10)
            ->get();

        $stockByWarehouse = Stock::query()
            ->join('warehouses', 'stocks.warehouse_id', '=', 'warehouses.id')
            ->whereNull('stocks.deleted_at')
            ->selectRaw('warehouses.name as warehouse_name, SUM(stocks.available_quantity) as total_available')
            ->groupBy('warehouses.id', 'warehouses.name')
            ->orderByDesc('total_available')
            ->get();

        $stockByProduct = Stock::query()
            ->join('products', 'stocks.product_id', '=', 'products.id')
            ->whereNull('stocks.deleted_at')
            ->when(!empty($warehouseIds), fn ($query) => $query->whereIn('stocks.warehouse_id', $warehouseIds))
            ->selectRaw('products.sku, products.name, SUM(stocks.quantity) as total_quantity, SUM(stocks.available_quantity) as total_available')
            ->groupBy('products.id', 'products.sku', 'products.name')
            ->orderByDesc('total_quantity')
            ->limit(15)
            ->get();

        $dates = collect(range(6, 0))->map(fn ($day) => Carbon::today()->subDays($day));
        $stockLogByDate = StockLog::query()
            ->whereDate('created_at', '>=', $dates->first()->toDateString())
            ->when(!empty($warehouseIds), fn ($query) => $query->whereIn('warehouse_id', $warehouseIds))
            ->selectRaw('DATE(created_at) as log_date, type, SUM(ABS(quantity_change)) as total_quantity')
            ->groupBy('log_date', 'type')
            ->get()
            ->groupBy(fn ($item) => $item->log_date . '_' . $item->type);

        return view('inventory.dashboard', [
            'totalProducts' => Product::whereNull('deleted_at')->count(),
            'totalWarehouses' => Warehouse::whereNull('deleted_at')->count(),
            'totalImportReceipts' => ImportReceipt::whereNull('deleted_at')->count(),
            'totalExportReceipts' => ExportReceipt::whereNull('deleted_at')->count(),
            'lowStockProducts' => $lowStockProducts,
            'stockByProduct' => $stockByProduct,
            'warehouses' => Warehouse::whereNull('deleted_at')->orderBy('name')->get(),
            'selectedWarehouseIds' => $warehouseIds,
            'chartData' => [
                'warehouseLabels' => $stockByWarehouse->pluck('warehouse_name')->values(),
                'warehouseValues' => $stockByWarehouse->pluck('total_available')->map(fn ($value) => (float) $value)->values(),
                'lowStockLabels' => $lowStockProducts->map(fn ($stock) => optional($stock->product)->sku . ' - ' . optional($stock->warehouse)->code)->values(),
                'lowStockValues' => $lowStockProducts->pluck('available_quantity')->map(fn ($value) => (float) $value)->values(),
                'movementLabels' => $dates->map(fn ($date) => $date->format('d/m'))->values(),
                'importValues' => $dates->map(fn ($date) => (float) optional(optional($stockLogByDate->get($date->toDateString() . '_import'))->first())->total_quantity)->values(),
                'exportValues' => $dates->map(fn ($date) => (float) optional(optional($stockLogByDate->get($date->toDateString() . '_export'))->first())->total_quantity)->values(),
            ],
        ]);
    }

    public function stockSummary(Request $request)
    {
        $warehouseIds = array_filter((array) $request->get('warehouse_id', []));

        $items = Stock::query()
            ->join('products', 'stocks.product_id', '=', 'products.id')
            ->whereNull('stocks.deleted_at')
            ->when(!empty($warehouseIds), fn ($query) => $query->whereIn('stocks.warehouse_id', $warehouseIds))
            ->selectRaw('products.sku, products.name, SUM(stocks.quantity) as total_quantity, SUM(stocks.available_quantity) as total_available')
            ->groupBy('products.id', 'products.sku', 'products.name')
            ->orderByDesc('total_quantity')
            ->limit(15)
            ->get()
            ->map(function ($item) {
                $totalQuantity = (float) $item->total_quantity;
                $totalAvailable = (float) $item->total_available;

                return [
                    'sku' => $item->sku,
                    'name' => $item->name,
                    'total_quantity' => $totalQuantity,
                    'total_available' => $totalAvailable,
                    'available_percent' => $totalQuantity > 0 ? min(100, round(($totalAvailable / $totalQuantity) * 100, 1)) : 0,
                ];
            });

        return response()->json([
            'data' => $items,
        ]);
    }
}

<?php

namespace App\Http\Controllers\Web\Inventory;

use App\Http\Controllers\Controller;
use App\Models\Product;
use App\Models\Warehouse;
use App\Repositories\StockLog\StockLogRepositoryInterface;
use Illuminate\Http\Request;

class StockLogController extends Controller
{
    public function __construct(private StockLogRepositoryInterface $stockLogs)
    {
    }

    public function index(Request $request)
    {
        $filters = $request->only(['product_id', 'warehouse_id', 'type', 'from_date', 'to_date']);

        return view('inventory.stock_logs.index', [
            'stockLogs' => $this->stockLogs->search($filters),
            'filters' => $filters,
            'products' => Product::whereNull('deleted_at')->orderBy('name')->get(),
            'warehouses' => Warehouse::whereNull('deleted_at')->orderBy('name')->get(),
        ]);
    }
}

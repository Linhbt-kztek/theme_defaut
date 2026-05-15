<?php

namespace App\Http\Controllers\Web\Inventory;

use App\Http\Controllers\Controller;
use App\Models\Product;
use App\Models\Warehouse;
use App\Repositories\Stock\StockRepositoryInterface;
use Illuminate\Http\Request;

class StockController extends Controller
{
    public function __construct(private StockRepositoryInterface $stocks)
    {
    }

    public function index(Request $request)
    {
        $filters = $request->only(['keyword', 'product_id', 'warehouse_id']);

        return view('inventory.stocks.index', [
            'stocks' => $this->stocks->search($filters),
            'filters' => $filters,
            'products' => Product::whereNull('deleted_at')->orderBy('name')->get(),
            'warehouses' => Warehouse::whereNull('deleted_at')->orderBy('name')->get(),
        ]);
    }
}

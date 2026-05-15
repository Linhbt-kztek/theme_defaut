<?php

namespace App\Repositories\Stock;

use App\Models\Stock;
use App\Repositories\BaseRepository;

class StockRepository extends BaseRepository implements StockRepositoryInterface
{
    public function getModel()
    {
        return Stock::class;
    }

    public function search(array $filters, $perPage = 15)
    {
        $keyword = trim((string) ($filters['keyword'] ?? ''));
        $productIds = array_filter((array) ($filters['product_id'] ?? []));
        $warehouseIds = array_filter((array) ($filters['warehouse_id'] ?? []));

        return $this->model->with(['product', 'warehouse'])
            ->whereNull('deleted_at')
            ->when($keyword !== '', function ($query) use ($keyword) {
                $query->where(function ($q) use ($keyword) {
                    $q->whereHas('product', function ($productQuery) use ($keyword) {
                        $productQuery->where('sku', 'like', "%{$keyword}%")
                            ->orWhere('barcode', 'like', "%{$keyword}%")
                            ->orWhere('name', 'like', "%{$keyword}%");
                    })->orWhereHas('warehouse', function ($warehouseQuery) use ($keyword) {
                        $warehouseQuery->where('code', 'like', "%{$keyword}%")
                            ->orWhere('name', 'like', "%{$keyword}%");
                    });
                });
            })
            ->when(!empty($productIds), fn ($query) => $query->whereIn('product_id', $productIds))
            ->when(!empty($warehouseIds), fn ($query) => $query->whereIn('warehouse_id', $warehouseIds))
            ->orderByDesc('updated_at')
            ->paginate($perPage)
            ->appends($filters);
    }
}

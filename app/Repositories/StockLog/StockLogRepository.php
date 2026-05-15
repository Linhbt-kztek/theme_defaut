<?php

namespace App\Repositories\StockLog;

use App\Models\StockLog;
use App\Repositories\BaseRepository;

class StockLogRepository extends BaseRepository implements StockLogRepositoryInterface
{
    public function getModel()
    {
        return StockLog::class;
    }

    public function search(array $filters, $perPage = 20)
    {
        $productIds = array_filter((array) ($filters['product_id'] ?? []));
        $warehouseIds = array_filter((array) ($filters['warehouse_id'] ?? []));

        return $this->model->with(['product', 'warehouse'])
            ->when(!empty($productIds), fn ($query) => $query->whereIn('product_id', $productIds))
            ->when(!empty($warehouseIds), fn ($query) => $query->whereIn('warehouse_id', $warehouseIds))
            ->when($filters['type'] ?? null, fn ($query, $value) => $query->where('type', $value))
            ->when($filters['from_date'] ?? null, fn ($query, $value) => $query->whereDate('created_at', '>=', $value))
            ->when($filters['to_date'] ?? null, fn ($query, $value) => $query->whereDate('created_at', '<=', $value))
            ->orderByDesc('created_at')
            ->paginate($perPage)
            ->appends($filters);
    }
}

<?php

namespace App\Repositories\Warehouse;

use App\Models\Warehouse;
use App\Repositories\BaseRepository;

class WarehouseRepository extends BaseRepository implements WarehouseRepositoryInterface
{
    public function getModel()
    {
        return Warehouse::class;
    }

    public function search($keyword, $perPage = 15)
    {
        $keyword = trim((string) $keyword);

        return $this->model->whereNull('deleted_at')
            ->when($keyword !== '', function ($query) use ($keyword) {
                $query->where(function ($q) use ($keyword) {
                    $q->where('code', 'like', "%{$keyword}%")
                        ->orWhere('name', 'like', "%{$keyword}%")
                        ->orWhere('address', 'like', "%{$keyword}%");
                });
            })
            ->orderByDesc('created_at')
            ->paginate($perPage)
            ->appends(['keyword' => $keyword]);
    }
}

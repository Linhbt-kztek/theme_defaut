<?php

namespace App\Repositories\Product;

use App\Models\Product;
use App\Repositories\BaseRepository;

class ProductRepository extends BaseRepository implements ProductRepositoryInterface
{
    public function getModel()
    {
        return Product::class;
    }

    public function search($keyword, $perPage = 15)
    {
        $keyword = trim((string) $keyword);

        return $this->model->whereNull('deleted_at')
            ->when($keyword !== '', function ($query) use ($keyword) {
                $query->where(function ($q) use ($keyword) {
                    $q->where('sku', 'like', "%{$keyword}%")
                        ->orWhere('barcode', 'like', "%{$keyword}%")
                        ->orWhere('name', 'like', "%{$keyword}%");
                });
            })
            ->orderByDesc('created_at')
            ->paginate($perPage)
            ->appends(['keyword' => $keyword]);
    }
}

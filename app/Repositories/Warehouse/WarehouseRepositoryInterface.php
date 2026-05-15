<?php

namespace App\Repositories\Warehouse;

use App\Repositories\BaseRepositoryInterface;

interface WarehouseRepositoryInterface extends BaseRepositoryInterface
{
    public function search($keyword, $perPage = 15);
}

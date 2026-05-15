<?php

namespace App\Repositories\StockLog;

use App\Repositories\BaseRepositoryInterface;

interface StockLogRepositoryInterface extends BaseRepositoryInterface
{
    public function search(array $filters, $perPage = 20);
}

<?php

namespace App\Repositories\Stock;

use App\Repositories\BaseRepositoryInterface;

interface StockRepositoryInterface extends BaseRepositoryInterface
{
    public function search(array $filters, $perPage = 15);
}

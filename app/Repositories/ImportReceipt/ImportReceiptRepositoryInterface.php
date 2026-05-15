<?php

namespace App\Repositories\ImportReceipt;

use App\Repositories\BaseRepositoryInterface;

interface ImportReceiptRepositoryInterface extends BaseRepositoryInterface
{
    public function search($keyword, $perPage = 15);

    public function createReceipt(array $data, array $items);

    public function complete($id);

    public function cancel($id);
}

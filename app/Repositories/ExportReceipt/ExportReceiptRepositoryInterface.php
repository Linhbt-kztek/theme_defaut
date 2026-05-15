<?php

namespace App\Repositories\ExportReceipt;

use App\Repositories\BaseRepositoryInterface;

interface ExportReceiptRepositoryInterface extends BaseRepositoryInterface
{
    public function search($keyword, $perPage = 15);

    public function createReceipt(array $data, array $items);

    public function complete($id);

    public function cancel($id);
}

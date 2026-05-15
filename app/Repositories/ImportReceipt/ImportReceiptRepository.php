<?php

namespace App\Repositories\ImportReceipt;

use App\Models\ImportReceipt;
use App\Models\Stock;
use App\Models\StockLog;
use App\Repositories\BaseRepository;
use Illuminate\Support\Facades\DB;
use Illuminate\Validation\ValidationException;

class ImportReceiptRepository extends BaseRepository implements ImportReceiptRepositoryInterface
{
    public function getModel()
    {
        return ImportReceipt::class;
    }

    public function search($keyword, $perPage = 15)
    {
        $keyword = trim((string) $keyword);

        return $this->model->with('warehouse')
            ->whereNull('deleted_at')
            ->when($keyword !== '', function ($query) use ($keyword) {
                $query->where(function ($q) use ($keyword) {
                    $q->where('code', 'like', "%{$keyword}%")
                        ->orWhere('note', 'like', "%{$keyword}%")
                        ->orWhereHas('warehouse', fn ($w) => $w->where('name', 'like', "%{$keyword}%"));
                });
            })
            ->orderByDesc('created_at')
            ->paginate($perPage)
            ->appends(['keyword' => $keyword]);
    }

    public function createReceipt(array $data, array $items)
    {
        return DB::transaction(function () use ($data, $items) {
            $receipt = $this->model->create($data);

            foreach ($items as $item) {
                $receipt->items()->create($item);
            }

            return $receipt;
        });
    }

    public function complete($id)
    {
        return DB::transaction(function () use ($id) {
            $receipt = $this->model->with('items')->lockForUpdate()->findOrFail($id);
            $this->ensureCompletable($receipt);

            foreach ($receipt->items as $item) {
                $stock = Stock::where('product_id', $item->product_id)
                    ->where('warehouse_id', $receipt->warehouse_id)
                    ->lockForUpdate()
                    ->first();

                if (!$stock) {
                    $stock = Stock::create([
                        'product_id' => $item->product_id,
                        'warehouse_id' => $receipt->warehouse_id,
                        'quantity' => 0,
                        'available_quantity' => 0,
                    ]);
                    $stock = Stock::whereKey($stock->id)->lockForUpdate()->first();
                }

                $before = (float) $stock->quantity;
                $change = (float) $item->quantity;
                $after = $before + $change;

                $stock->update([
                    'quantity' => $after,
                    'available_quantity' => (float) $stock->available_quantity + $change,
                ]);

                StockLog::create([
                    'product_id' => $item->product_id,
                    'warehouse_id' => $receipt->warehouse_id,
                    'type' => 'import',
                    'reference_type' => ImportReceipt::class,
                    'reference_id' => $receipt->id,
                    'quantity_before' => $before,
                    'quantity_change' => $change,
                    'quantity_after' => $after,
                ]);
            }

            $receipt->update([
                'status' => 'completed',
                'completed_at' => now(),
            ]);

            return $receipt;
        });
    }

    public function cancel($id)
    {
        return DB::transaction(function () use ($id) {
            $receipt = $this->model->lockForUpdate()->findOrFail($id);

            if ($receipt->status === 'completed') {
                throw ValidationException::withMessages(['receipt' => 'Phiếu nhập đã hoàn tất, không thể hủy.']);
            }

            if ($receipt->status === 'cancelled') {
                throw ValidationException::withMessages(['receipt' => 'Phiếu nhập đã hủy trước đó.']);
            }

            $receipt->update([
                'status' => 'cancelled',
                'cancelled_at' => now(),
            ]);

            return $receipt;
        });
    }

    private function ensureCompletable(ImportReceipt $receipt)
    {
        if ($receipt->status === 'completed') {
            throw ValidationException::withMessages(['receipt' => 'Phiếu nhập đã hoàn tất, không thể hoàn tất lại.']);
        }

        if ($receipt->status === 'cancelled') {
            throw ValidationException::withMessages(['receipt' => 'Phiếu nhập đã hủy, không thể hoàn tất.']);
        }

        if ($receipt->items->isEmpty()) {
            throw ValidationException::withMessages(['items' => 'Phiếu nhập cần có ít nhất một sản phẩm.']);
        }
    }
}

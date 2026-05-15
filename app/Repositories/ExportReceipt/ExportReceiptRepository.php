<?php

namespace App\Repositories\ExportReceipt;

use App\Models\ExportReceipt;
use App\Models\Stock;
use App\Models\StockLog;
use App\Repositories\BaseRepository;
use Illuminate\Support\Facades\DB;
use Illuminate\Validation\ValidationException;

class ExportReceiptRepository extends BaseRepository implements ExportReceiptRepositoryInterface
{
    public function getModel()
    {
        return ExportReceipt::class;
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
            $receipt = $this->model->with('items.product')->lockForUpdate()->findOrFail($id);
            $this->ensureCompletable($receipt);

            foreach ($receipt->items as $item) {
                $stock = Stock::where('product_id', $item->product_id)
                    ->where('warehouse_id', $receipt->warehouse_id)
                    ->lockForUpdate()
                    ->first();

                $available = $stock ? (float) $stock->available_quantity : 0;
                $change = (float) $item->quantity;

                if (!$stock || $available < $change) {
                    $name = optional($item->product)->name ?: 'Sản phẩm #' . $item->product_id;
                    throw ValidationException::withMessages([
                        'stock' => "Tồn kho không đủ cho {$name}. Khả dụng: {$available}, cần xuất: {$change}.",
                    ]);
                }

                $before = (float) $stock->quantity;
                $after = $before - $change;

                if ($after < 0) {
                    throw ValidationException::withMessages(['stock' => 'Không cho phép tồn kho âm.']);
                }

                $stock->update([
                    'quantity' => $after,
                    'available_quantity' => $available - $change,
                ]);

                StockLog::create([
                    'product_id' => $item->product_id,
                    'warehouse_id' => $receipt->warehouse_id,
                    'type' => 'export',
                    'reference_type' => ExportReceipt::class,
                    'reference_id' => $receipt->id,
                    'quantity_before' => $before,
                    'quantity_change' => -$change,
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
                throw ValidationException::withMessages(['receipt' => 'Phiếu xuất đã hoàn tất, không thể hủy.']);
            }

            if ($receipt->status === 'cancelled') {
                throw ValidationException::withMessages(['receipt' => 'Phiếu xuất đã hủy trước đó.']);
            }

            $receipt->update([
                'status' => 'cancelled',
                'cancelled_at' => now(),
            ]);

            return $receipt;
        });
    }

    private function ensureCompletable(ExportReceipt $receipt)
    {
        if ($receipt->status === 'completed') {
            throw ValidationException::withMessages(['receipt' => 'Phiếu xuất đã hoàn tất, không thể hoàn tất lại.']);
        }

        if ($receipt->status === 'cancelled') {
            throw ValidationException::withMessages(['receipt' => 'Phiếu xuất đã hủy, không thể hoàn tất.']);
        }

        if ($receipt->items->isEmpty()) {
            throw ValidationException::withMessages(['items' => 'Phiếu xuất cần có ít nhất một sản phẩm.']);
        }
    }
}

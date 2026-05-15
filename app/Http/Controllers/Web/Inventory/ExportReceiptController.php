<?php

namespace App\Http\Controllers\Web\Inventory;

use App\Http\Controllers\Controller;
use App\Models\Product;
use App\Models\Warehouse;
use App\Repositories\ExportReceipt\ExportReceiptRepositoryInterface;
use Illuminate\Http\Request;

class ExportReceiptController extends Controller
{
    public function __construct(private ExportReceiptRepositoryInterface $receipts)
    {
    }

    public function index(Request $request)
    {
        return view('inventory.export_receipts.index', [
            'receipts' => $this->receipts->search($request->keyword),
            'keyword' => $request->keyword,
        ]);
    }

    public function create()
    {
        return view('inventory.export_receipts.create', $this->formData());
    }

    public function store(Request $request)
    {
        $data = $request->validate([
            'code' => ['required', 'max:100', 'unique:export_receipts,code'],
            'warehouse_id' => ['required', 'exists:warehouses,id'],
            'note' => ['nullable'],
            'items' => ['required', 'array', 'min:1'],
            'items.*.product_id' => ['nullable', 'exists:products,id'],
            'items.*.quantity' => ['nullable', 'numeric', 'min:0.01'],
        ]);

        $items = $this->cleanItems($data['items']);
        if (empty($items)) {
            return back()->withInput()->withErrors(['items' => 'Cần nhập ít nhất một sản phẩm hợp lệ.']);
        }

        $receipt = $this->receipts->createReceipt([
            'code' => $data['code'],
            'warehouse_id' => $data['warehouse_id'],
            'note' => $data['note'] ?? null,
            'status' => 'draft',
        ], $items);

        return redirect()->route('inventory.export-receipts.show', $receipt->id)->with('success', 'Đã tạo phiếu xuất.');
    }

    public function show($id)
    {
        return view('inventory.export_receipts.show', [
            'receipt' => $this->receipts->getById($id, ['warehouse', 'items.product']),
        ]);
    }

    public function complete($id)
    {
        $this->receipts->complete($id);

        return back()->with('success', 'Đã hoàn tất phiếu xuất và trừ tồn kho.');
    }

    public function cancel($id)
    {
        $this->receipts->cancel($id);

        return back()->with('success', 'Đã hủy phiếu xuất.');
    }

    private function formData()
    {
        return [
            'products' => Product::whereNull('deleted_at')->where('status', 1)->orderBy('name')->get(),
            'warehouses' => Warehouse::whereNull('deleted_at')->where('status', 1)->orderBy('name')->get(),
            'defaultCode' => 'PX-' . now()->format('YmdHis'),
        ];
    }

    private function cleanItems(array $items)
    {
        $cleaned = [];

        foreach ($items as $item) {
            if (empty($item['product_id']) || empty($item['quantity'])) {
                continue;
            }

            $cleaned[] = [
                'product_id' => $item['product_id'],
                'quantity' => $item['quantity'],
            ];
        }

        return $cleaned;
    }
}

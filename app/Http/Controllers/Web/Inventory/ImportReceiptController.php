<?php

namespace App\Http\Controllers\Web\Inventory;

use App\Http\Controllers\Controller;
use App\Models\Product;
use App\Models\Warehouse;
use App\Repositories\ImportReceipt\ImportReceiptRepositoryInterface;
use Illuminate\Http\Request;

class ImportReceiptController extends Controller
{
    public function __construct(private ImportReceiptRepositoryInterface $receipts)
    {
    }

    public function index(Request $request)
    {
        return view('inventory.import_receipts.index', [
            'receipts' => $this->receipts->search($request->keyword),
            'keyword' => $request->keyword,
        ]);
    }

    public function create()
    {
        return view('inventory.import_receipts.create', $this->formData());
    }

    public function store(Request $request)
    {
        $data = $request->validate([
            'code' => ['required', 'max:100', 'unique:import_receipts,code'],
            'warehouse_id' => ['required', 'exists:warehouses,id'],
            'note' => ['nullable'],
            'items' => ['required', 'array', 'min:1'],
            'items.*.product_id' => ['nullable', 'exists:products,id'],
            'items.*.quantity' => ['nullable', 'numeric', 'min:0.01'],
            'items.*.import_price' => ['nullable', 'numeric', 'min:0'],
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

        return redirect()->route('inventory.import-receipts.show', $receipt->id)->with('success', 'Đã tạo phiếu nhập.');
    }

    public function show($id)
    {
        return view('inventory.import_receipts.show', [
            'receipt' => $this->receipts->getById($id, ['warehouse', 'items.product']),
        ]);
    }

    public function complete($id)
    {
        $this->receipts->complete($id);

        return back()->with('success', 'Đã hoàn tất phiếu nhập và cộng tồn kho.');
    }

    public function cancel($id)
    {
        $this->receipts->cancel($id);

        return back()->with('success', 'Đã hủy phiếu nhập.');
    }

    private function formData()
    {
        return [
            'products' => Product::whereNull('deleted_at')->where('status', 1)->orderBy('name')->get(),
            'warehouses' => Warehouse::whereNull('deleted_at')->where('status', 1)->orderBy('name')->get(),
            'defaultCode' => 'PN-' . now()->format('YmdHis'),
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
                'import_price' => $item['import_price'] ?? 0,
            ];
        }

        return $cleaned;
    }
}

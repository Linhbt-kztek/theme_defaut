<?php

namespace App\Http\Controllers\Web\Inventory;

use App\Http\Controllers\Controller;
use App\Repositories\Product\ProductRepositoryInterface;
use Illuminate\Http\Request;
use Illuminate\Validation\Rule;

class ProductController extends Controller
{
    public function __construct(private ProductRepositoryInterface $products)
    {
    }

    public function index(Request $request)
    {
        return view('inventory.products.index', [
            'products' => $this->products->search($request->keyword),
            'keyword' => $request->keyword,
        ]);
    }

    public function create()
    {
        return view('inventory.products.create');
    }

    public function store(Request $request)
    {
        $this->products->create($this->validatedData($request));

        return redirect()->route('inventory.products.index')->with('success', 'Đã thêm sản phẩm.');
    }

    public function edit($id)
    {
        return view('inventory.products.edit', [
            'product' => $this->products->getById($id),
        ]);
    }

    public function update(Request $request, $id)
    {
        $this->products->update($id, $this->validatedData($request, $id));

        return redirect()->route('inventory.products.index')->with('success', 'Đã cập nhật sản phẩm.');
    }

    public function destroy($id)
    {
        $this->products->delete($id);

        return redirect()->route('inventory.products.index')->with('success', 'Đã xóa sản phẩm.');
    }

    private function validatedData(Request $request, $id = null)
    {
        return $request->validate([
            'sku' => ['required', 'max:100', Rule::unique('products', 'sku')->ignore($id)->whereNull('deleted_at')],
            'barcode' => ['nullable', 'max:100', Rule::unique('products', 'barcode')->ignore($id)->whereNull('deleted_at')],
            'name' => ['required', 'max:255'],
            'unit' => ['required', 'max:50'],
            'description' => ['nullable'],
            'status' => ['required', 'in:0,1'],
        ]);
    }
}

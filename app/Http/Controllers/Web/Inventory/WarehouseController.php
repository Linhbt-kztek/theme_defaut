<?php

namespace App\Http\Controllers\Web\Inventory;

use App\Http\Controllers\Controller;
use App\Repositories\Warehouse\WarehouseRepositoryInterface;
use Illuminate\Http\Request;
use Illuminate\Validation\Rule;

class WarehouseController extends Controller
{
    public function __construct(private WarehouseRepositoryInterface $warehouses)
    {
    }

    public function index(Request $request)
    {
        return view('inventory.warehouses.index', [
            'warehouses' => $this->warehouses->search($request->keyword),
            'keyword' => $request->keyword,
        ]);
    }

    public function create()
    {
        return view('inventory.warehouses.create');
    }

    public function store(Request $request)
    {
        $this->warehouses->create($this->validatedData($request));

        return redirect()->route('inventory.warehouses.index')->with('success', 'Đã thêm kho.');
    }

    public function edit($id)
    {
        return view('inventory.warehouses.edit', [
            'warehouse' => $this->warehouses->getById($id),
        ]);
    }

    public function update(Request $request, $id)
    {
        $this->warehouses->update($id, $this->validatedData($request, $id));

        return redirect()->route('inventory.warehouses.index')->with('success', 'Đã cập nhật kho.');
    }

    public function destroy($id)
    {
        $this->warehouses->delete($id);

        return redirect()->route('inventory.warehouses.index')->with('success', 'Đã xóa kho.');
    }

    private function validatedData(Request $request, $id = null)
    {
        return $request->validate([
            'code' => ['required', 'max:100', Rule::unique('warehouses', 'code')->ignore($id)->whereNull('deleted_at')],
            'name' => ['required', 'max:255'],
            'address' => ['nullable', 'max:500'],
            'status' => ['required', 'in:0,1'],
        ]);
    }
}

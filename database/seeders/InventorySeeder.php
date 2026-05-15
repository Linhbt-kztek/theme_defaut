<?php

namespace Database\Seeders;

use Illuminate\Database\Seeder;
use Illuminate\Support\Facades\DB;

class InventorySeeder extends Seeder
{
    public function run()
    {
        DB::transaction(function () {
            $now = now();

            $products = [
                ['sku' => 'SEED-LAPTOP-001', 'barcode' => '893000000001', 'name' => 'Laptop Dell Latitude 5440', 'unit' => 'cái', 'description' => 'Laptop văn phòng cấu hình i5, RAM 16GB.', 'status' => 1],
                ['sku' => 'SEED-MOUSE-001', 'barcode' => '893000000002', 'name' => 'Chuột Logitech M331', 'unit' => 'cái', 'description' => 'Chuột không dây silent.', 'status' => 1],
                ['sku' => 'SEED-KEYBOARD-001', 'barcode' => '893000000003', 'name' => 'Bàn phím cơ Keychron K2', 'unit' => 'cái', 'description' => 'Bàn phím cơ bluetooth.', 'status' => 1],
                ['sku' => 'SEED-MONITOR-001', 'barcode' => '893000000004', 'name' => 'Màn hình LG 24 inch', 'unit' => 'cái', 'description' => 'Màn hình IPS Full HD.', 'status' => 1],
                ['sku' => 'SEED-CABLE-001', 'barcode' => '893000000005', 'name' => 'Cáp HDMI 2m', 'unit' => 'sợi', 'description' => 'Cáp HDMI 2 mét.', 'status' => 1],
                ['sku' => 'SEED-SSD-001', 'barcode' => '893000000006', 'name' => 'SSD Samsung 980 500GB', 'unit' => 'cái', 'description' => 'Ổ cứng SSD NVMe.', 'status' => 1],
                ['sku' => 'SEED-RAM-001', 'barcode' => '893000000007', 'name' => 'RAM Kingston 16GB DDR4', 'unit' => 'thanh', 'description' => 'RAM laptop DDR4.', 'status' => 1],
                ['sku' => 'SEED-PRINTER-001', 'barcode' => '893000000008', 'name' => 'Máy in Canon LBP 2900', 'unit' => 'cái', 'description' => 'Máy in laser trắng đen.', 'status' => 1],
            ];

            $warehouses = [
                ['code' => 'SEED-WH-HN', 'name' => 'Kho Hà Nội', 'address' => 'Cầu Giấy, Hà Nội', 'status' => 1],
                ['code' => 'SEED-WH-HCM', 'name' => 'Kho Hồ Chí Minh', 'address' => 'Quận 1, TP Hồ Chí Minh', 'status' => 1],
                ['code' => 'SEED-WH-DN', 'name' => 'Kho Đà Nẵng', 'address' => 'Hải Châu, Đà Nẵng', 'status' => 1],
            ];

            foreach ($products as $product) {
                DB::table('products')->updateOrInsert(
                    ['sku' => $product['sku']],
                    $product + ['is_delete' => 0, 'deleted_at' => null, 'created_at' => $now, 'updated_at' => $now]
                );
            }

            foreach ($warehouses as $warehouse) {
                DB::table('warehouses')->updateOrInsert(
                    ['code' => $warehouse['code']],
                    $warehouse + ['is_delete' => 0, 'deleted_at' => null, 'created_at' => $now, 'updated_at' => $now]
                );
            }

            $productIds = DB::table('products')->whereIn('sku', array_column($products, 'sku'))->pluck('id', 'sku');
            $warehouseIds = DB::table('warehouses')->whereIn('code', array_column($warehouses, 'code'))->pluck('id', 'code');

            $this->deleteOldSeedReceipts();
            $this->resetSeedStocks($productIds, $warehouseIds, $now);

            $this->createImportReceipt('PN-SEED-001', $warehouseIds['SEED-WH-HN'], 'Nhập hàng mẫu kho Hà Nội', [
                ['sku' => 'SEED-LAPTOP-001', 'quantity' => 20, 'import_price' => 18500000],
                ['sku' => 'SEED-MOUSE-001', 'quantity' => 150, 'import_price' => 245000],
                ['sku' => 'SEED-KEYBOARD-001', 'quantity' => 35, 'import_price' => 1450000],
                ['sku' => 'SEED-MONITOR-001', 'quantity' => 25, 'import_price' => 2850000],
                ['sku' => 'SEED-CABLE-001', 'quantity' => 220, 'import_price' => 65000],
            ], $productIds, $now->copy()->subDays(8));

            $this->createImportReceipt('PN-SEED-002', $warehouseIds['SEED-WH-HCM'], 'Nhập hàng mẫu kho Hồ Chí Minh', [
                ['sku' => 'SEED-SSD-001', 'quantity' => 60, 'import_price' => 1250000],
                ['sku' => 'SEED-RAM-001', 'quantity' => 75, 'import_price' => 980000],
                ['sku' => 'SEED-PRINTER-001', 'quantity' => 12, 'import_price' => 3200000],
                ['sku' => 'SEED-MOUSE-001', 'quantity' => 80, 'import_price' => 240000],
            ], $productIds, $now->copy()->subDays(6));

            $this->createImportReceipt('PN-SEED-003', $warehouseIds['SEED-WH-DN'], 'Nhập hàng mẫu kho Đà Nẵng', [
                ['sku' => 'SEED-CABLE-001', 'quantity' => 40, 'import_price' => 68000],
                ['sku' => 'SEED-MONITOR-001', 'quantity' => 8, 'import_price' => 2900000],
                ['sku' => 'SEED-KEYBOARD-001', 'quantity' => 6, 'import_price' => 1480000],
            ], $productIds, $now->copy()->subDays(4));

            $this->createExportReceipt('PX-SEED-001', $warehouseIds['SEED-WH-HN'], 'Xuất hàng mẫu cho phòng IT', [
                ['sku' => 'SEED-LAPTOP-001', 'quantity' => 5],
                ['sku' => 'SEED-MOUSE-001', 'quantity' => 30],
                ['sku' => 'SEED-CABLE-001', 'quantity' => 180],
                ['sku' => 'SEED-MONITOR-001', 'quantity' => 18],
            ], $productIds, $now->copy()->subDays(2));

            $this->createExportReceipt('PX-SEED-002', $warehouseIds['SEED-WH-HCM'], 'Xuất hàng mẫu cho chi nhánh', [
                ['sku' => 'SEED-SSD-001', 'quantity' => 18],
                ['sku' => 'SEED-RAM-001', 'quantity' => 22],
                ['sku' => 'SEED-PRINTER-001', 'quantity' => 5],
            ], $productIds, $now->copy()->subDay());
        });
    }

    private function deleteOldSeedReceipts()
    {
        $importIds = DB::table('import_receipts')->where('code', 'like', 'PN-SEED-%')->pluck('id');
        $exportIds = DB::table('export_receipts')->where('code', 'like', 'PX-SEED-%')->pluck('id');

        DB::table('stock_logs')
            ->where(function ($query) use ($importIds, $exportIds) {
                $query->where(function ($q) use ($importIds) {
                    $q->where('reference_type', 'App\\Models\\ImportReceipt')->whereIn('reference_id', $importIds);
                })->orWhere(function ($q) use ($exportIds) {
                    $q->where('reference_type', 'App\\Models\\ExportReceipt')->whereIn('reference_id', $exportIds);
                });
            })
            ->delete();

        DB::table('import_receipts')->whereIn('id', $importIds)->delete();
        DB::table('export_receipts')->whereIn('id', $exportIds)->delete();
    }

    private function resetSeedStocks($productIds, $warehouseIds, $now)
    {
        foreach ($productIds as $productId) {
            foreach ($warehouseIds as $warehouseId) {
                DB::table('stocks')->updateOrInsert(
                    ['product_id' => $productId, 'warehouse_id' => $warehouseId],
                    [
                        'quantity' => 0,
                        'available_quantity' => 0,
                        'is_delete' => 0,
                        'deleted_at' => null,
                        'created_at' => $now,
                        'updated_at' => $now,
                    ]
                );
            }
        }
    }

    private function createImportReceipt($code, $warehouseId, $note, array $items, $productIds, $createdAt)
    {
        $receiptId = DB::table('import_receipts')->insertGetId([
            'code' => $code,
            'warehouse_id' => $warehouseId,
            'note' => $note,
            'status' => 'completed',
            'completed_at' => $createdAt,
            'created_at' => $createdAt,
            'updated_at' => $createdAt,
        ]);

        foreach ($items as $item) {
            $productId = $productIds[$item['sku']];

            DB::table('import_receipt_items')->insert([
                'import_receipt_id' => $receiptId,
                'product_id' => $productId,
                'quantity' => $item['quantity'],
                'import_price' => $item['import_price'],
                'created_at' => $createdAt,
                'updated_at' => $createdAt,
            ]);

            $this->changeStock($productId, $warehouseId, $item['quantity'], 'import', 'App\\Models\\ImportReceipt', $receiptId, $createdAt);
        }
    }

    private function createExportReceipt($code, $warehouseId, $note, array $items, $productIds, $createdAt)
    {
        $receiptId = DB::table('export_receipts')->insertGetId([
            'code' => $code,
            'warehouse_id' => $warehouseId,
            'note' => $note,
            'status' => 'completed',
            'completed_at' => $createdAt,
            'created_at' => $createdAt,
            'updated_at' => $createdAt,
        ]);

        foreach ($items as $item) {
            $productId = $productIds[$item['sku']];

            DB::table('export_receipt_items')->insert([
                'export_receipt_id' => $receiptId,
                'product_id' => $productId,
                'quantity' => $item['quantity'],
                'created_at' => $createdAt,
                'updated_at' => $createdAt,
            ]);

            $this->changeStock($productId, $warehouseId, -$item['quantity'], 'export', 'App\\Models\\ExportReceipt', $receiptId, $createdAt);
        }
    }

    private function changeStock($productId, $warehouseId, $change, $type, $referenceType, $referenceId, $createdAt)
    {
        $stock = DB::table('stocks')
            ->where('product_id', $productId)
            ->where('warehouse_id', $warehouseId)
            ->first();

        $before = (float) $stock->quantity;
        $after = $before + (float) $change;

        DB::table('stocks')
            ->where('id', $stock->id)
            ->update([
                'quantity' => $after,
                'available_quantity' => $after,
                'updated_at' => $createdAt,
            ]);

        DB::table('stock_logs')->insert([
            'product_id' => $productId,
            'warehouse_id' => $warehouseId,
            'type' => $type,
            'reference_type' => $referenceType,
            'reference_id' => $referenceId,
            'quantity_before' => $before,
            'quantity_change' => $change,
            'quantity_after' => $after,
            'created_at' => $createdAt,
            'updated_at' => $createdAt,
        ]);
    }
}

<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\Schema;

return new class extends Migration
{
    public function up()
    {
        Schema::create('products', function (Blueprint $table) {
            $table->id();
            $table->string('sku')->unique();
            $table->string('barcode')->nullable()->unique();
            $table->string('name');
            $table->string('unit')->default('pcs');
            $table->text('description')->nullable();
            $table->tinyInteger('status')->default(1);
            $table->tinyInteger('is_delete')->default(0);
            $table->timestamps();
            $table->softDeletes();
        });

        Schema::create('warehouses', function (Blueprint $table) {
            $table->id();
            $table->string('code')->unique();
            $table->string('name');
            $table->string('address')->nullable();
            $table->tinyInteger('status')->default(1);
            $table->tinyInteger('is_delete')->default(0);
            $table->timestamps();
            $table->softDeletes();
        });

        Schema::create('stocks', function (Blueprint $table) {
            $table->id();
            $table->foreignId('product_id')->constrained('products')->cascadeOnDelete();
            $table->foreignId('warehouse_id')->constrained('warehouses')->cascadeOnDelete();
            $table->decimal('quantity', 15, 2)->default(0);
            $table->decimal('available_quantity', 15, 2)->default(0);
            $table->tinyInteger('is_delete')->default(0);
            $table->timestamps();
            $table->softDeletes();

            $table->unique(['product_id', 'warehouse_id']);
        });

        Schema::create('import_receipts', function (Blueprint $table) {
            $table->id();
            $table->string('code')->unique();
            $table->foreignId('warehouse_id')->constrained('warehouses')->restrictOnDelete();
            $table->text('note')->nullable();
            $table->string('status')->default('draft');
            $table->timestamp('completed_at')->nullable();
            $table->timestamp('cancelled_at')->nullable();
            $table->tinyInteger('is_delete')->default(0);
            $table->timestamps();
            $table->softDeletes();
        });

        Schema::create('import_receipt_items', function (Blueprint $table) {
            $table->id();
            $table->foreignId('import_receipt_id')->constrained('import_receipts')->cascadeOnDelete();
            $table->foreignId('product_id')->constrained('products')->restrictOnDelete();
            $table->decimal('quantity', 15, 2);
            $table->decimal('import_price', 15, 2)->default(0);
            $table->timestamps();
        });

        Schema::create('export_receipts', function (Blueprint $table) {
            $table->id();
            $table->string('code')->unique();
            $table->foreignId('warehouse_id')->constrained('warehouses')->restrictOnDelete();
            $table->text('note')->nullable();
            $table->string('status')->default('draft');
            $table->timestamp('completed_at')->nullable();
            $table->timestamp('cancelled_at')->nullable();
            $table->tinyInteger('is_delete')->default(0);
            $table->timestamps();
            $table->softDeletes();
        });

        Schema::create('export_receipt_items', function (Blueprint $table) {
            $table->id();
            $table->foreignId('export_receipt_id')->constrained('export_receipts')->cascadeOnDelete();
            $table->foreignId('product_id')->constrained('products')->restrictOnDelete();
            $table->decimal('quantity', 15, 2);
            $table->timestamps();
        });

        Schema::create('stock_logs', function (Blueprint $table) {
            $table->id();
            $table->foreignId('product_id')->constrained('products')->restrictOnDelete();
            $table->foreignId('warehouse_id')->constrained('warehouses')->restrictOnDelete();
            $table->string('type');
            $table->string('reference_type')->nullable();
            $table->unsignedBigInteger('reference_id')->nullable();
            $table->decimal('quantity_before', 15, 2);
            $table->decimal('quantity_change', 15, 2);
            $table->decimal('quantity_after', 15, 2);
            $table->timestamps();

            $table->index(['type', 'created_at']);
            $table->index(['reference_type', 'reference_id']);
        });
    }

    public function down()
    {
        Schema::dropIfExists('stock_logs');
        Schema::dropIfExists('export_receipt_items');
        Schema::dropIfExists('export_receipts');
        Schema::dropIfExists('import_receipt_items');
        Schema::dropIfExists('import_receipts');
        Schema::dropIfExists('stocks');
        Schema::dropIfExists('warehouses');
        Schema::dropIfExists('products');
    }
};

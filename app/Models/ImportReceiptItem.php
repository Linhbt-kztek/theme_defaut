<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;

class ImportReceiptItem extends Model
{
    use HasFactory;

    protected $fillable = [
        'import_receipt_id',
        'product_id',
        'quantity',
        'import_price',
    ];

    public function receipt()
    {
        return $this->belongsTo(ImportReceipt::class, 'import_receipt_id');
    }

    public function product()
    {
        return $this->belongsTo(Product::class);
    }
}

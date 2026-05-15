<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;

class ExportReceiptItem extends Model
{
    use HasFactory;

    protected $fillable = [
        'export_receipt_id',
        'product_id',
        'quantity',
    ];

    public function receipt()
    {
        return $this->belongsTo(ExportReceipt::class, 'export_receipt_id');
    }

    public function product()
    {
        return $this->belongsTo(Product::class);
    }
}

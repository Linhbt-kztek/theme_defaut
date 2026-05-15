<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;
use Illuminate\Database\Eloquent\SoftDeletes;

class ExportReceipt extends Model
{
    use HasFactory, SoftDeletes;

    protected $fillable = [
        'code',
        'warehouse_id',
        'note',
        'status',
        'completed_at',
        'cancelled_at',
        'is_delete',
        'deleted_at',
    ];

    protected $casts = [
        'completed_at' => 'datetime',
        'cancelled_at' => 'datetime',
    ];

    public function warehouse()
    {
        return $this->belongsTo(Warehouse::class);
    }

    public function items()
    {
        return $this->hasMany(ExportReceiptItem::class);
    }
}

<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;
use Illuminate\Database\Eloquent\SoftDeletes;

class Product extends Model
{
    use HasFactory, SoftDeletes;

    protected $fillable = [
        'sku',
        'barcode',
        'name',
        'unit',
        'description',
        'status',
        'is_delete',
        'deleted_at',
    ];

    public function stocks()
    {
        return $this->hasMany(Stock::class);
    }
}

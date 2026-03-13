<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;
use Spatie\Permission\Traits\HasPermissions;

class Role extends Model
{
    use HasFactory;
    use HasPermissions;

    protected $fillable = [
        'name',
        // 'description',
        'guard_name',
        'is_manager',
        'created_at',
        'updated_at',
        'deleted_at',
        'is_delete'
    ];

}

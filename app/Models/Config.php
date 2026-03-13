<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Model;
use Illuminate\Database\Eloquent\SoftDeletes;
use Illuminate\Support\Facades\Storage;

class Config extends Model
{
    use SoftDeletes;

    protected $table = 'configs';

    protected $fillable = [
        'id',
        'content',
        'banners',
        'max_device_sale',
        'is_delete',
        'showRegisterOnline',
        'ticket_config',
    ];


}

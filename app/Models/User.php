<?php

namespace App\Models;

use App\Services\MinioService;
use http\Exception\RuntimeException;
use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Relations\HasMany;
use Illuminate\Database\Eloquent\Relations\HasOne;
use Illuminate\Foundation\Auth\User as Authenticatable;
use Illuminate\Notifications\Notifiable;

//dùng Passport login
//use Laravel\Sanctum\HasApiTokens;
use Laravel\Passport\HasApiTokens;

use Illuminate\Database\Eloquent\SoftDeletes;

//dùng gói phân quyền Spatie
use Spatie\Permission\Traits\HasPermissions;
use Spatie\Permission\Traits\HasRoles;

class User extends Authenticatable
{
    use HasApiTokens, HasFactory, Notifiable, SoftDeletes, HasRoles;

    /**
     * The attributes that are mass assignable.
     *
     * @var array<int, string>
     */

    protected $table = 'users';
    protected $primaryKey = 'id';
    protected $keyType = 'string';
    protected $appends = ['url_user_avatar'];
    protected $folder_image = 'user_avatar';
    public $incrementing = false;

    /**
     * The attributes that are mass assignable.
     *
     * @var array<int, string>
     */
    protected $fillable = [
        'id',
        'role_id',
        'user_name',
        'name',
        'phone',
        'user_avatar',
        'email',
        'type',
        'email_verified_at',
        'token',
        'password',
        'remember_token',
        'token',
        'is_delete',
        'deleted_at',
        'updated_at',
        'created_at',
        'list_ticket_type'

    ];

    protected $casts = [
        'is_delete' => 'integer',
        'email_verified_at' => 'datetime',
    ];

    public const CREATED_AT = 'created_at';
    public const UPDATED_AT = 'updated_at';


    /**
     * The attributes that should be hidden for serialization.
     *
     * @var array<int, string>
     */
    protected $hidden = [
        'password',
        'remember_token',
    ];

    public function getUrlUserAvatarAttribute()
    {
        if ($this->user_avatar) {
          
            $url_avatar = app(MinioService::class)->getCloudImage($this->folder_image, $this->user_avatar);
        } else {
            $url_avatar = url('images/noimage.jpg');
        }
        return $url_avatar;
    }
}

<?php

// app/Bindings/RepositoryBindings.php
namespace App\Bindings;

use App\Models\PricingRule;
use App\Repositories\BaseRepository;
use App\Repositories\BaseRepositoryInterface;


use App\Repositories\Log\LogRepository;
use App\Repositories\Log\LogRepositoryInterface;
use App\Repositories\Permission\PermissionRepository;
use App\Repositories\Permission\PermissionRepositoryInterface;
use App\Repositories\Role\RoleRepository;
use App\Repositories\Role\RoleRepositoryInterface;
use App\Repositories\User\UserRepository;
use App\Repositories\User\UserRepositoryInterface;

class RepositoryBindings
{
    public static function map(): array
    {
        return [

            BaseRepositoryInterface::class => BaseRepository::class,
            UserRepositoryInterface::class => UserRepository::class,
            RoleRepositoryInterface::class => RoleRepository::class,
            PermissionRepositoryInterface::class => PermissionRepository::class,
            LogRepositoryInterface::class => LogRepository::class,

        ];
    }
}
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
use App\Repositories\Product\ProductRepository;
use App\Repositories\Product\ProductRepositoryInterface;
use App\Repositories\Role\RoleRepository;
use App\Repositories\Role\RoleRepositoryInterface;
use App\Repositories\Warehouse\WarehouseRepository;
use App\Repositories\Warehouse\WarehouseRepositoryInterface;
use App\Repositories\Stock\StockRepository;
use App\Repositories\Stock\StockRepositoryInterface;
use App\Repositories\StockLog\StockLogRepository;
use App\Repositories\StockLog\StockLogRepositoryInterface;
use App\Repositories\ImportReceipt\ImportReceiptRepository;
use App\Repositories\ImportReceipt\ImportReceiptRepositoryInterface;
use App\Repositories\ExportReceipt\ExportReceiptRepository;
use App\Repositories\ExportReceipt\ExportReceiptRepositoryInterface;
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
            ProductRepositoryInterface::class => ProductRepository::class,
            WarehouseRepositoryInterface::class => WarehouseRepository::class,
            StockRepositoryInterface::class => StockRepository::class,
            StockLogRepositoryInterface::class => StockLogRepository::class,
            ImportReceiptRepositoryInterface::class => ImportReceiptRepository::class,
            ExportReceiptRepositoryInterface::class => ExportReceiptRepository::class,

        ];
    }
}

<?php
return [
    'Quản lý kho' => [
        'class' => 'ri-archive-drawer-line',
        'child_menu' => [
            'Dashboard kho' => [
                'route_group' => 'inventory.dashboard',
                'route' => 'inventory.dashboard',
                'class' => 'ri-dashboard-line',
            ],
            'Sản phẩm' => [
                'route_group' => 'inventory.products',
                'route' => 'inventory.products.index',
                'class' => 'ri-box-3-line',
            ],
            'Kho hàng' => [
                'route_group' => 'inventory.warehouses',
                'route' => 'inventory.warehouses.index',
                'class' => 'ri-store-2-line',
            ],
            'Tồn kho' => [
                'route_group' => 'inventory.stocks',
                'route' => 'inventory.stocks.index',
                'class' => 'ri-stack-line',
            ],
            'Nhập kho' => [
                'route_group' => 'inventory.import-receipts',
                'route' => 'inventory.import-receipts.index',
                'class' => 'ri-download-2-line',
            ],
            'Xuất kho' => [
                'route_group' => 'inventory.export-receipts',
                'route' => 'inventory.export-receipts.index',
                'class' => 'ri-upload-2-line',
            ],
            'Lịch sử tồn kho' => [
                'route_group' => 'inventory.stock-logs',
                'route' => 'inventory.stock-logs.index',
                'class' => 'ri-history-line',
            ],
        ],
    ],
 
    // //---------------------------------- PHÂN QUYỀN ------------------------------------------------
    'Hệ thống' => [
        'class' => 'ri-lock-line',
        'child_menu' => [
            'Tài khoản' => [
                'route_group' => 'user', //sử dụng để active
                'route' => 'user', //route để gắn link
                'class' => 'ri-user-fill', //class của icon
                'permission' => 'index_user', //quyền của menu
            ],

            'Nhóm quyền' => [
                'route_group' => 'role', //sử dụng để active
                'route' => 'role.index', //route để gắn link
                'class' => 'ri-key-2-line', //class của icon
                'permission' => 'index_role', //quyền của menu
            ],

            'Cài đặt hệ thống' => [
                'route_group' => 'config', //sử dụng để active
                'route' => 'config.index', //route để gắn link
                'class' => 'ri-tools-line', //class của icon
                'permission' => 'index_config', //quyền của menu
            ],
            // 'Logs' => [
            //     'route_group' => 'log', //sử dụng để active
            //     'route' => 'log.index', //route để gắn link
            //     'class' => 'ri-file-history-line', //class của icon
            //     'permision' => 'index_log', //quyền của menu
            // ],
        ]
    ]
    // menu 1 cấp

];

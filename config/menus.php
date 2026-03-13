<?php
return [
 
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

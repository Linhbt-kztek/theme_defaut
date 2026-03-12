<?php
return [
    //-----------------------------------DANH MỤC ------------------------------------------------
    'Quản trị' => [
        'route_group' => '',
        'class' => 'ri-community-fill',
        'child_menu' => [
            'Chi nhánh' => [
                'route_group' => 'branch', //sử dụng để active
                'route' => 'branch.index', //route để gắn link
                'class' => 'ri-community-line', //class của icon
                'permision' => 'index_branch', //quyền của menu
            ],

            'Phòng ban' => [
                'route_group' => 'department', //sử dụng để active
                'route' => 'department.index', //route để gắn link
                'class' => 'ri-community-line', //class của icon
                'permision' => 'index_department', //quyền của menu
            ],

            'Nhân viên' => [
                'route_group' => 'staff', //sử dụng để active menu cha , nếu url hiện tại có chưa giá trị này
                'route' => 'staff.index', //route để gắn link
                'class' => 'ri-user-line', //class của icon
                'permission' => 'index_staff', //quyền của menu
            ]
        ]
    ],


    'Vận hành' => [
        'route_group' => '',
        'class' => 'ri-calendar-2-line',
        'child_menu' => [
            'Thiết bị' => [
                'route_group' => '',
                'class' => 'ri-device-line',
                'child_menu' => [
                    'Thiết bị chấm công' => [
                        'route_group' => 'control', //sử dụng để active menu cha , nếu url hiện tại có chưa giá trị này
                        'route' => 'control.index', //route để gắn link
                        'class' => 'ri-map-pin-line', //class của icon
                        'permission' => 'index_control', //quyền của menu
                    ],

                    'Tài khoản thiết bị' => [
                        'route_group' => 'user', //sử dụng để active
                        'route' => 'user.index', //route để gắn link
                        'class' => 'ri-user-fill', //class của icon
                        'permission' => 'index_user', //quyền của menu
                    ],
                ]
            ],

            'Cấu hình ca' => [
                'route_group' => '',
                'class' => 'ri-calendar-2-line',
                'child_menu' => [
                    'Lịch làm việc' => [
                        'route_group' => 'working-time', //sử dụng để active menu cha , nếu url hiện tại có chưa giá trị này
                        'route' => 'working-time.index', //route để gắn link
                        'class' => 'ri-calendar-line', //class của icon
                        'permission' => 'index_workingTime', //quyền của menu
                    ],
                    'Lịch nghỉ cố định' => [
                        'route_group' => 'day-off', //sử dụng để active menu cha , nếu url hiện tại có chưa giá trị này
                        'route' => 'day-off.index', //route để gắn link
                        'class' => 'ri-calendar-event-line', //class của icon
                        'permission' => 'index_dayOff', //quyền của menu
                    ],

                ]
            ],
        ]
    ],

    'Tiện ích' => [
        'route_group' => '',
        'class' => 'ri-gamepad-line',
        'child_menu' => [
            'Đặt lịch phòng họp' => [
                'route_group' => 'meetingRooms',
                'route' => 'meetingRooms.index',
                'class' => 'ri-calendar-event-line',
                'permission' => 'index_meetingroom',
            ],

            'Quản lý bữa ăn' => [
                'route_group' => '',
                'class' => 'ri-restaurant-line',
                'child_menu' => [
                    'Lịch ăn' => [
                        'route_group' => 'meal', //name của roter , sử dụng để active menu cha , nếu name của url hiện tại có chứa giá trị này
                        'route' => 'meal.meal_menu_index', //name của roter , để gắn link, kích vào
                        'class' => 'ri-calendar-line', //class của icon
                        'permission' => 'index_list_menu_meal', //quyền của menu
                    ],
                    'Suất ăn' => [
                        'route_group' => 'mealOrder', //sử dụng để active menu cha , nếu url hiện tại có chưa giá trị này
                        'route' => 'mealOrder.index', //route để gắn link
                        'class' => 'ri-clipboard-line', //class của icon
                        'permission' => 'index_booking_food', //quyền của menu
                    ]
                ]
            ]
        ]
    ],

    'Nghỉ phép và OT' => [
        'route_group' => '',
        'class' => 'ri-brush-line',
        'child_menu' => [
            'Nghỉ phép' => [
                'route_group' => 'absent',
                'route' => 'absent.index',
                'class' => 'ri-calendar-event-line',
                'permission' => 'index_absent',
            ],
            'Làm thêm giờ' => [
                'route_group' => 'overtime-work', //sử dụng để active menu cha , nếu url hiện tại có chưa giá trị này
                'route' => 'overtime-work.index', //route để gắn link
                'class' => 'ri-alarm-line', //class của icon
                'permission' => 'index_overtime_working', //quyền của menu
            ],
        ]
    ],

    'Chấm công' => [
        'route_group' => '',
        'class' => 'ri-brush-line',
        'child_menu' => [
            'Chấm công lại' => [
                'route_group' => 'attendance-rev',
                'route' => 'attendance-rev.index',
                'class' => 'ri-calendar-event-line',
                'permission' => 'index_attendance_revisions',
            ],
        ]
    ],

    'Báo cáo ' => [
        'route_group' => '',
        'class' => 'ri-line-chart-line',
        'child_menu' => [
            'Ds sự kiện' => [
                'route_group' => 'event', //sử dụng để active menu cha , nếu url hiện tại có chưa giá trị này
                'route' => 'event.index', //route để gắn link
                'class' => 'ri-calendar-line', //class của icon
                'permission' => 'index_event', //quyền của menu
            ],
            'Lịch sử chấm công' => [
                'route_group' => 'check_in', //sử dụng để active menu cha , nếu url hiện tại có chưa giá trị này
                'route' => 'check_in.index', //route để gắn link
                'class' => 'ri-history-line', //class của icon
                'permission' => 'history_checkin', //quyền của menu
            ],
            'Chi tiết chấm công' => [
                'route_group' => 'attendances', //sử dụng để active menu cha , nếu url hiện tại có chưa giá trị này
                'route' => 'attendances.detail-list-attendance', //route để gắn link
                'class' => 'ri-keyboard-line', //class của icon
                'permission' => 'index_attendances', //quyền của menu
            ],
            'Ds chốt công' => [
                'route_group' => 'turn', //sử dụng để active menu cha , nếu url hiện tại có chưa giá trị này
                'route' => 'turn.index', //route để gắn link
                'class' => 'ri-quill-pen-line', //class của icon
                'permission' => 'list_checkin', //quyền của menu
            ],
        ]
    ],

    // //---------------------------------- PHÂN QUYỀN ------------------------------------------------
    'Hệ thống' => [
        'class' => 'ri-lock-line',
        'child_menu' => [
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
        ]
    ],


];

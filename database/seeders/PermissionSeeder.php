<?php

namespace Database\Seeders;

use Illuminate\Database\Console\Seeds\WithoutModelEvents;
use Illuminate\Database\Seeder;
use Illuminate\Support\Facades\DB;

class PermissionSeeder extends Seeder
{
    /**
     * Run the database seeds.
     *
     * @return void
     */
    public function run()
    {
        //tên các quyền trong nhóm quyền này
        $data = [
            'user' => [
                'index_user' => 'Xem thông tin người dùng',
                'create_user' => 'Thêm mới người dùng',
                'update_user' => 'Cập nhật thông tin người dùng',
                'delete_user' => 'Xóa thông tin người dùng',
            ],

            'role' => [
                'index_role' => 'Xem vai trò',
                'create_role' => 'Thêm vai trò mới',
                'update_role' => 'Cập nhật thông tin vai trò',
                'delete_role' => 'Xóa vai trò'
            ],
            'config' => [
                'index_config' => 'Cài đặt hệ thống',
                'config_invoice' => 'Cấu hình hóa đơn điện tử',

            ]

            // 'log' => [
            //     'index_log' => 'Hiển thị log',
            // ],



        ];


        $data_permission = [];
        foreach ($data as $key => $value) {
            foreach ($value as $keyVal => $val) {
                $data_permission[] = $keyVal; //tên các quyền cụ thể : index_company , create_company ,...., để dùng cho việc xoá các quyền ko có trong này

                $data_insert = [
                    'name' => $keyVal,  //tên các quyền : index_company , create_company ,....
                    'description' => $val,
                    'guard_name' => 'web',
                    'module' => $key //tên module : company,registerEat ...
                ];

                //kiểm tra trong bảng permissions đã có tên quyền này chưa ?
                if (count(DB::table('permissions')->where('name', $data_insert['name'])->get()) == 0) {
                    DB::table('permissions')->insert([
                        $data_insert
                    ]);
                }
            }
        }

        DB::table('permissions')->whereNotIn('name', $data_permission)->delete();


        // sau khi hoàn tất thì chạy lệnh để đổ dữ liệu vào database:
        // bước 1 : xoá hết các quyên trước của module đó : chọn 1 module và ẩn các quyền đi , để module đó rỗng. chạy php artisan db:seed --class=PermissionSeeder
        // bước 2 : bật lại các quyền của mobule này và chạy lênh : php artisan db:seed --class=PermissionSeeder
    }
}

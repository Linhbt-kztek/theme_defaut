<?php

namespace App\Console\Commands;

use App\Models\User;
use Illuminate\Console\Command;
use Illuminate\Support\Facades\Artisan;
use Illuminate\Support\Facades\DB;
use Mockery\Exception;
use Spatie\Permission\Models\Permission;
use Spatie\Permission\Models\Role;

class CreateAdminCommand extends Command
{
    /**
     * The name and signature of the console command.
     *
     * @var string
     */
    protected $signature = 'create:admin';

    /**
     * The console command description.
     *
     * @var string
     */
    protected $description = 'Đây là tạo admin bằng câu lệnh';

    public function handle()
    {
        try {
            if($this->checkExistAdmin()) {
                Artisan::call('db:seed --class=PermissionSeeder');
                DB::beginTransaction();
                $permissions = Permission::all();
                $data = [
                    'guard_name' => 'web',
                    'name' => 'admin',
                    'is_delete' => 0,
                ];

                $role = Role::create($data);

                $role->syncPermissions($permissions);

                $dataUser = [
                    'id' => getGUID(),
                    'full_name' => 'admin',
                    'user_name' => 'admin',
                    'name' => 'admin',

                    'type' => 1,
                    'password' => bcrypt('123456'),
                    'is_delete' => 0
                ];
                $user = User::query()->create($dataUser);

                $user->assignRole($role);
                DB::commit();
                echo "Tạo thành công <br>";
                exit;
            }

            echo "Đã tồn tại tài khoản admin trên CSDL <br>";
            exit;
        } catch (Exception $e) {
            DB::rollBack();
            echo $e->getMessage();
        }
    }

    private function checkExistAdmin() : bool
    {
        return !(auth()->user() && auth()->user()->user_name !== 'admin');
    }
}

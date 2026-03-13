<?php

namespace App\Services;

use App\Enums\PositionEnum;
use App\Http\Requests\UserRequest;
use App\Models\Role;
use App\Repositories\Role\RoleRepositoryInterface;
use App\Repositories\User\UserRepositoryInterface;
use Carbon\Carbon;
use Illuminate\Support\Facades\Auth;
use Illuminate\Support\Facades\DB;
use Illuminate\Support\Facades\Validator;
use Illuminate\Support\Str;
use Illuminate\Validation\Rule;
use Laravel\Passport\RefreshToken;

class UserService
{
    public function __construct(
        protected UserRepositoryInterface $userRepo,
        protected RoleRepositoryInterface $roleRepo
    ) {
        //
    }

    public function createUserWithStaff($dataRequest, $type = 1)
    {
        DB::beginTransaction();
        $has_error = false;
        try {

            // $check_user = 

            $old_user = $this->userRepo->getDataAllOption([
                'orWhere' => [
                    ['user_name', '=', $dataRequest['user_name']],
                    ['email', '=', $dataRequest['email']],
                ]
            ]);

            if ($old_user->count() > 0) {
                $has_error = true;
                $message = 'Tên đăng nhập hoặc email đã được sử dụng, vui lòng kiểm tra lại!';
            } else {
                $user = [
                    'id' => getGUID(),
                    'name' => $dataRequest['name'],
                    'email' => isset($dataRequest['email']) ? ($dataRequest['email']) : getGUID(),
                    'password' => isset($dataRequest['password']) ? bcrypt($dataRequest['password']) : null,
                    'user_name' => isset($dataRequest['user_name']) ? ($dataRequest['user_name']) : null,
                    'phone' => isset($dataRequest['phone']) ? ($dataRequest['phone']) : null,
                    'type' => $type,
                    'role_id' => $dataRequest['role'] ?? null,
                    'image' => $dataRequest['image'] ?? null
                ];
                $create_user = $this->userRepo->create($user, true);

                if ($create_user != null) {
                 
                    // $create_user->syncRoles([]);
                    //    dd(123);

                    if (isset($dataRequest['role']) and $dataRequest['role'] != "") {
                        $role = Role::find(intval($dataRequest['role']));
                        $create_user->syncRoles([$role->name]);
                    }
                } else {
                    $has_error = true;
                    $message = 'Tạo tài khoản không thành công, vui lòng kiểm tra lại thông tin!';
                }



            }
        } catch (\Throwable $th) {
            $has_error = true;
            $message = 'Xảy ra lỗi trong quá trình tạo tài khoản!';
        }

        $has_error ? DB::rollBack() : DB::commit();
        return [
            'status' => !$has_error,
            'message' => $message ?? 'Thêm dữ liệu thành công!',
            'data' => $create_user ?? []
        ];
    }

    public function updateUser($dataRequest)
    {
        $user = [
            'password' => isset($dataRequest['password']) ? bcrypt($dataRequest['password']) : null,
        ];
        return $this->userRepo->update($dataRequest['id'], $user);
    }

    public function setRoleToUser($user_id, $role_id)
    {
        $user = $this->userRepo->getById($user_id);
     
        $role = Role::find($role_id);
        if (!$role) {
            // Xử lý khi role không tồn tại
            return;
        }

        // Xóa tất cả vai trò cũ và gán vai trò mới
        $user->syncRoles([$role->name]);

        // Cập nhật role_id nếu cần thiết (nên đảm bảo dữ liệu nhất quán)
        $user->update(['role_id' => $role->id]);
    }

    public function deleteUser($id)
    {
        return $this->userRepo->getById($id)->forceDelete();
    }

    public function clearToken($id)
    {
        try {
            $user = $this->userRepo->getById($id);
            $user->tokens()->delete();
            RefreshToken::where('user_id', $user->id)->delete();
            return true;
        } catch (\Throwable $th) {
            return false;
        }

    }

    public function checkTypeUser($type)
    {
        $user = auth()->user();

        if ($user->type != $type) {
            $this->clearToken($user->id);
            return [
                'status' => false,
                'message' => 'Tài khoản không được sử dụng tính năng này!',
            ];
        } else {
            return [
                'status' => true,
                'message' => '',
                'data' => $user
            ];
        }
    }

    public function createToken($user)
    {
        try {
            $tokenResult = $user->createToken('meeting-management');
            $accessTokenValue = $tokenResult->accessToken;

            $accessTokenId = $tokenResult->token->id;

            $refreshTokenValue = Str::random(64);
            $expiresAt = Carbon::now()->addDays(30);

            RefreshToken::create([
                'id' => getGUID(),
                'access_token_id' => $accessTokenId,
                'revoked' => true,
                'user_id' => $user->id,
                'token' => hash('sha256', $refreshTokenValue),
                'expires_at' => $expiresAt,
            ]);

            return [
                'status' => true,
                'token' => [
                    'access_token' => $accessTokenValue,
                    'refresh_token' => $refreshTokenValue, // gửi cho client
                ],
            ];

        } catch (\Throwable $th) {
            $response = [
                'status' => false,
                'errors' => 'Xảy ra lỗi trong quá trình tạo tokent!',
            ];
        }
        return $response;
    }
}

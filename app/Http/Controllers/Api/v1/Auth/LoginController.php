<?php

namespace App\Http\Controllers\Api\v1\Auth;

use App\Http\Controllers\Controller;
use App\Http\Requests\Api\Auth\LoginRequest;
use App\Http\Resources\UserResource;
use App\Repositories\Device\DeviceRepositoryInterface;
use App\Repositories\User\UserRepositoryInterface;
use App\Services\UserService;
use Carbon\Carbon;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\Auth;
use Illuminate\Support\Facades\DB;
use Illuminate\Support\Facades\Hash;
use Illuminate\Support\Facades\Validator;
use Illuminate\Support\Str;
use Laravel\Passport\RefreshToken;

class LoginController extends Controller
{
    protected $userRepository;
    public function __construct(
        UserRepositoryInterface $userRepository,
        // protected DeviceRepositoryInterface $deviceRepo,
        protected UserService $userService
    ) {
        $this->userRepository = $userRepository;
    }

    public function login(Request $request)
    {
        $has_error = false;
        try {
            DB::beginTransaction();
            $validator = Validator::make($request->all(), [
                'user_name' => 'required|string|max:255',
                'password' => 'required|string|min:6',
            ]);

            $user = $request->only('user_name', 'password');

            if ($validator->fails()) {
                $has_error = true;
                $errors = $validator->errors();
            }

            if (!$has_error) {
                $credentials = $request->only('user_name', 'password');
                $credentials['is_delete'] = 0;
                if (!(Auth::guard('web')->attempt($credentials))) {
                    $has_error = true;
                    $errors = 'login fails';
                }
            }

            if (!$has_error) {
                $user = Auth::guard('web')->user();
                // dd($user );
                // if ($user->type == 1) {
                //     $has_error = true;
                //     $errors = 'login fails';
                // }
            }

            if (!$has_error) {
                if ($user->type == 3) {
                    $checkDevice = $this->checkDevice($request, $user);
                    if ($checkDevice['status'] == 90) {
                        $has_error = true;
                        $errors = $checkDevice['errors'];
                    }
                }
            }

            if (!$has_error) {
                $createToken = $this->userService->createToken($user);
                if ($createToken['status']) {
                    $token = $createToken['token'];
                } else {
                    $has_error = true;
                    $errors = $createToken['errors'];
                }
            }

        } catch (\Throwable $th) {
            $has_error = true;
            $errors = 'Xảy ra lỗi trong quá trình kiểm tra đăng nhập!';
        }

        $has_error ? DB::rollBack() : DB::commit();

        $response = [
            'status' => $has_error ? 90 : 200,
            'token' => $token ?? '',
            'errors' => $errors ?? ''
        ];

        return $response;

    }

    // private function checkDevice($request, $user)
    // {
    //     $device_info = [
    //         'name' => $request->header('device-name'),
    //         'id_device' => $request->header('device-id'),
    //     ];

    //     $device = $this->deviceRepo->getDataAllOption(['filter' => [['user_id', '=', $user->id]]])->first();

    //     $update_device = ($device->id_device == $device_info['id_device']) || empty($device->id_device);

    //     if ($update_device) {
    //         $device->update($device_info);
    //         $response = [
    //             'status' => 200,
    //             'errors' => ''
    //         ];
    //     } else {
    //         $response = [
    //             'status' => 90,
    //             'errors' => 'Tài khoản đã được đăng nhập trên thiết bị khác!'
    //         ];
    //     }
    //     return $response;
    // }

    // private function createToken($user)
    // {
    //     try {
    //         $tokenResult = $user->createToken('meeting-management');
    //         $accessTokenValue = $tokenResult->accessToken;

    //         $accessTokenId = $tokenResult->token->id;

    //         $refreshTokenValue = Str::random(64);
    //         $expiresAt = Carbon::now()->addDays(30);

    //         RefreshToken::create([
    //             'id' => getGUID(),
    //             'access_token_id' => $accessTokenId,
    //             'revoked' => true,
    //             'user_id' => $user->id,
    //             'token' => hash('sha256', $refreshTokenValue),
    //             'expires_at' => $expiresAt,
    //         ]);

    //         return [
    //             'status' => true,
    //             'token' => [
    //                 'access_token' => $accessTokenValue,
    //                 'refresh_token' => $refreshTokenValue, // gửi cho client
    //             ],
    //         ];

    //     } catch (\Throwable $th) {
    //         $response = [
    //             'status' => false,
    //             'errors' => 'Xảy ra lỗi trong quá trình tạo tokent!',
    //         ];
    //     }
    //     return $response;
    // }

    // public function loginWithKey(Request $request)
    // {
    //     $has_error = false;
    //     $diffInMinutes = 30;

    //     $validator = Validator::make($request->all(), [
    //         'key' => 'required|string',
    //     ]);

    //     if ($validator->fails()) {
    //         $has_error = true;
    //         $errors = 'Key không được để trống và phải là string!';
    //     }

    //     if (!$has_error) {
    //         $key_request = $request->key;
    //         // $key = $this->generateAndPandemicKey($generateAndPandemicKey, 'pandemic');

    //         $key_sub_str = explode('_', $key_request);

    //         $key = $key_sub_str[0];
    //         $timestamp = $key_sub_str[1];

    //         $dt = Carbon::createFromTimestamp($timestamp);
    //         $time = $dt->format('Y-m-d H:i:s');

    //         $device = $this->deviceRepo->getDataAllOption([
    //             'filter' => [
    //                 ['key', '=', $key]
    //             ]
    //         ])->first();

    //         if ($device) {
    //             if ($device->key_time_created != $time) {
    //                 $has_error = true;
    //                 $errors = 'Key không đúng, vui lòng kiểm tra lại!';
    //             }
    //         } else {
    //             $has_error = true;
    //             $errors = 'Không tìm thấy thiết bị!';
    //         }

    //     }

    //     if (!$has_error) {
    //         $key_time_created = Carbon::parse($device->key_time_created);
    //         $thisDiffInMinutes = Carbon::now()->diffInMinutes($key_time_created);

    //         if ($thisDiffInMinutes <= $diffInMinutes) {
    //             $user = $this->userRepository->getById($device->user_id);
    //             if ($user == null) {
    //                 $has_error = true;
    //                 $errors = 'Vui lòng kiểm tra tài khoản thiết bị!';
    //             }
    //         } else {
    //             $has_error = true;
    //             $errors = 'Key quá hạn, vui lòng kiểm tra lại!';
    //         }
    //     }

    //     if (!$has_error) {
    //         $checkDevice = $this->checkDevice($request, $user);
    //         if ($checkDevice['status'] == 90) {
    //             $has_error = true;
    //             $errors = $checkDevice['errors'];
    //         }
    //     }

    //     if (!$has_error) {
    //         $createToken = $this->userService->createToken($user);
    //         if ($createToken['status']) {
    //             $token = $createToken['token'];
    //         } else {
    //             $has_error = true;
    //             $errors = $createToken['errors'];
    //         }
    //     }

    //     $response = [
    //         'status' => $has_error ? 90 : 200,
    //         'token' => $token ?? '',
    //         'errors' => $errors ?? ''
    //     ];

    //     return $response;
    // }

    public function refreshToken(Request $request)
    {
        $has_error = false;
        try {
            DB::beginTransaction();
            $validator = Validator::make($request->all(), [
                'refresh_token' => 'required|string',
            ]);

            if ($validator->fails()) {
                $has_error = true;
                $errors = 'refresh_token không được để trống!';
            }

            if (!$has_error) {
                $refreshTokenValue = $request->input('refresh_token');
                $refreshTokenHash = hash('sha256', $refreshTokenValue);

                $refreshToken = RefreshToken::where('token', $refreshTokenHash)
                    ->where('expires_at', '>', now())
                    ->where('revoked', true)
                    ->first();

                if (!$refreshToken) {
                    $has_error = true;
                    $errors = 'Refresh token is invalid or expired!';
                }
            }
            if (!$has_error) {
                $user = $this->userRepository->getById($refreshToken->user_id); // Giả sử có relation: RefreshToken belongsTo User

                $refreshToken->revoked = false;
                $refreshToken->save();

                if (!$user) {
                    $has_error = true;
                    $errors = 'User not found';
                }
            }
            if (!$has_error) {
                $createToken = $this->userService->createToken($user);
                if (!$createToken['status']) {
                    $has_error = true;
                    $errors = $createToken['errors'];
                }
            }
        } catch (\Throwable $th) {
            $has_error = true;
            $errors = 'Xảy ra lỗi trong quá trình cấp lại token';
        }

        $has_error ? DB::rollBack() : DB::commit();

        return response()->json([
            'status' => $has_error ? 90 : 200,
            'token' => $createToken['token'] ?? [],
            'errors' => $errors ?? ''
        ], 200);
    }
}

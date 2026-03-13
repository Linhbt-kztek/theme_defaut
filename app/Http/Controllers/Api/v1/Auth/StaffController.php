<?php

namespace App\Http\Controllers\Api\v1\Auth;

use App\Http\Controllers\Controller;
use App\Http\Rules\CheckImageRules;
use App\Models\Role;
use App\Repositories\Agency\AgencyRepositoryInterface;
use App\Repositories\MeetingDetail\MeetingDetailRepositoryInterface;
use App\Repositories\Staff\StaffRepositoryInterface;
use App\Services\MinioService;
use App\Services\StaffService;
use App\Services\UserService;
use Carbon\Carbon;
use Exception;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\DB;
use Illuminate\Support\Facades\Hash;
use Illuminate\Support\Facades\Validator;
use Illuminate\Validation\Rule;
use Svg\Tag\Rect;

class StaffController extends Controller
{
    protected $folder_image = 'staff_avatar';
    public function __construct(
        protected UserService $userService,
        protected MinioService $minioService,
        protected StaffService $staffService,
        protected StaffRepositoryInterface $staffRepo,
        protected MeetingDetailRepositoryInterface $meetingDetailRepo,
        protected AgencyRepositoryInterface $agencyRepo
    ) {
    }

    public function getData(Request $request)
    {
        $has_error = false;
        $this->setSearchSession($request, 'staff');

        $limit = $request->limit ?? 0;

        if (!empty($request->meeting_detail_id)) {
            $meeting_detail = $this->meetingDetailRepo->getById($request->meeting_detail_id);
            if ($meeting_detail == null) {
                $has_error = true;
                $message = 'Không tìm thấy phiên họp!';
            }
        }

        if (!$has_error) {
            $data = $this->staffRepo->getDataAllOption($this->getWhereIndex($request), $limit, ['agency']);

            if (!empty($data)) {
                $data->transform(function ($staff) {
                    $staff->url_image = $this->minioService->getCloudImage($this->folder_image, $staff->image);
                    return $staff;
                });
            }
        }


        return response()->json([
            'message' => $message ?? 'Lấy dữ liệu thành công!',
            'count' => !empty($data) ? count($data) : 0,
            'data' => $data ?? []
        ]);
    }

    private function getWhereIndex($request)
    {
        $filter = [['is_approve', '=', 2]];
        $whereHas = [];

        if (!empty($request->meeting_detail_id)) {
            $meeting_detail = $this->meetingDetailRepo->getById($request->meeting_detail_id);

            if ($meeting_detail) {
                $whereHas['meetingStaff'] = [
                    [
                        'colum' => 'meeting_id',
                        'operator' => '=',
                        'value' => $meeting_detail->meeting_id
                    ]
                ];
            }

        }

        $orWhere = [];
        if (!empty(session('staff.key_search'))) {
            $orWhere[] = ['name', 'Like', '%' . session('staff.key_search') . '%'];
            $orWhere[] = ['code', 'Like', '%' . session('staff.key_search') . '%'];
        }
        return [
            'orWhere' => $orWhere,
            'whereHas' => $whereHas,
            'filter' => $filter,
        ];
    }

    private function validateData($request, $id = '', $check_new_staff = false)
    {
        $has_error = false;
        $message = '';

        $rules = [
            'code' => [
                // 'required',
                'string',
                'max:200',
                Rule::unique('staffs')
                    ->ignore($id) // Bỏ qua bản ghi với id hiện tại
                    ->where(function ($query) use ($request) {
                        return $query->where('deleted_at', null);
                    }),
            ],

            'name' => 'string',
            'image' => [new CheckImageRules],
            'tokent_firebase' => 'string',

            // 'agencies_id' => 'required',
            // 'user_name' => !empty($id) ? ['string', 'max:200'] : ['required', 'string', 'max:200'],
            // 'password' => 'required',
            // 'role' => 'required',
        ];

        if ($check_new_staff) {
            $rules['identification'] = [
                // 'required',
                'string',
                'max:200',
                Rule::unique('staffs')
                    ->ignore($id) // Bỏ qua bản ghi với id hiện tại
                    ->where(function ($query) use ($request) {
                        return $query->where('deleted_at', null);
                    }),
            ];
            $rules['agencies_id'] = 'required';
            // $rules['user_name'] = !empty($id) ? ['string', 'max:200'] : [
            //     'required',
            //     'string',
            //     'max:200',
            //     Rule::unique('users')
            //         ->where(function ($query) use ($request) {
            //             return $query->where('deleted_at', null);
            //         }),
            // ];
            // $rules['password'] = 'required';
            // $rules['role'] = 'required';
        }

        $messages = [
            'name.required' => 'Tên thành viên là bắt buộc.',
            'code.required' => 'Mã thành viên là bắt buộc.',
            'code.unique' => 'Mã thành viên đã tồn tại.',

            'user_name.required' => 'Tài khoản không được để trống!',
            'password.required' => 'Mật khẩu không được để trống!',
            // 'role.required' => 'Nhóm quyền không được để trống!',

            'agencies_id.required' => 'Vui lòng chọn đơn vị.',
            'identification.unique' => 'Căn cước của bạn đã được lưu trên hệ thống.',
            'identification.required' => 'Căn cước không được để trống.',
            'user_name.unique' => 'Tài khoản đã được sử dụng, vui lòng nhập tài khoản khác!',
        ];
        $validator = Validator::make($request->all(), $rules, $messages);

        if ($validator->fails()) {
            $has_error = true;
            // dd($validator->errors());
            $message = $validator->errors()->first();
            $list_errors = $validator->errors();
        }

        return [
            'status' => !$has_error,
            'message' => $message ?? '',
            'errors' => $list_errors ?? []
        ];
    }

    public function save(Request $request)
    {
        $has_error = false;
        DB::beginTransaction();
        try {
            $validate = $this->validateData($request, $request->id);

            if (!$validate['status']) {
                $has_error = true;
                $message = $validate['message'];
            }

            if (!$has_error) {
                $birthday = !empty($request->birthday) ? Carbon::parse($request->birthday) : null;



                $staff_data = [
                    // 'id' => getGUID(),
                    'name' => $request->name,
                    'code' => $request->code,
                    'phone' => $request->phone,
                    'email' => $request->email,
                    'day_of_birth' => $birthday != null ? $birthday->clone()->format('d') : null,
                    'month_of_birth' => $birthday != null ? $birthday->clone()->format('m') : null,
                    'year_of_birth' => $birthday != null ? $birthday->clone()->format('Y') : null,
                    'address' => $request->address ?? '',
                    'agencies_id' => $request->agencies_id,
                ];

                if (!empty($request->staff_avatar)) {
                    $staff_data['image'] = $this->minioService->pushImageCloud($request->staff_avatar, $this->folder_image);
                }

                if (!empty($request->id)) {
                    $staff = $this->staffRepo->getById($request->id);
                    if ($staff) {
                        $this->staffRepo->update($request->id, $staff_data);
                        $this->userService->setRoleToUser($staff->user_id, $request->role);
                    } else {
                        $has_error = true;
                        $message = 'Không tìm thấy thông tin nhân viên, vui lòng thử lại!';
                    }
                } else {
                    $staff_data['id'] = getGUID();
                    $user = $this->userService->createUserWithStaff($request);

                    if ($user['status']) {
                        $staff_data['user_id'] = $user['data']['id'];
                    } else {
                        $has_error = true;
                        $message = $user['message'];
                    }

                    if (!$has_error) {
                        $staff = $this->staffRepo->create($staff_data, true);
                    }
                }

            }

        } catch (\Throwable $th) {
            dd( $th->getMessage());
            $has_error = true;
            $message = 'Xảy ra lỗi trong quá trình thêm dữ liệu!';
        }

        $has_error ? DB::rollBack() : DB::commit();
        return response()->json([
            'status' => $has_error ? 503 : 200,
            'message' => $message ?? 'Thêm dữ liệu thành công!',
            'data' => $staff ?? []
        ]);

    }

    public function show($id)
    {
        $data = $this->staffRepo->getById($id, ['user']);
        if (!empty($data)) {
            $data['url_image'] = $this->minioService->getCloudImage($this->folder_image, $data->image);
        }
        return response()->json(
            [
                'status' => !empty($data) ? 200 : 90,
                'data' => $data
            ]
        );
    }

    public function destroy($id)
    {
        DB::beginTransaction();

        try {
            $data = $this->staffRepo->getById($id);
            $data->delete();

            DB::commit();

            if (request()->ajax()) {
                return response()->json([
                    'success' => true,
                    'message' => 'Xóa thành công'
                ]);
            }

            return redirect()->route('agency.index')
                ->with('alert-success', 'Xóa thành công');
        } catch (\Throwable $th) {
            DB::rollBack();

            if (request()->ajax()) {
                return response()->json([
                    'success' => false,
                    'message' => $th->getMessage()
                ], 500);
            }

            return redirect()->route('agency.index')
                ->with('alert-error', $th->getMessage());
        }
    }

    public function getinfo()
    {
        $checkTypeUser = $this->userService->checkTypeUser(1);
        $has_error = !$checkTypeUser['status'];
        $message = $checkTypeUser['message'];

        if (!$has_error) {
            $user = auth()->user();
            $staff = $this->staffRepo->getDataAllOption([
                'filter' => [
                    ['user_id', '=', $user->id]
                ]
            ], 0, ['agency'])->first();
            // dd($staff);
            if ($staff != null) {
                $staff['user_name'] = $user->user_name;
            } else {
                $has_error = true;
                $message = 'Không tìm thấy thông tin!';
            }
        }

        return response()->json([
            'data' => $staff ?? null,
            'message' => $message
        ], $has_error ? 503 : 200);
    }



    public function updateinfo(Request $request)
    {
        $checkTypeUser = $this->userService->checkTypeUser(1);
        $has_error = !$checkTypeUser['status'];
        $message = $checkTypeUser['message'];

        try {
            DB::beginTransaction();
            if (!$has_error) {
                $user = auth()->user();
                $staff = $this->staffRepo->getDataAllOption([
                    'filter' => [
                        ['user_id', '=', $user->id]
                    ]
                ], 0, ['agency'])->first();

                if ($staff == null) {
                    $has_error = true;
                    $message = 'Không tìm thấy thông tin thành viên!';
                }
            }

            if (!$has_error) {
                $validate = $this->validateData($request, $staff->id);
                $has_error = !$validate['status'];
                $message = $validate['message'];
            }

            $data_update = [];

            if (!empty($request->name)) {
                $data_update['name'] = $request->name;
            }

            if (!empty($request->code)) {
                $data_update['code'] = $request->code;
            }

            if (!empty($request->phone)) {
                $data_update['phone'] = $request->phone;
            }

            if (!empty($request->email)) {
                $data_update['email'] = $request->email;
            }

            if (!empty($request->address)) {
                $data_update['address'] = $request->address;
            }

            if (!empty($request->position)) {
                $data_update['position'] = $request->position;
            }

            if (!empty($request->birthday)) {
                $birthday = Carbon::parse($request->birthday);
                $data_update['day_of_birth'] = $birthday->clone()->format('d');
                $data_update['month_of_birth'] = $birthday->clone()->format('m');
                $data_update['year_of_birth'] = $birthday->clone()->format('Y');
            }

            if (!empty($request->image)) {
                $data_update['image'] = $this->minioService->pushImageCloud($request->image, $this->folder_image);
            }

            if (!empty($request->tokent_firebase)) {
                $data_update['tokent_firebase'] = $request->tokent_firebase;
            }

            if (!$has_error) {
                $update_staff = $this->staffRepo->update($staff->id, $data_update);
                if (!$update_staff) {
                    $has_error = true;
                    $message = 'Xảy ra lỗi trong quá trình cập nhật thành viên!';
                }
            }

            // if (!empty($request->password) && !$has_error) {
            //     $updateUser = $this->userService->updateUser([
            //         'id' => $user->id,
            //         'password' => $request->password
            //     ]);
            //     if (!$updateUser) {
            //         $has_error = true;
            //         $message = 'Xảy ra lỗi trong quá trình đổi mật khẩu!';
            //     }
            // }
        } catch (\Throwable $th) {
            $has_error = true;
            $message = 'Xảy ra lỗi trong quá trình đổi mật khẩu!';
        }

        $has_error ? DB::rollBack() : DB::commit();

        return response()->json([
            'message' => $message != '' ? $message : 'Cập nhật thông tin thành công!'
        ], $has_error ? 503 : 200);

    }

    public function changePassword(Request $request)
    {
        try {
            DB::beginTransaction();

            $checkTypeUser = $this->userService->checkTypeUser(1);
            $has_error = !$checkTypeUser['status'];
            $message = $checkTypeUser['message'];

            $rules = [
                'password_old' => ['required', 'string'],
                'password' => ['required', 'string'],
                'password_confirm' => ['required', 'string'],
            ];
            $messages = [
                'password_old.required' => 'Mật khẩu cũ không được để trống!',
                'password.required' => 'Mật khẩu không được để trống!',
                'password_confirm.required' => 'Xác nhận mật khẩu được để trống!'
            ];

            $validator = Validator::make($request->all(), $rules, $messages);

            if ($validator->fails()) {
                $has_error = true;
                $message = $validator->errors()->first();
            }

            if (!$has_error) {
                if ($request->password != $request->password_confirm) {
                    $has_error = true;
                    $message = 'Mật khẩu mới và mật khẩu xác nhận không trùng khớp!';
                }
            }

            if (!$has_error) {
                if ($request->password == $request->password_old) {
                    $has_error = true;
                    $message = 'Mật khẩu mới và mật khẩu cũ trùng khớp!';
                }
            }

            if (!$has_error) {
                $user = $checkTypeUser['data'];
                $inputPassword = $request->input('password_old');

                if (!(Hash::check($inputPassword, $user->password))) {
                    $has_error = true;
                    $message = 'Mật khẩu cũ không đúng, vui lòng kiểm tra lại!';
                }
            }

            if (!$has_error) {
                $updateUser = $this->userService->updateUser([
                    'id' => $user->id,
                    'password' => $request->password
                ]);

                if (!$updateUser) {
                    $has_error = true;
                    $message = 'Xảy ra lỗi trong quá trình đổi mật khẩu!';
                }
            }

            if (!$has_error) {
                $this->userService->clearToken($user->id);
            }
        } catch (\Throwable $th) {
            $has_error = true;
            $message = 'Xảy ra lỗi trong quá trình đổi mật khẩu!';
        }

        $has_error ? DB::rollBack() : DB::commit();

        return response()->json([
            'message' => $message != '' ? $message : 'Đổi mật khẩu thành công!'
        ], $has_error ? 503 : 200);
    }

    public function register(Request $request)
    {
        $has_error = false;
        $message = '';
        $errors = [];
        try {
            $validateData = $this->validateData($request, '', true);

            if (!$validateData['status']) {
                $has_error = true;
                $message = $validateData['message'];
                $errors = $validateData['errors'];
            }

            if (!$has_error) {
                $save = $this->staffService->saveStaff($request->all(), true);
                if (!$save['status']) {
                    $has_error = true;
                    $message = $save['message'];
                } else {
                    $data = $save['data'];
                }
            }

        } catch (\Throwable $th) {
            $has_error = true;
            $message = 'Xảy ra lỗi trong quá trình đổi mật khẩu!';
            $errors[] = $th->getMessage();
        }

        return response()->json([
            'message' => $message != '' ? $message : 'Đăng ký tài khoản thành công!',
            'errors' => $errors,
            'data' => $data ?? null,
        ], $has_error ? 503 : 200);
    }
}

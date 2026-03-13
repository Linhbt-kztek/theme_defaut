<?php

namespace App\Http\Controllers\Admin;

//use App\Exports\UserExport;
//use Maatwebsite\Excel\Facades\Excel;

use App\Http\Controllers\Controller;
use App\Http\Requests\LoginRequest;
use App\Http\Requests\UserCreateRequest;
use App\Http\Requests\UserRequest;
use App\Repositories\Area\AreaRepositoryInterface;
use App\Repositories\User\UserRepository;
use App\Repositories\User\UserRepositoryInterface;
use App\Services\MinioService;
use Exception;
use Illuminate\Http\Request;
use Illuminate\Support\Carbon;
use Illuminate\Support\Facades\Auth;
use Illuminate\Support\Facades\DB;
use Illuminate\Support\Facades\Log;
use Illuminate\Support\Facades\Session;
use Illuminate\Support\Facades\Storage;
use Illuminate\Validation\ValidationException;
use Illuminate\Support\Str;
use Illuminate\Support\Facades\View;
use Spatie\Permission\Models\Role;

use App\Repositories\Role\RoleRepositoryInterface;


class UserController extends Controller
{
    protected $image_file;
    public function __construct(
        protected UserRepositoryInterface $userRepo,
        protected UserRepository $repository,
        protected RoleRepositoryInterface $roleRepo,
        protected MinioService $minioService,
    ) {
        $this->image_file = 'assets/images/users';
        $this->folder_image = 'user_avatar';
    }

    public function index(Request $request)
    {

        $this->resetSessionSearch('index_user');
        $this->authorize('index_user');
        $breadcrumb = [
            [
                'title' => 'Trang chủ',
                'route' => 'home'
            ],
            [
                'title' => 'Quản lý tài khoản',
                'route' => 'user'
            ]
        ];

        if (!session()->has('search')) {
            session([
                'search' => [
                    'page' => 1,
                    'key_search' => '',
                ]
            ]);
        }

        if (isset($request->confirm_search) and $request->confirm_search == "1") {
            session(['search.page' => 1]);

            if (isset($request->key_search))
                session(['search.key_search' => trim($request->key_search)]);
            else
                session(['search.key_search' => ""]);

            return redirect('admin/user');
        }

        //kích vào link chuyển trang
        if (isset($request->page) and (int) $request->page > 0)
            session(['search.page' => (int) $request->page]);

        $search_option = array();
        // $search_option[] = ['is_classification_customer', '=', 0];

        $limit = 20;
        $users = $this->repository->getWithFilter(session('search.key_search'), $limit, session('search.page'), $search_option);
        $total = count($this->repository->getWithFilter(session('search.key_search'), 0, -1, $search_option));

        $search = [];
        //        $search[] = ['is_manager', '=', 2];
        $roles = $this->roleRepo->getWithFilter('', 0, -1, $search);

        //$user->getRoleNames()->first()
        if (isset($users) and $users->count() > 0) {
            foreach ($users as $key => $item) {
                $role_name = $item->getRoleNames()->first();
                $users[$key]["role_name"] = $role_name;
            }
        }


        return view('admin.user.index', [
            'users' => $users,
            'total' => $total,
            'limit' => $limit,
            'roles' => $roles,
            'breadcrumb' => $breadcrumb,
        ]);
    }

    public function show($id)
    {
        $user = $this->userRepo->getById($id);
        if (!$user) {
            return redirect()->back()->with('alert-error', 'Không tồn tại !');
        }
        return view('user.view', [
            'user' => $user,
        ]);
    }

    //lấy tất cả quyền của
    public function callOption()
    {
        View::share('roles', Role::get());
    }

    public function create()
    {
        $this->authorize('create_user');

        $search_option = [];
        //        $search_option[] = ['is_manager', '=', 2];
        $roles = $this->roleRepo->getWithFilter('', 0, -1, $search_option);

        $breadcrumb = [
            [
                'title' => 'Trang chủ',
                'route' => 'home'
            ],
            [
                'title' => 'Quản lý tài khoản',
                'route' => 'user'
            ],
            [
                'title' => 'Thêm tài khoản mới'
            ],
        ];

        return view('admin.user.edit', [
            'breadcrumb' => $breadcrumb,
            'roles' => $roles
        ]);
    }


    /**
     * @param UserRequest $request
     * @return \Illuminate\Http\RedirectResponse
     */
    public function store(UserRequest $request) //UserCreateRequest
    {

        $this->authorize('create_user');
        $dataRequest = $request->all();

        $user = [
            'id' => getGUID(),
            'is_delete' => 0,
            'name' => $dataRequest['name'],
            'email' => isset($dataRequest['email']) ? ($dataRequest['email']) : getGUID(),
            'password' => isset($dataRequest['password']) ? bcrypt($dataRequest['password']) : null,
            //            'id_token' => $this->getToken(),
//            'refresh_token' => $this->getToken(),
//            'access_token' => $this->getToken(),
            'user_name' => isset($dataRequest['user_name']) ? ($dataRequest['user_name']) : null,
            'phone' => isset($dataRequest['phone']) ? ($dataRequest['phone']) : null,
            //            'address' => isset($dataRequest['address']) ? ($dataRequest['address']) : null,
            'type' => isset($dataRequest['type']) ? (int) $dataRequest['type'] : null,
            //'list_company_id' => json_encode([]),
            // 'role_id' => isset($dataRequest['role_id']) ? ($dataRequest['role_id']) : null,
        ];

        //nếu là tài khoản api thì ko có role nhé
        if ((int) $user["type"] == 2) {
            $role_id = null;
        } else {
            $role_id = $dataRequest['role_id'];
        }


        if ($request->hasFile('file_link_image')) {
            $avatar_name = $this->minioService->pushImageCloud($request->file_link_image, $this->folder_image);
            $user['user_avatar'] = $avatar_name;
        } else {
            $user["user_avatar"] = null;
        }

        DB::beginTransaction();
        try {
            $user = $this->repository->create($user);
            $user->syncRoles([]);

            if (isset($role_id) and $role_id != "") {
                $role = Role::findById(intval($role_id));
                $user->assignRole($role);
            }

            DB::commit();

            return redirect()->route('user')->with('alert-success', 'Thêm mới thành công');
        } catch (Exception $th) {
            DB::rollBack();
            Log::error($th->getMessage());

            return redirect()->back()->with('alert-error', 'Có lỗi trong quá trình thêm mới!');
        }
    }

    /**
     * @param $id
     * @return \Illuminate\Contracts\Foundation\Application|\Illuminate\Contracts\View\Factory|\Illuminate\Contracts\View\View|\Illuminate\Http\RedirectResponse
     */

    public function editOnUser($id)
    {

        $breadcrumb = [
            [
                'title' => 'Trang chủ',
                'route' => 'home'
            ],
            [
                'title' => 'Quản lý tài khoản',
                'route' => 'user'
            ],
            [
                'title' => 'Cập nhật thông tin tài khoản'
            ],
        ];

        //lấy tất cả các quyền
        //  $this->callOption();
        $search_option = [];
        //        $search_option[] = ['is_manager', '=', 2];

        $roles = $this->roleRepo->getWithFilter('', 0, -1, $search_option);


        $user = $this->userRepo->getById($id);

        if ($user != null) {
            //bắt quyền phòng ban: nếu typ=2 là app hoặc phòng ban của user này ko phải phòng ban của người đăng nhập
//            if (Auth::user()->type > 1 && $user->company_id != Auth::user()->company_id) {
//                return redirect()->back()->with('alert-error', 'Bạn không thể chỉnh sửa thông tin tài khoản này!');
//            }

            $url_previous = url()->previous();
            if (!strpos($url_previous, 'edit')) {
                session(['url_previous' => $url_previous]);
            }

            return view('admin.user.edit', [
                'user' => $user,
                'breadcrumb' => $breadcrumb,
                'on_user' => true,
                'roles' => $roles
            ]);
        } else {
            return redirect()->back()->with('alert-error', 'Không tim thấy thông tin tài khoản này!');
        }
    }

    public function edit($id)
    {

        $this->authorize('update_user');


        $search_option = [];
        //        $search_option[] = ['is_manager', '=', 2];
        $roles = $this->roleRepo->getWithFilter('', 0, -1, $search_option);


        $breadcrumb = [
            [
                'title' => 'Trang chủ',
                'route' => 'home'
            ],
            [
                'title' => 'Quản lý tài khoản',
                'route' => 'user'
            ],
            [
                'title' => 'Cập nhật thông tin tài khoản'
            ],
        ];

        $user = $this->userRepo->getById($id);

        if (!isset($user)) {
            return redirect()->back()->with('alert-error', 'Không tim thấy thông tin tài khoản này!');
        }

        //nếu tài khoản này là web(type=1) mà chưa dc phân vai trò là lỗi-------------------
        {
            $role_id = "";
            if ((int) $user['type'] == 1) {
                //tìm $role_id cho tài khoản này , 1 user chỉ có 1 $role_id thôi
                $roleIds = $user->roles->pluck('id');
                // dd($roleIds);
                if (count($roleIds) == 0) {
                    return redirect()->back()->with('alert-error', 'Lỗi : tài khoản này chưa được phân vai trò');
                }
                $role_id = $roleIds[0];
            }
        }
        //nếu tài khoản này là web(type=1) mà chưa dc phân vai trò là lỗi-------------------


        //        if (Auth::user()->type > 1 && $user->company_id != Auth::user()->company_id) {
//            return redirect()->back()->with('alert-error', 'Bạn không thể chỉnh sửa thông tin tài khoản này!');
//        }

        $url_previous = url()->previous();
        if (!strpos($url_previous, 'edit')) {
            session(['url_previous' => $url_previous]);
        }

        return view('admin.user.edit', [
            'user' => $user,
            'breadcrumb' => $breadcrumb,
            'roles' => $roles,
            'role_id' => $role_id
        ]);
    }

    /**
     * @param UserRequest $request
     * @param $id
     * @return \Illuminate\Http\RedirectResponse
     */
    public function update(UserRequest $request, $id)
    {


        $this->authorize('update_user');


        $dataRequest = $request->all();

        // dd($dataRequest);

        $user_data = [
            'is_delete' => 0,
            'name' => $dataRequest['name'],
            'email' => isset($dataRequest['email']) ? ($dataRequest['email']) : null,
            'user_name' => isset($dataRequest['user_name']) ? ($dataRequest['user_name']) : null,

            'phone' => isset($dataRequest['phone']) ? ($dataRequest['phone']) : null,
            //                'address' => isset($dataRequest['address']) ? ($dataRequest['address']) : null,
            //                'type' => isset($dataRequest['type']) ? (int)$dataRequest['type'] : null
        ];

        //            if ($dataRequest['type'] == 2) {
        //                # code...
        //                $user['location_eat_id'] = isset($dataRequest['location_eat_id']) ? (int)$dataRequest['location_eat_id'] : null;
        //            } else {
        //                $user['location_eat_id'] = null;
        //            }

        if (isset($dataRequest['password']) and $dataRequest['password'] != "") {
            $user_data["password"] = bcrypt($dataRequest['password']);
        }

        // dd($request->hasFile('file_link_image'));
        //xử lý ảnh ==========================================
        if ($request->hasFile('file_link_image')) {
            $image = $request->file('file_link_image');
            //lấy loại file
            if (!in_array($image->getClientMimeType(), unserialize(config('app.image_type')))) {

                $arr_error["file_link_image"] = "Phải chọn file ảnh đuôi : jpeg , png , jpg !";
                throw ValidationException::withMessages($arr_error);
                exit;
            }

            $avatar_name = $this->minioService->pushImageCloud($request->file_link_image, $this->folder_image);
            $user_data['user_avatar'] = $avatar_name;

        }
        // dd($user_data);

        //xử lý ảnh ==========================================

        DB::beginTransaction();
        try {

            $result = $this->userRepo->update($id, $user_data);
            if (!$result) {
                return redirect()->back()->with('alert-error', 'Có lỗi trong quá trình cập nhật!');

            }

            //cập nhật vai trò--------------------------
            $user = $this->userRepo->getById($id);

            //xoá đi tất các role đã gán cho user này trước đó
            $user->syncRoles([]);

            if (isset($request->role_id) and $request->role_id != "") {

                $role = Role::findById(intval($request->role_id));

                $user->assignRole($role);
            }
            //cập nhật vai trò--------------------------

            DB::commit();

            return redirect()->back()->with('alert-success', 'Sửa thành công !');
        } catch (\Throwable $th) {

            DB::rollBack();
            Log::error($th->getMessage());

            return redirect()->back()->with('alert-error', 'Có lỗi trong quá trình cập nhật!');
        }
    }

    /**
     * @param $id
     * @return \Illuminate\Http\RedirectResponse
     */
    public function delete($id)
    {
        $this->authorize('delete_user');

        $user = $this->userRepo->getById($id);
        if (!isset($user)) {
            return redirect()->back()->with('alert-error', 'Không tim thấy thông tin tài khoản này!');
        }

        DB::beginTransaction();
        ;
        try {


            $user->roles()->detach();

            $this->userRepo->delete($id);

            DB::commit();

            return response()->json(['success' => 'User Deleted Successfully!']);
        } catch (\Throwable $th) {

            DB::rollBack();
            Log::error($th->getMessage());

            return redirect()->back()->with('alert-error', 'Có lỗi trong quá trình thêm mới!');
        }
    }

    public function export()
    {
        $query = [];
        if (session('search')['name']) {
            $query[] = ['name', 'like', '%' . session('search')['name'] . '%'];
        }
        $user = $this->userRepo->index($query, null)->toArray();
        return Excel::download(new UserExport($user), 'user.xlsx');
    }


    /*
     *  crop ảnh ở tâm ra , kích thước mặc định vuông : 250px x 250px
     */

    function resize_crop_image($max_width = 250, $max_height = 250, $source_file, $dst_dir, $quality = 80)
    {
        // dd(1,$source_file, );
        try {
            $imgsize = getimagesize(url($source_file));
        } catch (\Throwable $th) {
            $imgsize = $this->getimagesize_function(url($source_file));
        }

        $width = $imgsize[0];
        $height = $imgsize[1];
        $mime = $imgsize['mime'];

        switch ($mime) {
            case 'image/gif':
                $image_create = "imagecreatefromgif";
                $image = "imagegif";
                break;

            case 'image/png':
                $image_create = "imagecreatefrompng";
                $image = "imagepng";
                $quality = 7;
                break;

            case 'image/jpeg':
                $image_create = "imagecreatefromjpeg";
                $image = "imagejpeg";
                $quality = 80;
                break;

            default:
                return false;
                break;
        }

        $dst_img = imagecreatetruecolor($max_width, $max_height);
        $src_img = $image_create($source_file);

        $width_new = $height * $max_width / $max_height;
        $height_new = $width * $max_height / $max_width;
        //if the new width is greater than the actual width of the image, then the height is too large and the rest cut off, or vice versa
        if ($width_new > $width) {
            //cut point by height
            $h_point = (($height - $height_new) / 2);
            //copy image
            imagecopyresampled($dst_img, $src_img, 0, 0, 0, $h_point, $max_width, $max_height, $width, $height_new);
        } else {
            //cut point by width
            $w_point = (($width - $width_new) / 2);
            imagecopyresampled($dst_img, $src_img, 0, 0, $w_point, 0, $max_width, $max_height, $width_new, $height);
        }


        $image($dst_img, $dst_dir, $quality);

        if ($dst_img)
            imagedestroy($dst_img);
        if ($src_img)
            imagedestroy($src_img);
    }


    public function login()
    {


        $user = auth()->user();

        $hasAuthOrToken = auth()->check()
            || (
                is_object($user) && (
                    method_exists($user, 'token')
                    || property_exists($user, 'token')
                    || isset($user->token)
                )
            );

        if ($hasAuthOrToken) {
            return redirect()->route('booking.index');
        }

        $error_active_code = "";
        if (Session::has('error_active_code')) {
            $error_active_code = Session::get('error_active_code');
            Session::forget("error_active_code");
        }

        //xoá session hiển thị decode
        if (Session::has('device_code')) {
            $device_code = Session::get('device_code');
            Session::forget("device_code");
        }

        //nếu đã có file license và không có lỗi (ví dụ ko bị hết hạn)
        if (Storage::exists('kztech.txt') and $error_active_code == "") {


            if (view()->exists('auth.authenticate')) {

                return view('auth.authenticate');
            }

            // dd(555);
            return view('auth.login_new');
            // return view('auth.login');
        }

        return view('auth.devicecode', ["error_active_code" => $error_active_code, "device_code" => $device_code]);
    }




    public function check_login(LoginRequest $request)
    {
        //xoá hết  login ở trước đó , để huỷ login ở mobile đi-----
        Session::flush();
        Auth::logout();
        //xoá hết  login ở trước đó , để huỷ login ở mobile đi-----

        $credentials = $request->only('user_name', 'password');
        $credentials['is_delete'] = 0;

        if (Auth::guard('web')->attempt($credentials)) {

            $user = Auth::guard('web')->user();

            if ($user->type == 1) {
                $is_login = 1;

                if ($is_login == 1) {

                    //tạo session login_mobile và web , để check trong mọi request
                    session()->forget('login_mobile');
                    session(['login_desktop' => config('login.login_desktop')]);


                    session(['token' => $user->access_token]);
                    session(['user_avatar' => $user->user_avatar]);
                    session(['user_name' => $user->user_name]);
                    session(['user_id' => $user->id]);
                    session(['type' => (int) $user->type]);
                    session(['user_company_id' => (int) $user->company_id]);
                    session(['roles' => (int) $user->role]);

                    // return redirect()->route('home');
                    return redirect()->route('home');

                } else {
                    echo "ko bao giờ rơi vao đây !";
                    exit;
                }
            } elseif ((int) $user->is_classification_customer === 1) {

                Session::flush();
                Auth::logout();

                return redirect()->back()->withErrors(['login' => 'Không được đăng nhập vì không phải tài khoản admin!']);
            } else {
                Session::flush();
                Auth::logout();

                return redirect()->back()->withErrors(['login' => 'Không thể dùng tài khoản thiết bị để đăng nhập vào hệ thống!']);
            }
        }


        return redirect()->back()->withErrors(['login' => 'Sai tài khoản !']);
    }

    public function check_active(Request $request)
    {
        session()->flush();
        return redirect()->route('home');
    }

    public function logout()
    {
        Session::flush();
        Auth::logout();
        return redirect()->route('login');
    }

    // public function getFormAddcompanyToUser($id)
    // {
    //     $breadcrumb = [
    //         [
    //             'title' => 'Trang chủ',
    //             'route' => 'home'
    //         ],
    //         [
    //             'title' => 'Quản lý tài khoản',
    //             'route' => 'user'
    //         ],
    //         [
    //             'title' => 'Chọn công ty'
    //         ]
    //     ];

    //     $user = $this->userRepo->getById($id);

    //     if (!empty($user->list_company_id)) {
    //         # code...
    //         if (!empty(json_decode($user->list_company_id))) {
    //             # code...
    //             $list_company = json_decode($user->list_company_id);
    //         } else {
    //             $list_company = [];
    //         }
    //     } else {
    //         $list_company = [];
    //     }
    //     //  dd($user);
    //     $companise = $this->companyRepository->getAll();
    //     return view('admin.user.add_companies', [
    //         'user' => $user,
    //         'list_company_id' => $list_company,
    //         'companise' => $companise,
    //         'breadcrumb' => $breadcrumb,
    //     ]);
    // }

    public function updateFormAddcompanyToUser(Request $request)
    {
        try {
            $list_company = $request->company;
            $list_company = json_encode($list_company);

            $this->userRepo->update($request->user_id, ['list_company_id' => $list_company]);
            return redirect()->route('user')->with('alert-success', 'Lưu thành công!');
        } catch (\Throwable $th) {
            return redirect()->back()->with('alert-error', 'Có lỗi xảy ra trong quá trình lưu!');
        }
    }

    protected function getToken()
    {
        return Str::random(500);
    }

    public function getFormAddTicketTypeToUser($id)
    {
        $this->authorize('ticket_user');
        $breadcrumb = [
            [
                'title' => 'Trang chủ',
                'route' => 'home'
            ],
            [
                'title' => 'Quản lý tài khoản',
                'route' => 'user'
            ],
            [
                'title' => 'Gán loại vé'
            ]
        ];

        // $type_tickets = $this->ticketTypeRepo->getData(['type_of_ticket' => 'offline'], 0, ['areas']);
        // $areas = $this->areaRepository->getAll();
        $user = $this->userRepo->getById($id);
        // dd($ticketType, $areas);
        return view('admin.user.add_ticket_type', compact('breadcrumb', 'user'));
    }

    public function saveFormAddTicketTypeToUser(Request $request, $id)
    {
        $this->authorize('ticket_user');
        try {
            DB::beginTransaction();
            $list_ticket_type = json_encode($request->ticket_type_id);
            $this->userRepo->update($id, ['list_ticket_type' => $list_ticket_type]);

            DB::commit();
            return redirect()->route('user')->with('alert-success', 'Lưu thành công!');
        } catch (\Throwable $th) {
            DB::rollBack();
            return redirect()->back()->with('alert-error', 'Có lỗi xảy ra trong quá trình lưu!');
        }
    }
}

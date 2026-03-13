<?php

namespace App\Http\Controllers\Web;

use App\Http\Controllers\Controller;
use App\Http\Requests\RoleRequest;
use App\Models\User;

// use App\Http\Requests\company\UpdateCompanyRequest;
// use App\Http\Requests\company\CreateCompanyRequest;
use Illuminate\Support\Facades\View;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\Auth;
use Illuminate\Support\Facades\DB;
use Spatie\Permission\Models\Permission;

use App\Repositories\Role\RoleRepositoryInterface;

use Illuminate\Support\Facades\Cache;
use Spatie\Permission\Models\Role;


//use Spatie\Permission\Models\Role as RoleLibrary;
//use App\Models\Role;

class RoleController extends Controller
{
    protected $repository;
    protected $modelPermission;

    public function __construct(
        RoleRepositoryInterface $repository,
        Permission $modelPermission
    ) {
        $this->repository = $repository;
        $this->modelPermission = $modelPermission;

        //---------------------------------------
        $permission = $this->modelPermission::get()
            ->groupBy('module');

        View::share('permission', $permission);
        //---------------------------------------
    }

    /**
     * @param Request $request
     * @return \Illuminate\Contracts\Foundation\Application|\Illuminate\Contracts\View\Factory|\Illuminate\Contracts\View\View
     */
    public function index(Request $request)
    {
        // $this->resetSessionSearch();
        // $this->authorize('index_role');
        $breadcrumb = [
            [
                'title' => 'Trang chủ',
                'route' => 'home'
            ],
            [
                'title' => 'Quản trị vai trò',
                'route' => 'role.index'
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

            return redirect('admin/role');
        }

        //kích vào link chuyển trang
        if (isset($request->page) and (int) $request->page > 0)
            session(['search.page' => (int) $request->page]);


        if (Auth::user()->type > 1) {
            $filter['company_id'] = Auth::user()->company_id;
        }

        $search_option = [];
        $limit = 20;
        //        $data = $this->repository->getWithFilter($filter, $limit);
        $data = $this->repository->getWithFilter(session('search.key_search'), $limit, session('search.page'), $search_option);

        return view('admin.role.index', [
            'data' => $data,
            //            'search' => $search,
            'limit' => $limit,
            'breadcrumb' => $breadcrumb
        ]);
    }

    public function showCreateForm()
    {

        // $this->authorize('create_role');
        $breadcrumb = [
            [
                'title' => 'Trang chủ',
                'route' => 'home'
            ],
            [
                'title' => 'Quản trị vai trò',
                'route' => 'role.index'
            ],
            [
                'title' => 'Thêm vai trò'
            ],
        ];


        return view(
            'admin.role.form',
            [
                'breadcrumb' => $breadcrumb,
                'action' => 'role.create',
            ]
        );
    }

    public function create(RoleRequest $request)
    {
        // $this->authorize('create_role');
        $dataRequest = $request->all();


        $data = [
            // 'description' =>  isset($dataRequest['description']) ? (strtoupper($dataRequest['description'])) : null,
            'guard_name' => 'web',
            'name' => isset($dataRequest['name']) ? $dataRequest['name'] : null,

            //1:trưởng phòng, 0: nhân viên , null: ko thuộc 2 nhóm trên
            'is_manager' => isset($dataRequest['is_manager']) ? (int) $dataRequest['is_manager'] : 0,
            'is_delete' => 0,
        ];


        DB::beginTransaction();
        try {
            // Lấy ra một vai trò
            $role = $this->repository->create($data);
            if (count($request->permission) > 0) {
                // Danh sách các quyền cần đồng bộ hóa
                $permissions = Permission::whereIn('id', $request->permission)->get();

                //phương thức này sẽ loại bỏ tất cả các quyền không được chỉ định và thêm các quyền mới nếu cần.
                //nó chỉ vào bảng model_has_permissions
                $role->syncPermissions($permissions);

                //insert vào bảng role_has_permissions
                foreach ($permissions as $value) {
                    DB::table('role_has_permissions')->insert([
                        'role_id' => $role->id, // Thay thế bằng ID của vai trò
                        'permission_id' => $value->id, // Thay thế bằng ID của quyền
                    ]);
                }
            }
            DB::commit();
            Cache::flush();
            return redirect()->route('role.index')->with('alert-success', 'Thêm vai trò thành công!');
        } catch (\Throwable $th) {
            dd($th->getMessage());
            DB::rollBack();
            return redirect()->back()->with('alert-error', 'Có lỗi sảy ra trong quá trình thêm mới!');
        }

        // dd($result);
    }


    public function showEditForm(int $id)
    {
        //        $this->authorize('update_role');
        $breadcrumb = [
            [
                'title' => 'Trang chủ',
                'route' => 'home'
            ],
            [
                'title' => 'Quản trị vai trò',
                'route' => 'role.index'
            ],
            [
                'title' => 'Chỉnh sửa vai trò'
            ],
        ];

        try {

            //tìm Role theo id
            $data = $this->repository->getById($id);

            return view(
                'admin.role.form',
                [
                    'data' => $data,
                    'breadcrumb' => $breadcrumb,
                    'action' => 'role.update'
                ]
            );
        } catch (\Throwable $th) {
            return redirect()->back()->with('alert-error', 'Không tìm thấy vai trò');
        }
    }

    public function update(RoleRequest $request)
    {
        //        $this->authorize('update_role');
 $dataRequest =  $request->all();
        $data = [
            'description' => isset($dataRequest['description']) ? (strtoupper($dataRequest['description'])) : null,
            'guard_name' => 'web',
            'name' => isset($dataRequest['name']) ? $dataRequest['name'] : null,
            //1:trưởng phòng, 0: nhân viên , null: ko thuộc 2 nhóm trên
            'is_manager' => isset($dataRequest['is_manager']) ? (int) $dataRequest['is_manager'] : null,
            'is_delete' => 0,
        ];

        try {

            $this->repository->update(intval($request->id), $data);

            $role = $this->repository->getById(intval($request->id));
            //                $role = Role::findById(intval($request->id));

            $permissions = Permission::whereIn('id', $request->permission)->get();

            //Xóa tất cả các quyền hiện tại của vai trò, Gán lại các quyền từ danh sách $permissions cho vai trò.
            $role->syncPermissions($permissions);

            DB::table('role_has_permissions')->where('role_id', $request->id)
                ->delete();

            foreach ($permissions as $value) {
                DB::table('role_has_permissions')->insert([
                    'role_id' => $request->id, // Thay thế bằng ID của vai trò
                    'permission_id' => $value->id, // Thay thế bằng ID của quyền
                ]);
            }
            Cache::flush();
            DB::commit();

            return redirect()->route('role.index')->with('alert-success', 'Cập nhật vai trò thành công!');
        } catch (\Throwable $th) {
            DB::rollBack();
            return redirect()->back()->with('alert-error', 'Có lỗi sảy ra trong quá trình update!');
        }
    }

    public function delete($id)
    {

        //        $this->authorize('delete_role');
        $this->repository->delete(intval($id));

        return response()->json(['success' => 'Xoá vài trò thành công !']);
    }
}

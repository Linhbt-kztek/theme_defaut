<?php

namespace App\Repositories\Role;

//use App\Models\Role as ModelsRole;
//use Spatie\Permission\Models\Role as ModelsRole;


use App\Models\Role ;
use App\Repositories\BaseRepository;


class RoleRepository extends BaseRepository implements RoleRepositoryInterface
{
    public function getModel()
    {
        return Role::class;
    }

    //public function getWithFilter($filter = [], $paging)
    public function getWithFilter($keyword, $paging, $page, $search_option = [])
    {

        $keyword_val = isset($keyword) ? trim($keyword) : '';

        $data =  $this->model::where(function ($query) use ($keyword_val, $search_option) {

            if ($keyword_val != '') {

//                $query->where(function ($q1) use ($keyword_val) {
//                    $q1->where('name', 'like', '%' . $keyword_val . '%')
//                        ->orWhere('code', 'like', '%' . $keyword_val . '%')
//                        ->orWhere('phone', 'like', '%' . $keyword_val . '%')
//                        ->orwhereHas('user', function ($q2) use ($keyword_val) {
//                            $q2->where('user_name', 'like', '%' . $keyword_val . '%');
//                        });
//                });

            }

            $query->where('is_delete', '=', 0);

            //tìm kiếm trong đk tìm kiếm
            if (isset($search_option) and count($search_option) > 0) {

                foreach ($search_option as $v) {
                    // dd($v);
                    //nó ko phải là mảng của 3 phần tử  ,mà nó là 3 phần tử truyền vào thôi
                    $query->where($v[0], $v[1], $v[2]);
                }
            }

//            if (!$is_api) {
//                $query->whereIn('company_id', $inArrayCompanyId);
//            }
        });

        $data->orderBy('created_at', 'desc');

        if (isset($page) and $page == -1) {
            return $data->get();
        }

        return $data->paginate($paging);

//        if ($paging == 0) {
//            return $data->get();
//        } else {
//            return $data->paginate($paging);
//        }
    }

    public function checkName(string $name, $notId = null)
    {
        $data = $this->model->where('name', $name);

        if (!empty($notId)) {
            $data = $data->whereKeyNot($notId);
        }
      //  dd($data->get()->count());
        if ($data->get()->count() == 0) {
//        if (count($data->get()) == 0) {
            return false ;
        } else {
            return true;
        }
    }
}

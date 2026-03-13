<?php

namespace App\Repositories\User;

use App\Models\User;
use App\Repositories\BaseRepository;

class UserRepository extends BaseRepository implements UserRepositoryInterface
{
    /**
     * @return string
     *  Return the model
     */
    public function getModel()
    {
        return User::class;
    }

    public function getWithFilter($keyword, $paging, $page, $search_option = [])
    {

        $keyword_val = isset($keyword) ? trim($keyword) : '';

        $data =  $this->model::where(function ($query) use ($keyword_val, $search_option) {

            if ($keyword_val != '') {

                $query->where(function ($q1) use ($keyword_val) {
                    $q1->where('name', 'like', '%' . $keyword_val . '%')
//                        ->orWhere('code', 'like', '%' . $keyword_val . '%')
                        ->orWhere('phone', 'like', '%' . $keyword_val . '%')
                        ->orWhere('user_name', 'like', '%' . $keyword_val . '%');

                });

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

        });

        $data->orderBy('created_at', 'desc');

        if (isset($page) and $page == -1) {
            return $data->get();
        }

        return $data->paginate($paging);
    }



    public function getDataByFilter(array $dataFilter)
    {
        $data =  $this->model::where(
            function ($query) use ($dataFilter) {

                if (!empty($dataFilter)) {
                    foreach ($dataFilter as $key => $value) {
                        $query->where($key, $value);
                    }

                    $query->where('deleted_at', '=', null);
                }
            }
        );
        // dd($data->get());
        return $data->get();
    }

    public function getUserByEmail($email)
    {
        $result = $this->model->where('email', $email)->first();
        if ($result) {
            return $result;
        }
        return false;
    }

    public function getUserByUsername($username)
    {
        $result = $this->model->where('user_name', $username)->first();
        if (isset($result)) {
            return $result;
        }
        return false;
    }

}

<?php

namespace App\Repositories\Log;

use App\Models\Log;
use App\Repositories\BaseRepository;

class LogRepository extends BaseRepository implements LogRepositoryInterface
{
    public function getModel()
    {
        return Log::class;
    }

    public function getWithFilter($keyword, $paging, $page, $search_option = [], $inArrayCompanyId = [])
    {
        //dd($customer_id);
        $keyword_val = isset($keyword) ? trim($keyword) : '';


        $data =  $this->model::where(function ($query) use ($keyword_val, $search_option, $inArrayCompanyId) {

            if ($keyword_val != '') {
//                $query->where(function ($q1) use ($keyword_val) {
//                    $q1->where('name', 'like', '%' . $keyword_val . '%')
//                        ->orWhere('code', 'like', '%' . $keyword_val . '%')
//                        ->orWhere('phone', 'like', '%' . $keyword_val . '%')
//                        ->orWhere('card_id', 'like', '%' . $keyword_val . '%');
//                });
            }

            $query->where('is_delete', '=', 0);

            // dd($search_option);

            // Log::error($search_option);
            //tìm kiếm trong đk tìm kiếm
            if (isset($search_option) and count($search_option) > 0) {

                foreach ($search_option as $v) {
                    // dd($v);
                    //nó ko phải là mảng của 3 phần tử  ,mà nó là 3 phần tử truyền vào thôi
                    $query->where($v[0], $v[1], $v[2]);
                }
            }

            //  $query->whereIn('company_id', $inArrayCompanyId);
        });

        // dd(count($data->get()));
        $data->orderBy('created_at', 'desc');

        if (isset($page) and $page == -1) {
            return $data->get();
        }

        return $data->paginate($paging);
    }

//    public function getWithFilter($keyword,$paging)
//    {
//        $keyword_val = isset($keyword) ? trim($keyword) : '';
//        return $this->model::where('table_name', 'like', '%'.$keyword_val.'%')->paginate($paging);
//
//    }

    /**
     * @param $dataRequest
     * @return bool
     *
     * hàm này hiện tai chưa dùng đến , sẽ dùng trong các trường hợp ghi log đặc biệt
     */
    public function lists($fullTextSearch, $query, $page)
    {
//        $data = $this->model->search($fullTextSearch)->query(function ($q) use ($query) {
//            $q->where($query)
//            ->orderBy('modified_date', 'desc')
//            ->orderBy('date_created', 'desc');
//        });
        $data = $this->model->where($query)->orderBy('created_at', 'desc');
        if($page == -1){
            return $data->get();
        }
        return $data->paginate($page);
    }

    public function saveLog(string $model, string $action, array $before_action, array $after_action, $moreOptions = [])
    {

        try {
            $data = [
                'id' => getGUID(),
                'user_id' => auth()->id(),
                'table_name' => $model,
                'action' => $action,
                'data_old' => json_encode($before_action),
                'data_new' => json_encode($after_action),
                'is_delete' => 0
            ];
            if (isset($moreOptions)) {
                foreach ($moreOptions as $key => $moreOption){
                    $data[$key] = $moreOption;
                }
            }
            $this->create($data);
            return true;
        } catch (\Throwable $th) {
            return $th->getMessage();
        }
    }
}

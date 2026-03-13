<?php

namespace App\Repositories;

use Illuminate\Support\Facades\DB;
use Illuminate\Support\Facades\Log;

abstract class BaseRepository implements BaseRepositoryInterface
{
    // model muốn tương tác
    protected $model;

    public function __construct()
    {
        $this->setModel();
    }

    abstract public function getModel();

    public function setModel()
    {
        $this->model = app()->make($this->getModel());
    }


    public function getAll($columns = '')
    {
        if ($columns != '') {
            return $this->model->where('deleted_at', '=', null)->get($columns);
        } else {
            return $this->model->where('deleted_at', '=', null)->get();
        }
    }

    public function getAllNoneGet()
    {
        return $this->model->where('deleted_at', '=', null);
    }

    public function query()
    {
        return $this->model->query();
    }

    public function getById($id, $with = [])
    {
        return $this->model->where('deleted_at', '=', null)->with($with)->find($id);
    }

    public function getByIdHaveDelete($id, $with = [])
    {
        return $this->model->with($with)->withTrashed()->find($id);
    }

    // public function findWhere(array $where, $columns = array('*'))
    // {
    //     $this->applyConditions($where);
    //     return $this->model->orderBy('id', 'DESC')->get($columns);
    // }

    public function getByField($field, $value)
    {
        return $this->model->where('deleted_at', '=', null)->where($field, '=', $value);
    }

    public function create($attributes, $showError = false)
    {
        DB::beginTransaction();
        try {
            $result = $this->model->create($attributes);
            DB::commit();
            return $result;
        } catch (\Illuminate\Database\QueryException $exception) {
            dd($exception->getMessage());

            DB::rollBack();
            if ($showError) {
                return $exception->getMessage();
            }
            $error["error"] = $exception->errorInfo;
            $error["request"] = $attributes;
            Log::error($error["error"]);
            Log::error($error["request"]);
            return false;
        }
    }

    public function createMutiData($attributes, $getDataCreate = false)
    {
        DB::beginTransaction();
        $data_created = [];
        try {
            $has_error = false;
            $message = '';
            foreach ($attributes as $key => $attribute) {
                if ($has_error) {
                    break;
                } else {
                    $create = $this->create($attribute);
                    if (!$create) {
                        $has_error = true;
                        $message = 'Có lỗi xảy ra trong việc tạo dữ liệu key= ' . $key;
                    } elseif ($getDataCreate) {
                        $data_created[] = $create;
                    }
                }
            }
            if (!$has_error) {
                DB::commit();
                if ($getDataCreate) {
                    $data_return = [
                        'status' => 200,
                        'message' => $message,
                        'data' => $data_created
                    ];
                } else {
                    $data_return = [
                        'status' => 200,
                        'message' => $message
                    ];
                }
            } else {
                DB::rollBack();
                $data_return = [
                    'status' => 90,
                    'message' => $message
                ];
            }
        } catch (\Throwable $th) {
            DB::rollBack();
            $data_return = [
                'status' => 90,
                'message' => $th->getMessage()
            ];
        }
        return $data_return;
    }


    public function update($id, $attributes)
    {

        $result = $this->getById($id);
        if ($result) {
            DB::beginTransaction();
            try {
                $return = $result->update($attributes);
                DB::commit();
                return $return;
            } catch (\Illuminate\Database\QueryException $exception) {
                dd($exception);
                DB::rollBack();
                $error["error"] = $exception->errorInfo;
                Log::error($exception->errorInfo);
                Log::error($attributes);
                return false;
            }
        }
        return false;
    }

    public function updateMutiData($attributes)
    {
        try {
            DB::beginTransaction();
            $has_error = false;
            $message = '';
            foreach ($attributes as $key => $attribute) {
                if ($has_error) {
                    break;
                } else {
                    $update = $this->update($key, $attribute);
                    if (!$update) {
                        $has_error = true;
                        $message = 'Có lỗi xảy ra trong việc cập nhật dữ liệu key= ' . $key;
                    }
                }
            }

            if (!$has_error) {
                DB::commit();
                $data_return = [
                    'status' => 200,
                    'message' => $message
                ];
            } else {
                $data_return = [
                    'status' => 90,
                    'message' => $message
                ];
            }
        } catch (\Throwable $th) {
            DB::rollBack();
            $data_return = [
                'status' => 90,
                'message' => $th->getMessage()
            ];
        }
        return $data_return;
    }

    // public function updateT($id, $attributes)
    // {
    //     $result = $this->model->where('is_delete', 0)->find($id);
    //     if ($result) {
    //         DB::beginTransaction();
    //         try {
    //             $result->update($attributes);
    //             DB::commit();
    //             return $result;
    //         } catch (\Illuminate\Database\QueryException $exception) {
    //             DB::rollBack();
    //             $error["error"] = $exception->errorInfo;
    //             Log::error($exception->errorInfo);
    //             Log::error($attributes);
    //             return false;
    //         }
    //     }
    //     return false;
    // }

    public function delete($id)
    {
        $result = $this->getById($id);

        if ($result) {
            DB::beginTransaction();
            try {
                $result->fill(["is_delete" => 1, "deleted_at" => date("Y-m-d G:i:s")]);
                $result->save();
                $result->delete();
                DB::commit();
                return true;
            } catch (\Illuminate\Database\QueryException $exception) {
                DB::rollBack();
                $error["error"] = $exception->errorInfo;
                Log::error($exception->errorInfo);

                return false;
            }
        }
        return false;
    }

    // public function update_where($where = [], $update = [])
    // {
    //     DB::beginTransaction();
    //     try {
    //         $this->model->where($where)->update($update);
    //         DB::commit();
    //         return true;
    //     } catch (\Throwable $exception) {
    //         DB::rollBack();
    //         Log::error($exception->getMessage());
    //         return false;
    //     }
    // }

    //chuyển tiếng việt có dấu sang ko dấu
    // function convert_name($str)
    // {
    //     $str = preg_replace("/(à|á|ạ|ả|ã|â|ầ|ấ|ậ|ẩ|ẫ|ă|ằ|ắ|ặ|ẳ|ẵ)/", 'a', $str);
    //     $str = preg_replace("/(è|é|ẹ|ẻ|ẽ|ê|ề|ế|ệ|ể|ễ)/", 'e', $str);
    //     $str = preg_replace("/(ì|í|ị|ỉ|ĩ)/", 'i', $str);
    //     $str = preg_replace("/(ò|ó|ọ|ỏ|õ|ô|ồ|ố|ộ|ổ|ỗ|ơ|ờ|ớ|ợ|ở|ỡ)/", 'o', $str);
    //     $str = preg_replace("/(ù|ú|ụ|ủ|ũ|ư|ừ|ứ|ự|ử|ữ)/", 'u', $str);
    //     $str = preg_replace("/(ỳ|ý|ỵ|ỷ|ỹ)/", 'y', $str);
    //     $str = preg_replace("/(đ)/", 'd', $str);
    //     $str = preg_replace("/(À|Á|Ạ|Ả|Ã|Â|Ầ|Ấ|Ậ|Ẩ|Ẫ|Ă|Ằ|Ắ|Ặ|Ẳ|Ẵ)/", 'A', $str);
    //     $str = preg_replace("/(È|É|Ẹ|Ẻ|Ẽ|Ê|Ề|Ế|Ệ|Ể|Ễ)/", 'E', $str);
    //     $str = preg_replace("/(Ì|Í|Ị|Ỉ|Ĩ)/", 'I', $str);
    //     $str = preg_replace("/(Ò|Ó|Ọ|Ỏ|Õ|Ô|Ồ|Ố|Ộ|Ổ|Ỗ|Ơ|Ờ|Ớ|Ợ|Ở|Ỡ)/", 'O', $str);
    //     $str = preg_replace("/(Ù|Ú|Ụ|Ủ|Ũ|Ư|Ừ|Ứ|Ự|Ử|Ữ)/", 'U', $str);
    //     $str = preg_replace("/(Ỳ|Ý|Ỵ|Ỷ|Ỹ)/", 'Y', $str);
    //     $str = preg_replace("/(Đ)/", 'D', $str);
    //     $str = preg_replace("/(\“|\”|\‘|\’|\,|\!|\&|\;|\@|\#|\%|\~|\`|\=|\_|\'|\]|\[|\}|\{|\)|\(|\+|\^)/", '-', $str);
    //     $str = preg_replace("/( )/", '-', $str);
    //     return $str;
    // }


    // public function db_select($sql_select, $arr_variable)
    // {
    //     try {
    //         return DB::select($sql_select, $arr_variable);
    //     } catch (\Illuminate\Database\QueryException $exception) {
    //         $error["error"] = $exception->errorInfo;
    //         Log::error($exception->errorInfo);
    //         exit;
    //     }
    // }

    // public function db_update($sql_update, $arr_variable)
    // {
    //     DB::beginTransaction();
    //     try {
    //         DB::update($sql_update, $arr_variable);

    //         DB::commit();

    //         return true;
    //     } catch (\Illuminate\Database\QueryException $exception) {

    //         DB::rollBack();

    //         $error["error"] = $exception->errorInfo;
    //         Log::error($exception->errorInfo);
    //         exit;
    //     }
    // }

    // public function db_insert($sql_select, $arr_variable)
    // {
    //     DB::beginTransaction();
    //     try {
    //         DB::insert($sql_select, $arr_variable);
    //         DB::commit();
    //         return true;
    //     } catch (\Illuminate\Database\QueryException $exception) {
    //         DB::rollBack();
    //         $error["error"] = $exception->errorInfo;
    //         Log::error($exception->errorInfo);
    //         exit;
    //     }
    // }

    public function paginateWhereLikeOrderBy(array $where, array $whereLike, $order_by = 'updated_at', $order = 'DESC', $current_page = null, $limit = null, $columns = array('*'), $primitive_data = false, $with = [], $whereHas = [], $whereDoesntHave = [])
    {
        $i = 0;
        $limit = is_null($limit) ? config('repository.pagination.limit', 10) : $limit;
        $current_page = is_null($current_page) ? config('repository.pagination.limit', 1) : $current_page;

        if (!empty($whereLike)) {
            $this->model = $this->model->where(function ($q) use ($whereLike, $i) {
                foreach ($whereLike as $fd => $val) {
                    if ($i == 0) {
                        $q->where($fd, 'LIKE', "%$val%");
                    } else {
                        $q->orWhere($fd, 'LIKE', "%$val%");
                    }
                    $i++;
                }
            });
        }

        // Lọc theo các điều kiện whereHas
        if (!empty($whereHas)) {
            foreach ($whereHas as $relation => $conditions) {
                $this->model = $this->model->whereHas($relation, function ($query) use ($conditions) {
                    foreach ($conditions as $field => $value) {
                        $query->where($field, $value);
                    }
                });
            }
        }

        // Lọc theo các điều kiện whereDoesntHave
        if (!empty($whereDoesntHave)) {
            foreach ($whereDoesntHave as $relation) {
                $this->model = $this->model->whereDoesntHave($relation);
            }
        }

        if (!empty($where)) {
            $this->applyConditions($where);
        }

        if (!empty($with)) {
            $this->model->with($with);
        }

        if ($primitive_data) {
            $results = $this->model->orderBy($order_by, $order);
        } else {
            $results = $this->model->orderBy($order_by, $order)->paginate($limit, $columns, 'page', $current_page);
        }
        $this->setModel();
        return $results;
    }

    // protected function applyConditions(array $where)
    // {
    //     foreach ($where as $field => $value) {
    //         if (is_array($value)) {
    //             if (count($value) == 2) {
    //                 $condition = '=';
    //                 list($field, $val) = $value;
    //             } elseif (count($value) == 1) {
    //                 $field = $value[0];
    //                 $condition = null;
    //                 $val = null;
    //             } else {
    //                 list($field, $condition, $val) = $value;
    //             }
    //             //smooth input
    //             $condition = preg_replace('/\s\s+/', ' ', trim($condition));

    //             //split to get operator, syntax: "DATE >", "DATE =", "DAY <"
    //             $operator = explode(' ', $condition);
    //             if (count($operator) > 1) {
    //                 $condition = $operator[0];
    //                 $operator = $operator[1];
    //             } else
    //                 $operator = null;
    //             switch (strtoupper($condition)) {
    //                 case 'IN':
    //                     if (!is_array($val))
    //                         throw new \Exception("Input {$val} mus be an array");
    //                     $this->model = $this->model->whereIn($field, $val);
    //                     break;
    //                 case 'NOTIN':
    //                     if (!is_array($val))
    //                         throw new \Exception("Input {$val} mus be an array");
    //                     $this->model = $this->model->whereNotIn($field, $val);
    //                     break;
    //                 case 'DATE':
    //                     if (!$operator)
    //                         $operator = '=';
    //                     $this->model = $this->model->whereDate($field, $operator, $val);
    //                     break;
    //                 case 'DAY':
    //                     if (!$operator)
    //                         $operator = '=';
    //                     $this->model = $this->model->whereDay($field, $operator, $val);
    //                     break;
    //                 case 'MONTH':
    //                     if (!$operator)
    //                         $operator = '=';
    //                     $this->model = $this->model->whereMonth($field, $operator, $val);
    //                     break;
    //                 case 'YEAR':
    //                     if (!$operator)
    //                         $operator = '=';
    //                     $this->model = $this->model->whereYear($field, $operator, $val);
    //                     break;
    //                 case 'EXISTS':
    //                     if (!($val instanceof \Closure))
    //                         throw new \Exception("Input {$val} must be closure function");
    //                     $this->model = $this->model->whereExists($val);
    //                     break;
    //                 case 'HAS':
    //                     if (!($val instanceof \Closure))
    //                         throw new \Exception("Input {$val} must be closure function");
    //                     $this->model = $this->model->whereHas($field, $val);
    //                     break;
    //                 case 'HASMORPH':
    //                     if (!($val instanceof \Closure))
    //                         throw new \Exception("Input {$val} must be closure function");
    //                     $this->model = $this->model->whereHasMorph($field, $val);
    //                     break;
    //                 case 'DOESNTHAVE':
    //                     if (!($val instanceof \Closure))
    //                         throw new \Exception("Input {$val} must be closure function");
    //                     $this->model = $this->model->whereDoesntHave($field, $val);
    //                     break;
    //                 case 'DOESNTHAVEMORPH':
    //                     if (!($val instanceof \Closure))
    //                         throw new \Exception("Input {$val} must be closure function");
    //                     $this->model = $this->model->whereDoesntHaveMorph($field, $val);
    //                     break;
    //                 case 'BETWEEN':
    //                     if (!is_array($val))
    //                         throw new \Exception("Input {$val} mus be an array");
    //                     $this->model = $this->model->whereBetween($field, $val);
    //                     break;
    //                 case 'BETWEENCOLUMNS':
    //                     if (!is_array($val))
    //                         throw new \Exception("Input {$val} mus be an array");
    //                     $this->model = $this->model->whereBetweenColumns($field, $val);
    //                     break;
    //                 case 'NOTBETWEEN':
    //                     if (!is_array($val))
    //                         throw new \Exception("Input {$val} mus be an array");
    //                     $this->model = $this->model->whereNotBetween($field, $val);
    //                     break;
    //                 case 'NOTBETWEENCOLUMNS':
    //                     if (!is_array($val))
    //                         throw new \Exception("Input {$val} mus be an array");
    //                     $this->model = $this->model->whereNotBetweenColumns($field, $val);
    //                     break;
    //                 case 'RAW':
    //                     $this->model = $this->model->whereRaw($val);
    //                     break;
    //                 default:
    //                     if (empty($condition)) {
    //                         $this->model = $this->model->where($field);
    //                     } else {
    //                         $this->model = $this->model->where($field, $condition, $val);
    //                     }
    //             }
    //         } else {
    //             $this->model = $this->model->where($field, '=', $value);
    //         }
    //     }
    // }

    public function getDataAllOption($where = [], $limit = 0, $with = [], $orderBy = ['created_at' => 'desc'], $selectRaw = [])
    {
        $data = $this->model;

        if (!empty($where['filter']) && is_array($where['filter'])) {
            foreach ($where['filter'] as $filterValue) {
                $data = $data->where($filterValue[0], $filterValue[1], $filterValue[2]);
            }
        }

        if (!empty($where['whereLike'])) {
            foreach ($where['whereLike'] as $whereLikeKey => $whereLikeValue) {
                $data = $data->where($whereLikeKey, 'LIKE', "%$whereLikeValue%");
            }
        }
        if (!empty($where['orWhere']) || !empty($where['orWhereHas'])) {
            $data = $data->where(function ($query) use ($where) {

                if (!empty($where['orWhere'])) {
                    foreach ($where['orWhere'] as $index => $value) {
                        if ($index === 0) {
                            $query->where($value[0], $value[1], $value[2]);
                        } else {
                            $query->orWhere($value[0], $value[1], $value[2]);
                        }
                    }
                }

                if (!empty($where['orWhereHas'])) {
                    foreach ($where['orWhereHas'] as $relation => $conditions) {
                        $query->orWhereHas($relation, function ($q) use ($conditions) {

                            $q->where(function ($sub) use ($conditions) {
                                foreach ($conditions as $index => $cond) {

                                    if ($index === 0) {
                                        $sub->where($cond['colum'], $cond['operator'], $cond['value']);
                                    } else {
                                        $sub->orWhere($cond['colum'], $cond['operator'], $cond['value']);
                                    }
                                }
                            });

                        });

                    }
                }

            });
        }




        // if (!empty($where['orWhere'])) {
        //     $data = $data->where(function ($query) use ($where) {
        //         foreach ($where['orWhere'] as $key => $value) {
        //             if ($key > 0) {
        //                 $query->orWhere($value[0], $value[1], $value[2]);
        //             } else {
        //                 $query->where($value[0], $value[1], $value[2]);
        //             }
        //         }
        //     });
        // }
        // if (!empty($where['orWhereHas'])) {
        //     $data = $data->where(function ($query) use ($where) {
        //         $isFirst = true;

        //         foreach ($where['orWhereHas'] as $relation => $conditions) {
        //             if ($isFirst) {
        //                 $query->whereHas($relation, function ($q) use ($conditions) {
        //                     foreach ($conditions as $cond) {
        //                         if (isset($cond['type']) && $cond['type'] === 'whereDate') {
        //                             $q->whereDate($cond['colum'], $cond['operator'], $cond['value']);
        //                         } else {
        //                             $q->where($cond['colum'], $cond['operator'], $cond['value']);
        //                         }
        //                     }
        //                 });
        //                 $isFirst = false;
        //             } else {
        //                 $query->orWhereHas($relation, function ($q) use ($conditions) {
        //                     foreach ($conditions as $cond) {
        //                         if (isset($cond['type']) && $cond['type'] === 'whereDate') {
        //                             $q->whereDate($cond['colum'], $cond['operator'], $cond['value']);
        //                         } else {
        //                             $q->where($cond['colum'], $cond['operator'], $cond['value']);
        //                         }
        //                     }
        //                 });
        //             }
        //         }
        //     });
        // }

        if (!empty($where['whereOrLike'])) {
            $whereOrLike = $where['whereOrLike'];
            $data = $data->where(function ($query) use ($whereOrLike) {
                $count = 0;
                foreach ($whereOrLike as $whereLikeKey => $whereLikeValue) {
                    if ($count == 0) {
                        $query->where($whereLikeKey, 'LIKE', "%$whereLikeValue%");
                    } else {
                        $query->orWhere($whereLikeKey, 'LIKE', "%$whereLikeValue%");
                    }
                    $count++;
                }
            });
        }

        if (!empty($where['orWhereGroup'])) {

            $data = $data->where(function ($query) use ($where) {

                foreach ($where['orWhereGroup'] as $group) {

                    $query->orWhere(function ($sub) use ($group) {

                        foreach ($group as $cond) {
                            $sub->where($cond[0], $cond[1], $cond[2]);
                        }

                    });

                }

            });

        }


        if (!empty($where['whereIn']) && is_array($where['whereIn'])) {
            foreach ($where['whereIn'] as $whereInKey => $whereInValue) {
                if (!empty($whereInValue) && is_array($whereInValue)) { // Kiểm tra dữ liệu hợp lệ
                    $data = $data->whereIn($whereInKey, $whereInValue);
                }
            }
        }


        if (!empty($where['whereNotIn'])) {
            foreach ($where['whereNotIn'] as $whereNotInKey => $whereNotInValue) {
                $data = $data->whereNotIn($whereNotInKey, $whereNotInValue);
            }
        }

        if (!empty($where['whereHas'])) {
            foreach ($where['whereHas'] as $whereHasKey => $whereHasValue) {
                $data = $data->whereHas($whereHasKey, function ($query) use ($whereHasValue) {
                    // dd($whereHasValue);
                    foreach ($whereHasValue as $value) {
                        if (isset($value['type']) && $value['type'] === 'whereDate') {
                            $query->whereDate($value['colum'], $value['operator'], $value['value']);
                        } else {
                            $query->where($value['colum'], $value['operator'], $value['value']);
                        }
                    }
                });
            }
        }

        if (!empty($where['whereDoesntHave'])) {
            foreach ($where['whereDoesntHave'] as $whereDoesntHaveKey => $whereDoesntHaveValue) {
                $data = $data->whereDoesntHave($whereDoesntHaveKey, function ($query) use ($whereDoesntHaveValue) {
                    foreach ($whereDoesntHaveValue as $value) {
                        $query->where($value['colum'], $value['operator'], $value['value']);
                    }
                });
            }
        }

        //chi so sanh ngay , bo qua gio phut giay
        if (!empty($where['whereDate'])) {
            foreach ($where['whereDate'] as $column => $date) {
                $data = $data->whereDate($column, $date);
            }
        }

        // whereHas theo khoảng ngày (now → now + N ngày)
        // if (!empty($where['whereHasDateRange'])) {
        //     foreach ($where['whereHasDateRange'] as $relation => $config) {

        //         $from = $config['from'] ?? now();
        //         $to = $config['to'] ?? now();

        //         $data = $data->whereHas($relation, function ($q) use ($config, $from, $to) {

        //             $column = $config['column'] ?? 'date';

        //             $q->whereBetween(
        //                 $column,
        //                 [
        //                     \Carbon\Carbon::parse($from)->toDateString(),
        //                     \Carbon\Carbon::parse($to)->toDateString()
        //                 ]
        //             );
        //         });
        //     }
        // }


        if (!empty($with)) {
            $data = $data->with($with);
        }

        // Thêm selectRaw vào nếu có
        if (!empty($selectRaw)) {
            foreach ($selectRaw as $raw) {
                $data = $data->selectRaw($raw);
            }
        }

        if (!empty($orderBy)) {
            foreach ($orderBy as $key => $value) {
                $data = $data->orderBy($key, $value);
            }
        }

        // $data = $data->orderBy('id', 'desc');

        if ($limit > 0) {
            $data = $data->paginate($limit);
        } elseif ($limit == 0) {
            $data = $data->get();
        } elseif ($limit === -1) {
            $data;
        }

        return $data;
    }

}

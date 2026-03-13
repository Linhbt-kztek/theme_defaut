<?php

namespace App\Repositories;

interface BaseRepositoryInterface
{
    public function getAll($columns = '');
    public function getById($id, $with = []);
    public function getByIdHaveDelete($id, $with = []);
    public function create($attributes, $showError = false);
    public function createMutiData($attributes, $getDataCreate = false);
    public function update($id, $attributes);
    public function updateMutiData($attributes);
    public function delete($id);
    public function query();
    // public function update_where($where = [], $update = []);

    // public function db_select($sql_select, $arr_variable);

    // public function db_update($sql_update, $arr_variable);

    // public function db_insert($sql_select, $arr_variable);

    // public function paginateWhereLikeOrderBy(array $where, array $whereLike, $order_by = 'updated_at', $order = 'DESC', $current_page = null, $limit = null, $columns = array('*'), $primitive_data = false);
    public function getDataAllOption($where = [], $limit = 0, $with = [], $orderBy = ['created_at' => 'desc']);

}

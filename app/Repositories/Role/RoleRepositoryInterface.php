<?php

namespace App\Repositories\Role;

use App\Repositories\BaseRepositoryInterface;

interface RoleRepositoryInterface extends BaseRepositoryInterface
{

    public function getWithFilter($keyword, $paging, $page, $search_option = []);

    public function checkName(string $name, $notId = null);
}

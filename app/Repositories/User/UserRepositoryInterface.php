<?php
namespace App\Repositories\User;

use App\Repositories\BaseRepositoryInterface;

interface UserRepositoryInterface extends BaseRepositoryInterface
{
    public function getWithFilter($keyword, $paging, $page, $search_option = []);

    public function getUserByEmail($email);

    public function getUserByUsername($username);
}

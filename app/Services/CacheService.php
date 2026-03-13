<?php

namespace App\Services;

use App\Models\Config;
use Illuminate\Support\Facades\Cache;

class CacheService
{
    public function __construct()
    {

    }

    //đọc cấu hình config
    public function read_config()
    {
        return Cache::rememberForever(config('name_cache.config_web'), function () {

            $config = Config::find(1);
            $data =[];
            if(!empty($config->content)) $data = json_decode($config->content, true);
            return $data;
        });

    }
}

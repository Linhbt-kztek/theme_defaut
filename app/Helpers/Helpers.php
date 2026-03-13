<?php

use App\Models\Config;
use App\Services\MinioService;

if (!function_exists('getGUID')) {
    function getGUID()
    {
        mt_srand((double) microtime() * 10000);//optional for php 4.2.0 and up.
        $charid = strtoupper(md5(uniqid(mt_rand(), true)));
        $hyphen = chr(45);// "-"
        return substr($charid, 0, 8) . $hyphen
            . substr($charid, 8, 4) . $hyphen
            . substr($charid, 12, 4) . $hyphen
            . substr($charid, 16, 4) . $hyphen
            . substr($charid, 20, 12);
    }
}

if (!function_exists('getConfigForLogin')) {
    function getConfigForLogin($is_login = false)
    {
        $data_return["title_web"] = "";
        $data_return["link_logo"] = "";
        $data_return["hotline"] = "";
        $data_return["email"] = "";
        $data_return["address"] = "";
        $data_return["link_banner_login"] = url('images/backgroud-1.jpg');
        $data_return["link_logo_system"] = "";
        $data_return["link_logo_login"] = "";

        try {
            $config = Config::find(1);

            if ($config && !empty($config->content)) {
                $data_return = json_decode($config->content, true);
                $minioService = new MinioService();
                if (!empty($data_return["logo_system"])) {
                    $data_return["link_logo_system"] = $minioService->getCloudImage('logo_system', $data_return["logo_system"]);
                }

                if ($is_login) {
                    if (!empty($data_return["banner_login"])) {
                        $data_return["link_banner_login"] = $minioService->getCloudImage('banner_login', $data_return["banner_login"]);
                    }

                    if (!empty($data_return["logo_login"])) {
                        $data_return["link_logo_login"] = $minioService->getCloudImage('logo_login', $data_return["logo_login"]);
                    }
                }
            }
        } catch (\Throwable $th) {

        }
        return $data_return;
    }
}
<?php

namespace App\Services;

use App\Services\CacheService;
use Carbon\Carbon;
use App\Models\Config;
use Illuminate\Support\Facades\Cache;

class ConfigService
{
    public function __construct(
        protected CacheService $cacheServiceRepo,
    ) {
        //
    }

    //tính ngày hết hạn
    public function cal_expire_date($currentDate = null)
    {
        $currentDate = $currentDate ?? Carbon::now()->endOfDay();

        $config = $this->cacheServiceRepo->read_config();

        return $currentDate->addMonths((int) $config["expiry_date"]);

    }

    // Lấy cấu hình từ Cache hoặc DB
    public function getConfig()
    {
        // Kiểm tra xem cấu hình có trong cache không
        $config = Cache::get(config('name_cache.config_web'));

        // Nếu không có trong cache, lấy từ DB
        if (!$config) {
            
            $config = Config::find(1); // Lấy cấu hình từ DB

            if ($config && !empty($config->content)) {
                // Giải mã chuỗi JSON trong cột content và trả về mảng
                $config = json_decode($config->content, true);
            } else {
                // Nếu không có dữ liệu trong content, trả về mảng trống
                $config = [];
            }

            // Lưu cấu hình vào cache để dùng cho lần sau (dữ liệu đã là mảng)
            Cache::forever(config('name_cache.config_web'), $config);
        }

        return $config;
    }

    // Lấy cấu hình provider
    public function getProvider()
    {
        $config = $this->getConfig();
        return $config["invoiceConfiguration"]["provider"] ?? "";
    }

    // Lấy cấu hình bill
    public function getBillConfig()
    {
        $config = $this->getConfig();
        return $config["invoiceConfiguration"]["bill"] ?? [];
    }

    // Lấy cấu hình ticket
    public function getTicketConfig()
    {
        $config = $this->getConfig();
        return $config["invoiceConfiguration"]["ticket"] ?? [];
    }

    public function getCompanyInfo()
    {
        $config = $this->getConfig();

        return [
            'companyName' => $config['companyName'] ?? null,
            'taxCode' => $config['taxCode'] ?? null,
        ];
    }
}

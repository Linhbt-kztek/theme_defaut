<?php

namespace App\Http\Controllers\Admin;

use App\Http\Controllers\Controller;
use App\Http\Requests\Config\UpdateInvoiceRequest;
use App\Models\Config;
use App\Services\ConfigService;
use Illuminate\Http\RedirectResponse;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\DB;
use App\Services\MinioService;
use Illuminate\Support\Facades\Log;
use Illuminate\Support\Facades\Cache;

class ConfigController extends Controller
{

    private $id;

    public function __construct(
        protected MinioService $minioService,
        protected ConfigService $configService
    ) {

    }

    public function index()
    {
        $this->id = 1;

        $this->resetSessionSearch('index_config');
        $this->authorize('index_config');

        $breadcrumb = [
            [
                'title' => 'Trang chủ',
                'route' => 'home'
            ],
            [
                'title' => 'Cài đặt hệ thống'
            ]
        ];

        $config = Config::find($this->id);
        if ($config) {
            $data = [];
            $logo_link = "";
            if (!empty($config->content))
                $data = json_decode($config->content, true);

            if (!empty($data["logo"])) {
                $logo_link = $this->minioService->getCloudImage('logo', $data["logo"]);
            }

            if (!empty($data["logo_system"])) {
                $data["logo_system"] = $this->minioService->getCloudImage('logo_system', $data["logo_system"]);
                session(['logo_system' => $data["logo_system"]]);
            }

            if (!empty($data["banner_login"])) {
                $data["banner_login"] = $this->minioService->getCloudImage('banner_login', $data["banner_login"]);
            }

            if (!empty($data["logo_login"])) {
                $data["logo_login"] = $this->minioService->getCloudImage('logo_login', $data["logo_login"]);
            }

            $banner = json_decode($config->banners, true);
            $images = [];
            $banners = [];
            if (!empty($config->banners)) {
                $banner = json_decode($config->banners, true);
                if (!empty($banner)) {
                    foreach ($banner as $key => $value) {
                        $images[$key] = $value;
                        $banners[$key] = $this->minioService->getCloudImage('banner', $value);
                    }
                    # code...
                }
            }

            if (!empty($config->banners)) {
                $banner = json_decode($config->banners, true);
                if (!empty($banner)) {
                    foreach ($banner as $key => $value) {
                        $images[$key] = $value;
                        $banners[$key] = $this->minioService->getCloudImage('banner', $value);
                    }
                    # code...
                }
            }
        }

        // $sale_devices = SaleDevice::all();

        return view('admin.config.index', [
            'id' => $this->id,
            'data' => $data ?? [],
            'logo_link' => $logo_link ?? '',
            'breadcrumb' => $breadcrumb,
            'banners' => $banners ?? [],
            'images' => $images ?? ''
        ]);
    }

    public function update(Request $request)
    {
        $status = true;
        $message = '';

        DB::beginTransaction();
        try {
            if ($request->hasFile('logo') && $status) {
                $image_link = $this->uploadImage($request->logo, "logo");
                if (!empty($image_link['status'])) {
                    $status = false;
                    $message = $image_link['message'];
                }
            }

            if ($request->hasFile('logo_system') && $status) {
                $logo_system = $this->uploadImage($request->logo_system, "logo_system");
                if (!empty($logo_system['status'])) {
                    $status = false;
                    $message = $logo_system['message'];
                }
            }

            if ($request->hasFile('banner_login') && $status) {
                $banner_login = $this->uploadImage($request->banner_login, "banner_login");
                if (!empty($banner_login['status'])) {
                    $status = false;
                    $message = $banner_login['message'];
                }
            }

            if ($request->hasFile('logo_login') && $status) {
                $logo_login = $this->uploadImage($request->logo_login, "logo_login");
                if (!empty($logo_login['status'])) {
                    $status = false;
                    $message = $logo_login['message'];
                }
            }

            if ($status) {
                $list_save_item = [
                    "title_web",
                    "home_content",
                    "facebook",
                    "instagram",
                    "tiktok",
                    "zalo",
                    "hotline",
                    "email",
                    "address",
                    "payment_policy",
                    "cancellation_policy",
                    "refund_policy",
                    "expiry_date",
                    "auto_send_electronic_tickets",
                    "operating_time",
                    "title_login",
                    "title_footer",
                    "description_login"
                ];

                $config = Config::find(1);

                $old_content = [];

                if (!empty($config)) {
                    if ($config->content != '') {
                        $old_content = json_decode($config->content, true);
                    }
                }


                foreach ($list_save_item as $value) {
                    $content[$value] = $request[$value] ?? ($old_content[$value] ?? null);
                }
                // dd($old_content);
                $content['logo'] = !empty($image_link) ? $image_link : ($old_content['logo'] ?? null);

                $content['logo_system'] = !empty($logo_system) ? $logo_system : ($old_content['logo_system'] ?? null);

                $content['banner_login'] = !empty($banner_login) ? $banner_login : ($old_content['banner_login'] ?? null);

                $content['logo_login'] = !empty($logo_login) ? $logo_login : ($old_content['logo_login'] ?? null);

                $content_js = json_encode($content);
                if ($config) {
                    $config->update(['content' => $content_js]);
                } else {
                    Config::create([
                        "id" => 1,
                        'content' => $content_js,
                        'is_delete' => 0
                    ]);

                }

                DB::commit();

                Cache::forget(config('name_cache.config_web'));
                Cache::forever(config('name_cache.config_web'), $content);

                //xoá ảnh cũ
                if (!empty($image_link))
                    $this->minioService->deleteImageCloud("logo", $request->logo_old);

                return redirect()->back()->with('alert-success', 'Cập nhật thành công!');
            } else {
                return redirect()->back()->with('alert-error', $message);
            }
        } catch (\Throwable $th) {
            DB::rollBack();
            return redirect()->back()->with('alert-error', 'Có lỗi trong quá trình cập nhật!');
        }
    }

    private function uploadImage($image, $folder)
    {
        if (!in_array($image->getClientMimeType(), ["image/jpeg", "image/icon", "image/png", "image/jpg"])) {
            return [
                'status' => 90,
                'message' => "Phải chọn file ảnh đuôi : jpeg , png , jpg,icon !"
            ];
        }

        $image_link = $this->minioService->pushImageCloud($image, $folder);
        return $image_link;
    }

    public function updateInvoice(UpdateInvoiceRequest $request)
    {

        DB::beginTransaction();
        try {
            // Lấy cấu hình hiện tại
            $config = Config::find(1);
            $oldContent = $config && !empty($config->content) ? json_decode($config->content, true) : [];

            // Lấy dữ liệu từ form
            $data = $request->input('content', []);

            // Kết hợp dữ liệu cũ với dữ liệu mới
            $newContent = $oldContent;

            // Cập nhật `invoiceConfiguration`
            $newContent['invoiceConfiguration']['provider'] = $data['invoiceConfiguration']['provider'] ?? $oldContent['invoiceConfiguration']['provider'] ?? '';
            $newContent['invoiceConfiguration']['bill'] = array_merge(
                $oldContent['invoiceConfiguration']['bill'] ?? [],
                $data['invoiceConfiguration']['bill'] ?? []
            );
            $newContent['invoiceConfiguration']['ticket'] = array_merge(
                $oldContent['invoiceConfiguration']['ticket'] ?? [],
                $data['invoiceConfiguration']['ticket'] ?? []
            );


            $newContent['taxCode'] = $request->taxCode;
            $newContent['companyName'] = $request->companyName;
            $newContent['use_receipt'] = $request->use_receipt ?? 0;


            if ($config) {
                $config->update([
                    'content' => json_encode($newContent),
                ]);

            } else {
                Config::create([
                    'id' => 1,
                    'content' => json_encode($newContent),
                    'is_delete' => 0,
                ]);
            }

            DB::commit();

            // Xóa cache sau khi cập nhật thành công
            Cache::forget(config('name_cache.config_web'));
            Cache::forever(config('name_cache.config_web'), $newContent);

            return redirect()->back()->with('alert-success', 'Cập nhật thành công!');
        } catch (\Throwable $th) {
            DB::rollBack();
            Log::error($th->getMessage());
            return redirect()->back()->with('alert-error', 'Có lỗi trong quá trình cập nhật!');
        }
    }


    public function processBanner($id, Request $request): RedirectResponse
    {
        $config = Config::query()->find($id);
        if ($config) {
            $config->update(['banners' => $this->minioService->pushBanner($request) ?? '']);
            return redirect()->back()->with('alert-success', 'Cập nhật thành công!');
        }
        return redirect()->back()->with('alert-error', 'Có lỗi trong quá trình cập nhật!');
    }
}

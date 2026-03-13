<?php

namespace App\Services\Invoice;


use Carbon\Carbon;
use GuzzleHttp\Exception\RequestException;
use Illuminate\Support\Facades\Auth;
use Illuminate\Support\Facades\Cache;
use Illuminate\Support\Facades\Log;
use App\Services\LogService;


class ApiService
{

    public function __construct(
        protected LogService $logService,
    ) {

    }

    private function login($user_login)
    {
        $name_cacht_token = config('name_cache.login_invoice');

        if (!Cache::has($name_cacht_token)) {

            $have_error = false;
            try {

                $url = config("invoice.api_login.url");
                $form_params = $user_login;


                $client = new \GuzzleHttp\Client([
                    'headers' => [
                        'Content-Type' => 'application/json'
                    ]
                ]);

                $res = $client->request('POST', trim($url), [
                    'json' => $form_params,
                ]);


                $statusCode = $res->getStatusCode();

                $result = json_decode($res->getBody(), true, 512, JSON_THROW_ON_ERROR);

                if ($statusCode === 200) {

                    if (!empty($result["token"])) {

                        $token = $result["token"];
                        //ghi cache nêu thời gian sống token quá 1 ngày
                        if (isset($result["expireAfter"]) and (int) $result["expireAfter"] > 0) {
                            Cache::put($name_cacht_token, $token, now()->addMinutes((int) $result["expireAfter"]));
                        }

                        $response = [
                            'status' => 200,
                            'message' => "",
                            'token' => $token
                        ];

                    } else {

                        $have_error = true;
                        $response = [
                            'status' => 90,
                            'message' => "api login lấy token  bị lỗi !"
                        ];

                    }

                } else {

                    $have_error = true;
                    $response = [
                        'status' => 90, //ko ghi  vào db bảng bill
                        'message' => "Mã lỗi trả về là statusCode=" . $statusCode,
                    ];

                }

            } catch (RequestException $e) {

                $have_error = true;
                if ($e->hasResponse()) {
                    $response = $e->getResponse();
                    $statusCode = $response->getStatusCode();

                    $response = [
                        'status' => $statusCode, //ko ghi  vào db bảng bill
                        'message' => "Mã lỗi trả về là statusCode=" . $statusCode,
                    ];

                } else {

                    $response = [
                        'status' => 90, //ko ghi  vào db bảng bill
                        'message' => "Mã lỗi trả về là lỗi =" . $e->getMessage(),
                    ];
                }

            } catch (\Throwable $e) {
                $have_error = true;
                $response = [
                    'status' => 90, //ko ghi  vào db bảng bill
                    'message' => "Mã lỗi trả về là lỗi =" . $e->getMessage(),
                ];
            }


        } else {

            $response = [
                'status' => 200,
                'message' => "",
                'token' => Cache::get($name_cacht_token)
            ];

        }
        return $response;

    }

    /*
     *
     */
    public function CreatObjectInvoice($order_id, $paymentItems, $creator, $buyer, $additionalData = [], $invoiceConfiguration_type = 0, $provider = "MISA", $company = null, $invoiceConfiguration = null, $taxRate = null, $createdAt = null)
    {

        $send = [
            "Id" => $order_id,

            "Company" => $company,

            "Creator" => [
                "id" => $creator["id"],
                "Name" => $creator["Name"]
            ],

            "paymentItems" => $paymentItems,
            "additionalData" => $additionalData,
            "invoiceConfiguration" => $invoiceConfiguration,
            "Buyer" => $buyer,
            "taxRate" => $taxRate,
            "createdAt" => $createdAt ?? Carbon::now(),
            "MappingId" => ""
        ];
        return $send;

    }

    public function create_invoice($user_login, $send = [], $provider = "MISA", $i = 0, $is_pending = false)
    {
        //chưa bắt hết định dạng send vì quá nhiều...
        if (empty($send)) {
            return [
                'status' => 90, //ko ghi  vào db bảng bill
                'message' => 'tham số đầu vào : chưa đúng format !',
                'result' => '',
                'response_data' => ''
            ];
        }

        $check_login = $this->login($user_login);
        if ($check_login["status"] == 200) {
            $token_invoice = $check_login['token'];
        } else {

            return [
                'status' => 90,
                'message' => $check_login["message"]
            ];

        }

        $have_fail = false;
        $i++;

        //chưa bắt hết định dạng send vì quá nhiều...
        if ($is_pending) {
            $url = config('invoice.url_create_invoice_pending');
        } else {
            $url = config('invoice.url_create_invoice');
        }
        $url = str_replace("[provider]", $provider, $url);
        // dd($url);
        $this->logService->debug(["Dữ liệu gửi lên", $url, json_encode($send, JSON_UNESCAPED_UNICODE)], __FUNCTION__);

        try {

            $client = new \GuzzleHttp\Client([
                'headers' => [
                    'Authorization' => "Bearer " . $token_invoice,
                    'Content-Type' => 'application/json'
                ]
            ]);

            $res = $client->request('POST', trim($url), [
                'json' => $send
            ]);

            $statusCode = $res->getStatusCode();

            if ($statusCode === 200) {

                $result = json_decode($res->getBody(), true);
                //chuyển json
                $response_data = json_encode($result, JSON_UNESCAPED_UNICODE);

                $this->logService->debug(["Kết quả trả về :tạo hddt thành công", $result], __FUNCTION__);

                $response = [
                    'status' => $statusCode, //ghi vào db bảng bill
                    'message' => '',
                    'result' => $result,
                    'response_data' => $response_data
                ];

            } else {

                $this->logService->debug(["Kết quả trả về : tạo hddt thất bại", $statusCode], __FUNCTION__);

                $have_fail = true;

                $response = [
                    'status' => 90, //ko ghi  vào db bảng bill
                    'message' => "Mã lỗi bên hoá đơn điện tử trả về là statusCode=" . $statusCode,
                    'result' => '',
                    'response_data' => '',
                    'response_status' => $statusCode
                ];

            }


        } catch (RequestException $e) {

            $have_fail = true;

            // Xử lý lỗi : vì Dương trả về status=400 ,.. nên sẽ ko bắt theo nôi dung trả về "detailCode":"400" , mà bắt tại đây
            if ($e->hasResponse()) {

                $result = $e->getResponse();
                $statusCode = $result->getStatusCode(); // Lấy mã trạng thái HTTP
                // dd($statusCode);
                $body = $result->getBody()->getContents(); // Lấy nội dung phản hồi
                $error = json_encode($body, JSON_UNESCAPED_UNICODE);

                $this->logService->debug(["Kết quả trả về : tạo hddt thất bại", $error], __FUNCTION__);

                $response = [
                    'status' => 90,
                    'message' => $body, //ghi tất cả lỗi và vào db bảng weighing_bill.response_data
                    'result' => '',
                    'response_data' => '',
                    'response_status' => $statusCode,

                ];

            } else {

                $this->logService->debug(["Kết quả trả về : tạo hddt thất bại", $e->getMessage()], __FUNCTION__);

                // Xử lý khi không có phản hồi
                $response = [
                    'status' => 90, //ko ghi  vào db bảng bill
                    'message' => $e->getMessage(),
                    'result' => '',
                    'response_data' => ''
                ];
            }


        } catch (\Throwable $e) {

            $have_fail = true;

            $error = $e->getMessage();

            $this->logService->debug(["Kết quả trả về : tạo hddt thất bại", $error], __FUNCTION__);

            $response = [
                'status' => 90,
                'message' => $error,
                'result' => '',
                'response_data' => '',
                'response_status' => 401,
            ];

        }

        if (isset($response["response_status"]) and (int) $response["response_status"] == 401) {
            Cache::forget(config('name_cache.login_invoice'));
            if ($i == 1) {
                $this->logService->debug(["lấy mới token login"], __FUNCTION__);
                $this->create_invoice($user_login, $send, $provider, $i);
            }
        }

        return $response;
    }


    /**
     * kiểm tra trạng thái vé/hd điện tử
     * @param $user_login
     * @param $id : là ticket_id nếu là vé, là order_id nếu là hoá đơn
     * @param $provider
     * @param $i
     * @return array
     */
    public function check_status_order($user_login, $id = "", $provider = "MISA", $i = 0)
    {
        $check_login = $this->login($user_login);
        if ($check_login["status"] == 90) {
            return [
                'status' => 90,
                'message' => $check_login["message"]
            ];

        } else {
            $token_invoice = $check_login['token'];
        }
        $i++;


        $url = config('invoice.url_status_invoice');
        $url = str_replace("[provider]", $provider, $url);
        $url = str_replace("[orderId]", $id, $url);

        $this->logService->debug(["url gọi đi", $url], __FUNCTION__);

        try {

            $client = new \GuzzleHttp\Client([
                'headers' => [
                    'Authorization' => "Bearer " . $token_invoice,
                    //'Content-Type' => 'application/json'
                ]
            ]);


            $res = $client->request('GET', trim($url), [
                // 'json' => $send, //$send là mảng nhé ,ko phải array
            ]);



            /////////////////--------------
            $statusCode = $res->getStatusCode();




            $this->logService->debug(["trạng thái mã :" . $statusCode], __FUNCTION__);


            if ($statusCode === 200) {

                $result = json_decode($res->getBody(), true);


                $response_data = json_encode($result, JSON_UNESCAPED_UNICODE);

                $this->logService->debug(["Kết quả trả về : đơn hàng điện tử đã dc tạo trước đó thành công rồi", $result], __FUNCTION__);

                $response = [
                    'status' => 200, //ghi vào db bảng bill
                    'message' => '',
                    'result' => $result,
                    'response_data' => $response_data
                ];


            } else {

                $this->logService->debug(["Kết quả trả về : kiểm tra trạng thái đơn hàng điện tử bị lỗi =", $statusCode], __FUNCTION__);

                $response = [
                    'status' => 90, //ko ghi  vào db bảng bill
                    'message' => "kiểm tra trạng thái đơn hàng điện tử bị lỗi=" . $statusCode,
                    'result' => '',
                    'response_data' => '',
                    'response_status' => $statusCode,
                ];

            }
            /////////////////------------------


        } catch (RequestException $e) {

            // Xử lý lỗi , không tìm thấy  http status=400
            if ($e->hasResponse()) {

                $result = $e->getResponse();
                $statusCode = $result->getStatusCode(); // Lấy mã trạng thái HTTP
                $body = $result->getBody()->getContents(); // Lấy nội dung phản hồi
                $error = json_encode($body, JSON_UNESCAPED_UNICODE);

                $this->logService->debug(["Kết quả trả về : kiểm tra trạng thái đơn hàng điện tử thất bại , trạng thái lỗi:" . $statusCode . " ,lỗi:", $error], __FUNCTION__);

                $response = [
                    'status' => 90,
                    'message' => $body, //ghi tất cả lỗi và vào db bảng weighing_bill.response_data
                    'result' => '',
                    'response_data' => '',
                    'response_status' => $statusCode,
                ];

            } else {

                $this->logService->debug(["Kết quả trả về : tạo đơn hàng điện tử thất bại", $e->getMessage()], __FUNCTION__);

                // Xử lý khi không có phản hồi
                $response = [
                    'status' => 90, //ko ghi  vào db bảng bill
                    'message' => $e->getMessage(),
                    'result' => '',
                    'response_data' => ''
                ];
            }


        } catch (\Throwable $e) {


            $error = $e->getMessage();

            $this->logService->debug(["bắt ở Throwable, lỗi:", $error], __FUNCTION__);

            $response = [
                'status' => 90, //ko ghi  vào db bảng bill
                'message' => $error,
                'result' => '',
                'response_data' => '',
                'response_status' => 401
            ];

        }

        //token login hết han => login lại
        if (isset($response["response_status"]) and (int) $response["response_status"] == 401) {
            Cache::forget(config('name_cache.login_invoice'));
            if ($i == 1) {
                $this->logService->debug(["lấy mới token login"], __FUNCTION__);
                $this->check_status_order($user_login, $orderId, $provider, $i);
            }
        }

        return $response;

    }


}

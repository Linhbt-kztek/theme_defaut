<?php

namespace App\Services\Payment;

use GuzzleHttp\Exception\RequestException;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\Cache;
use Illuminate\Support\Facades\DB;
use Illuminate\Support\Facades\Log;
use App\Services\LogService;
use Illuminate\Support\Str;

class MomoService
{

    public function __construct(
        protected LogService $logService,
    ) {

    }

    private function loginPayment()
    {
        $name_cacht_token = config('name_cache.login_payment_online');

        if (!Cache::has($name_cacht_token)) {

            $status = true;
            $token = '';
            $message = '';

            $url = config("api_payment_online.api_payment_login.url");
            $form_params = config("api_payment_online.api_payment_login.param");

            $client = new \GuzzleHttp\Client([
                'headers' => [
                    'Content-Type' => 'application/json'
                ]
            ]);

            $res = $client->request('POST', trim($url), [
                'json' => $form_params,
            ]);

            $result = json_decode($res->getBody(), true, 512, JSON_THROW_ON_ERROR);

            if (!empty($result["token"])) {
                $token = $result["token"];

                if (isset($result["expireAfter"]) and (int) $result["expireAfter"] > 0) {
                    Cache::put($name_cacht_token, $token, now()->addMinutes((int) $result["expireAfter"]));
                }

            } else {
                $error = "api login lấy token của momo bị lỗi !";
                Log::error($error);
                $status = false;
                $message = $error;


            }
            return [
                'status' => $status,
                'message' => $message,
                'token' => $token
            ];
        } else {

            return $data_return = [
                'status' => 200,
                'message' => "",
                'token' => Cache::get($name_cacht_token)
            ];
        }

    }


    public function createPaymentOrder($params_data, $allParams = true, array $params_oject, $i = 0, $paymentMethod)
    {
        $loginPayment = $this->loginPayment();
        if (!$loginPayment['status']) {
            $data_return = [
                'status' => 90,
                'message' => $loginPayment['message']
            ];

            return $data_return;
        } else {
            $token = $loginPayment['token'];
        }
        $i++;

        //==================API 2 : TẠO DƠN HÀNG Ở MOMO NHƯNG CHƯA THANH TOÁN =================================
        $url = str_replace("[url_replace]", url(''), config("api_payment_online.api_payment_momo.url") . '?key=' . (isset($params_data["key"]) ? $params_data["key"] : 'defaut'));
        $url = str_replace("[provider]", $paymentMethod, $url);

        //tên momo chỉ cho 30 ký tự
        $params_data["paymentObjectDetails"][0]["name"] = Str::limit($params_data["paymentObjectDetails"][0]["name"], 27);

        $form_params = [
            "id" => getGUID(),  //tự sinh , để phân biệt giữa các làn gửi sang Dương thôi
            "OrderId" => $params_data["OrderId"],  //order.id
            "code" => $params_data["code"],  //order.id
            "paymentObjectName" => config("api_payment_online.api_payment_momo.param.paymentObjectName"),
            "paymentObjectDetails" => $params_data["paymentObjectDetails"],
            "description" => config("api_payment_online.api_payment_momo.param.description"),
            "companyName" => $params_oject['companyName'],
            "companyCode" => config("api_payment_online.api_payment_momo.param.companyCode"),
            "user" => $params_oject['user_payment'],
            "categoryId" => $params_oject['categoryId'],
            "amount" => $params_data["amount"],
            "additionalData" => $params_data["additionalData"],
            "bankCode" => config("api_payment_online.api_payment_momo.param.bankCode"),
        ];
        
        try {
            $client = new \GuzzleHttp\Client([
                'headers' => [
                    'Authorization' => "Bearer " . $token,
                    'Content-Type' => 'application/json'
                ]
            ]);

            $res = $client->request('POST', trim($url), [
                'json' => $form_params,
            ]);

            $result = json_decode($res->getBody(), true, 512, JSON_THROW_ON_ERROR);
            $statusCode = $res->getStatusCode();

            if ($statusCode === 200) {
                $this->logService->debug(["Kết quả trả về :tạo mã QR thành công"], __FILE__ . __FUNCTION__ . __LINE__, "", "payment");

                if ($allParams) {
                    $data_return = [
                        'status' => 200,
                        'message' => 'Tạo vé thành công!',
                        'result_api' => $result["relatedData"]
                    ];
                } else {
                    //chỉ trả về payUrl
                    $data_return = [
                        'status' => 200,
                        'message' => 'Tạo vé thành công!',
                        'result_api' => $result["relatedData"]['payUrl']
                    ];
                }

            } else {
                $this->logService->debug(["Kết quả trả về : tạo mã QR thất bại", $statusCode], __FILE__ . __FUNCTION__ . __LINE__, "", "payment");

                $data_return = [
                    'status' => 90, //ko ghi  vào db bảng bill
                    'message' => "Mã lỗi bên tạo mã QR trả về là statusCode=" . $statusCode,
                    'result' => '',
                    'response_data' => '',
                    'response_status' => $statusCode
                ];

            }

        } catch (RequestException $e) {
            // Xử lý lỗi : vì Dương trả về status=400 ,.. nên sẽ ko bắt theo nôi dung trả về "detailCode":"400" , mà bắt tại đây
            if ($e->hasResponse()) {

                $response = $e->getResponse();
                $statusCode = $response->getStatusCode(); // Lấy mã trạng thái HTTP

                $body = $response->getBody()->getContents(); // Lấy nội dung phản hồi
                $error = json_encode($body, JSON_UNESCAPED_UNICODE);

                $this->logService->debug(["Kết quả trả về : tạo mã QR thất bại", $error], __FILE__ . __FUNCTION__ . __LINE__, "", "payment");

                $data_return = [
                    'status' => 90,
                    'message' => $body, //ghi tất cả lỗi và vào db bảng weighing_bill.response_data
                    'result' => '',
                    'response_data' => '',
                    'response_status' => $statusCode,

                ];

            } else {

                $this->logService->debug(["Kết quả trả về : tạo mã QR thất bại", $e->getMessage()], __FILE__ . __FUNCTION__ . __LINE__, "", "payment");

                // Xử lý khi không có phản hồi
                $data_return = [
                    'status' => 90, //ko ghi  vào db bảng bill
                    'message' => $e->getMessage(),
                    'result' => '',
                    'response_data' => ''
                ];
            }

        } catch (\Throwable $e) {

            $error = $e->getMessage();

            $this->logService->debug(["Kết quả trả về : tạo mã QR thất bại", $error], __FILE__ . __FUNCTION__ . __LINE__, "", "payment");

            $data_return = [
                'status' => 90,
                'message' => $error,
                'result' => '',
                'response_data' => '',
                'response_status' => 401,
            ];

        }
        //==================API 2 : TẠO DƠN HÀNG Ở MOMO NHƯNG CHƯA THANH TOÁN =================================

        if (isset($data_return["response_status"]) and (int) $data_return["response_status"] == 401) {
            Cache::forget(config('name_cache.login_payment_online'));
            if ($i == 1) {
                $this->logService->debug(["lấy mới token login"], __FILE__ . __FUNCTION__ . __LINE__, "", "payment");
                $this->createPaymentOrder($params_data, $allParams, $params_oject, $i, $paymentMethod);
            }
        }
        return $data_return;

    }

    //hàm này là url redirect từ momo về
    public function payment_result($orderId, $i = 0)
    {
        $loginPayment = $this->loginPayment();
        if (!$loginPayment['status']) {
            $data_return = [
                'status' => 90,
                'message' => $loginPayment['message']
            ];

            return $data_return;
        } else {
            $token = $loginPayment['token'];
        }

        $i++;

        try {
            //goi api check trang thai
            $url = str_replace("[orderId]", $orderId, config("api_payment_online.api_payment_momo_status.url"));
            $client = new \GuzzleHttp\Client([
                'headers' => [
                    'Authorization' => "Bearer " . $token,
                    'Content-Type' => 'application/json'
                ]
            ]);

            $res = $client->request('GET', $url);
            $result = json_decode($res->getBody(), true, 512, JSON_THROW_ON_ERROR);

            $statusCode = $res->getStatusCode();

            $data_return = [
                'message' => $result['message'],
                'data' => $result
            ];

            if ($statusCode === 200) {
                $data_return['status'] = 200;
            } else {
                $data_return['status'] = 90;
                $data_return['response_status'] = $statusCode;
            }
        } catch (RequestException $e) {

            if ($e->hasResponse()) {

                $response = $e->getResponse();
                $statusCode = $response->getStatusCode(); // Lấy mã trạng thái HTTP
                // dd($statusCode);
                $body = $response->getBody()->getContents(); // Lấy nội dung phản hồi
                $error = json_encode($body, JSON_UNESCAPED_UNICODE);

                $this->logService->debug(["Kết quả trả về : tạo mã QR thất bại", $error], __FILE__ . __FUNCTION__ . __LINE__, "", "payment");

                $data_return = [
                    'status' => 90,
                    'message' => $body, //ghi tất cả lỗi và vào db bảng weighing_bill.response_data
                    'data' => [],
                    'response_status' => $statusCode,
                ];

            } else {

                $this->logService->debug(["Kết quả trả về : tạo mã QR thất bại", $e->getMessage()], __FILE__ . __FUNCTION__ . __LINE__, "", "payment");

                // Xử lý khi không có phản hồi
                $data_return = [
                    'status' => 90, //ko ghi  vào db bảng bill
                    'message' => $e->getMessage(),
                    'data' => []
                ];
            }

        } catch (\Throwable $e) {

            $error = $e->getMessage();

            $this->logService->debug(["Kết quả trả về : tạo mã QR thất bại", $error], __FILE__ . __FUNCTION__ . __LINE__, "", "payment");

            $data_return = [
                'status' => 90,
                'message' => $error,
                'data' => [],
                'response_status' => 401,
            ];
        }


        if (isset($data_return["response_status"]) and (int) $data_return["response_status"] == 401) {
            Cache::forget(config('name_cache.login_payment_online'));
            if ($i == 1) {
                $this->logService->debug(["lấy mới token login"], __FILE__ . __FUNCTION__ . __LINE__, "", "payment");
                $this->payment_result($code_order, $i);
            }
        }

        return $data_return;

    }

    public function payment_search($orderId, $i = 0)
    {

        $loginPayment = $this->loginPayment();

        if (!$loginPayment['status']) {
            $data_return = [
                'status' => 90,
                'error' => $loginPayment['message']
            ];

            return $data_return;
        } else {
            $token = $loginPayment['token'];
        }

        $i++;

        try {

            $this->logService->debug(["tham só truyền vào code_order=", $orderId], __FILE__ . __FUNCTION__ . __LINE__, "", "payment");

            $url = str_replace("[orderId]", $orderId, config("api_payment_online.api_payment_momo_status.url"));
            $client = new \GuzzleHttp\Client([
                'headers' => [
                    'Content-Type' => 'application/json',
                    'Authorization' => "Bearer " . $token,
                ]
            ]);

            $res = $client->request('GET', $url);
            $result = json_decode($res->getBody(), true);

            $this->logService->debug(["Kết quả trả về=", $result], __FILE__ . __FUNCTION__ . __LINE__, "", "payment");

            $statusCode = $res->getStatusCode();


            if ($statusCode === 200) {

                //nếu đơn hàng thành công
                if ((int) $result["relatedData"]["status"] == 1) {
                    return [
                        'status' => 200,
                        'error' => 'Kết quả thành toán Momo : đơn hàng đã thanh toán thành công !'
                    ];
                } else {

                    //status: 0 đang xử lý , 2 đã huỷ , 3 thất bại
                    $error = "";
                    if ((int) $result["relatedData"]["status"] == 0)
                        $error = "Kết quả thành toán Momo : đơn hàng đang xử lý,";
                    else if ((int) $result["relatedData"]["status"] == 2)
                        $error = "Kết quả thành toán Momo : đơn hàng đã huỷ.";
                    else if ((int) $result["relatedData"]["status"] == 3)
                        $error = "Kết quả thành toán Momo : đơn hàng thất bại.";
                    else
                        $error = "Lỗi : API chưa định nghĩa relatedData.status";

                    return [
                        'status' => 201,
                        'error' => $error
                    ];
                }

            } else {

                $this->logService->debug(["Kết quả trả về : tạo mã QR thất bại mã trạng thái=", $statusCode], __FILE__ . __FUNCTION__ . __LINE__, "", "payment");
                return [
                    'status' => 90,
                    'error' => 'Lỗi server !!! ',
                    'response_status' => $statusCode
                ];
            }
        } catch (RequestException $e) {

            if ($e->hasResponse()) {

                $response = $e->getResponse();
                $statusCode = $response->getStatusCode(); // Lấy mã trạng thái HTTP
                // dd($statusCode);
                $body = $response->getBody()->getContents(); // Lấy nội dung phản hồi
                $error = json_encode($body, JSON_UNESCAPED_UNICODE);

                $this->logService->debug(["Kết quả trả về : tạo mã QR thất bại", $error], __FILE__ . __FUNCTION__ . __LINE__, "", "payment");

                $data_return = [
                    'status' => 90,
                    'error' => $body, //ghi tất cả lỗi và vào db bảng weighing_bill.response_data
                    'response_status' => $statusCode,

                ];

            } else {

                $this->logService->debug(["Kết quả trả về : tạo mã QR thất bại", $e->getMessage()], __FILE__ . __FUNCTION__ . __LINE__, "", "payment");

                // Xử lý khi không có phản hồi
                $data_return = [
                    'status' => 90, //ko ghi  vào db bảng bill
                    'error' => $e->getMessage(),
                ];
            }

        } catch (\Throwable $e) {

            $error = $e->getMessage();

            $this->logService->debug(["Kết quả trả về : tạo mã QR thất bại", $error], __FILE__ . __FUNCTION__ . __LINE__, "", "payment");

            $data_return = [
                'status' => 90,
                'error' => $error,
                'response_status' => 401,
            ];

        }

        if (isset($data_return["response_status"]) and (int) $data_return["response_status"] == 401) {
            Cache::forget(config('name_cache.login_payment_online'));
            if ($i == 1) {
                $this->logService->debug(["lấy mới token login"], __FILE__ . __FUNCTION__ . __LINE__, "", "payment");
                $this->payment_search($orderId, $i);
            }
        }

        return $data_return;
    }
}

<?php

namespace App\Services;

use Carbon\Carbon;
use Illuminate\Support\Facades\DB;
use Illuminate\Support\Facades\Log;
use Illuminate\Support\Facades\Validator;




class ReturnResultService
{

    public function __construct(
    ) {
    }

    public function return_error($error = "")
    {
        Log::channel('invoice')->info($error);

        $response = [
            "status" => 90,
            "run" => 1,
            "error" => $error,
            "result" => [],
        ];

        return response()->json($response , 200, [], JSON_UNESCAPED_UNICODE);
    }


    /**
     * hàm trả lỗi giữa các hàm trung gian
     * @param $error
     * @param $status
     * @param $data_error
     * @param $type :1 hoac 2, dùng cho t.hop : trả về lỗi của hddt và vdt
     *          1 : neu hddt(1 hd) va 1 ve : format hien thi loi khac,
     *          2: neu la hddt(nhieu ve) thi format hien thi loi khac, là 1 table nhieu hang
     * @return array
     */
    public function return_error_midle($error = "",$status=90,$data_error=[],$type="")
    {

        $response = [
            'status' => $status,
            'message' => $error,
            'data' => $data_error,
            'type' => $type,
        ];

        return $response;
    }

    public function return_success_midle($result = [],$message ="")
    {
        $response = [
            "status" => 200,
            "message" => $message,
            'data' => $result
        ];
        return $response;

    }

    public function return_success($result)
    {
        $response = [
            "status" => 200,
            "run" => 1,
            "error" => "",
            "result" => $result,
        ];
        return response()->json($response , 200, [], JSON_UNESCAPED_UNICODE);

    }

}

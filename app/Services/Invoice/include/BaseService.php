<?php

namespace App\Services\Invoice\include;

class BaseService
{
    /**
     * trường hợp lỗi :  for mat hiên thi lỗi trả về cho client
     * @param $response
     * @param $entity
     * @param $type
     * @return mixed
     */
    public function processResponseError($response, $entity, $type)
    {
        if (!empty($response['message'])) {
            $messageData = json_decode($response['message'], true);

            // danh cho format loi cua templateCode
            if (isset($messageData['isSuccess'])) {

                $isSuccess = $messageData['isSuccess'] ?? false;
                $fields = $messageData['fields'] ?? [];

                $data = [];
                if ($isSuccess === false && !empty($fields)) {
                    // Gộp tất cả errorCode từ mảng fields thành một chuỗi
                    $errorCodes = array_map(function ($field) {
                        return $field['errorCode'] ?? 'N/A'; // Lấy errorCode hoặc 'N/A' nếu không có
                    }, $fields);
                    $combinedErrorCodes = implode(', ', $errorCodes);

                    // Chỉ thêm một phần tử duy nhất vào mảng data
                    $data[] = [
                        "{$type}_id" => $entity->id,
                        "{$type}_code" => $type === 'order' ? $entity->code_order : $entity->code,
                        'status' => $type === 'order' ? 'Hoá đơn tạo thất bại' : 'Vé tạo thất bại',
                        'error_code' => $combinedErrorCodes // Chuỗi chứa tất cả errorCode
                    ];
                }

                // Kiểm tra trường hợp khác với format thứ hai
            } elseif (isset($messageData['errors'])) {
            // danh cho format loi cua ten cong ty, maso thue cty ,,,

                $errors = $messageData['errors'];
                // Gộp tất cả các lỗi từ mọi trường thành một chuỗi, cách nhau dấu phẩy
                $allErrorMessages = [];
                foreach ($errors as $field => $errorMessages) {
                    $allErrorMessages = array_merge($allErrorMessages, $errorMessages);
                }
                $combinedErrorMessages = implode(', ', $allErrorMessages);

                // Chỉ thêm một phần tử duy nhất vào mảng data
                $data[] = [
                    "{$type}_id" => $entity->id,
                    "{$type}_code" => $type === 'order' ? $entity->code_order : $entity->code,
                    'status' => $type === 'order' ? 'Hoá đơn tạo thất bại' : 'Vé tạo thất bại',
                    'error_code' => $combinedErrorMessages // Chuỗi chứa tất cả lỗi
                ];

            } else {
                // Trường hợp không có thông tin lỗi cần xử lý
                $data = [];
            }

            // Cập nhật lại mảng data trong response
            $response['data'] = $data;
            // Cập nhật message
            $response['message'] = '';

        } else {
            $response['data'] = [];
            $response['message'] = '';
        }
        // Trả về kết quả
        return $response;
    }

}

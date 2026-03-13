<?php
//API thanh toan online
$url_payment_root = env('URL_PAYMENT_ROOT');

return [
    //api login
    "api_payment_login" => [
        "url" => $url_payment_root . "login",
        "param" => [
            "username" => env('USER_PAYMENT'),
            "password" => env('PASSWORD_PAYMENT')
        ]
    ],

    "list_provider" => env('PROVIDER_PAYMENT'),

    "api_payment_momo" => [
        "url" => $url_payment_root . "order/qr?provider=[provider]&staticQR=true&redirectUrl=" . '[url_replace]' . "/payment_result",
        "param" => [
            "paymentObjectName" => "Mua vé Online tại KVC Ô Quy Hồ",
            "paymentObjectDetails" => [
                [
                    "name" => "xxxxxx",  //thay đổi theo sp mua
                    "description" => "xxxxxx", //thay đổi theo sp mua
                    "category" => "xxxx", //thay đổi theo sp mua
                    "currency" => "VND",
                    "quantity" => 1,
                    "unit" => "chiếc",
                    "taxAmount" => 0
                ]
            ],
            "description" => "Mô tả đơn hàng",
            "companyName" => "Ô Quy Hồ",
            "companyCode" => "KZTEK., JSC",
            "categoryId" => "string",
            "additionalData" => "",
            "bankCode" => ""
        ]
    ],
    //api check trạng thái
    "api_payment_momo_status" => [
        "url" => $url_payment_root . "transaction?orderId=[orderId]",
    ]

];


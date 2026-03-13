<?php
return [

    'url_payment_root' => env('INVOICE_URL'),
    'username' => env('INVOICE_NAME'),
    'password' => env('INVOICE_PASSWORD'),

    //api login
    "api_login" => [
        "url" => env('INVOICE_URL') . "/login", // Hoặc 'url_payment_root' . '/login'
        "param" => [
            "username" => env('INVOICE_NAME'),
            "password" => env('INVOICE_PASSWORD')
        ]
    ],

    "paymentItems" => [
        "UnitName" => "Vé",
        "Quantity" => 1,
    ],

    // TẠO HOÁ ĐƠN ĐIỆN TỬ
    "url_create_invoice" => env('INVOICE_URL') . "/invoice?provider=[provider]",

    "url_create_invoice_pending" => env('INVOICE_URL') . "/pending?provider=[provider]&pendingReason=Deplayed&publishAfterMinutes=0",

    // KIỂM TRA TÌNH TRẠNG HDDT
    "url_status_invoice" => env('INVOICE_URL') . "/published/orderId/[orderId]?getfile=true&provider=[provider]&fileType=pdf",

    "ip_callback" => env('IP_CALLBACK')

];
// http://invoice.demo.kztek.io.vn/invoice?provider=MISA
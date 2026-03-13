<?php
return [
//    'only' => ['register_online.saveCart', 'register_online.previewOrder', 'register_online.getTicketTypeDetail'],
    'except' => ['admin.*'],
    'groups' => [
        'register_online' => ['register_online.saveCart', 'register_online.previewOrder', 'register_online.getTicketTypeDetail'],
    ],
];

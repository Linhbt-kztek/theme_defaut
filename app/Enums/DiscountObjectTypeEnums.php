<?php

namespace App\Enums;

class DiscountObjectTypeEnums
{
    const PROMOTIONAL = 1;
    const VOUCHER = 2;
    const CUSTOMER_CLASSIFICATION = 3;

    public static function getLabels()
    {
        return [
            self::PROMOTIONAL => 'promotional',
            self::VOUCHER => 'voucher',
            self::CUSTOMER_CLASSIFICATION => 'customer classfication',
        ];
    }
}
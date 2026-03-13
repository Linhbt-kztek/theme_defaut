<?php

namespace App\Enums;

class CardStatusEnums
{
    const INACTIVE = 0;
    const ACTIVE = 1;
    const LOCKED = 2;

    public static function getLabels()
    {
        return [
            self::INACTIVE => 'inactive',
            self::ACTIVE => 'active',
            self::LOCKED => 'locked',
        ];
    }
}
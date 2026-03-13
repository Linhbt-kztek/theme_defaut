<?php

namespace App\Http\Rules\Config;


use Illuminate\Contracts\Validation\Rule;

class CheckTimeEatConfigRules implements Rule
{

    public function __construct()
    {
    }
    public function passes($attribute, $value)
    {

        $timeSlots = $value;

        // Tạo mảng mới để theo dõi các khoảng thời gian đã xuất hiện
        $seenTimeSlots = [];

        $hasOverlap = false;

        foreach ($timeSlots as $timeSlot) {
            $startTime = strtotime($timeSlot["start_time"]);
            $endTime = strtotime($timeSlot["end_time"]);

            foreach ($seenTimeSlots as $seenTimeSlot) {
                $seenStartTime = strtotime($seenTimeSlot["start_time"]);
                $seenEndTime = strtotime($seenTimeSlot["end_time"]);

                if (($startTime >= $seenStartTime && $startTime <= $seenEndTime) ||
                    ($endTime >= $seenStartTime && $endTime <= $seenEndTime)
                ) {
                    $hasOverlap = true;
                    break 2;
                }
            }

            $seenTimeSlots[] = $timeSlot;
        }

        if ($hasOverlap) {
            $return = false;
        } else {
            $return = true;
        }
        
        return $return;
    }

    public function message()
    {
        return 'Thời gian ăn giữa các bữa không được trùng nhau!';
    }
}

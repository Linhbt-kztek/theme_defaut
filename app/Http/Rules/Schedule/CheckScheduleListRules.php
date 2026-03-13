<?php

namespace App\Http\Rules\Schedule;


use Illuminate\Contracts\Validation\Rule;

class CheckScheduleListRules implements Rule
{

    public function __construct()
    {
    }
    public function passes($attribute, $value)
    {
        $return = true;
        $start_time = 0;
        $end_time = 0;
        foreach ($value as $key => $val) {
            $start_time =  $val['start_time'];
            if ($start_time != 0 && $end_time != 0) {
                if ($start_time < $end_time) {
                    $return = false;
                }
            }
            $end_time = $val['end_time'];
            
        }
        return $return;
    }

    public function message()
    {
        return 'Thiết lập thời gian hiển thị trong ngày không hợp lý!';
    }
}

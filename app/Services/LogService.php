<?php

namespace App\Services;

use App\Repositories\Log\LogRepositoryInterface;
use Illuminate\Support\Facades\Auth;
use Illuminate\Support\Facades\DB;
use Illuminate\Support\Facades\Log;
use App\Repositories\WarningEvent\WarningEventRepositoryInterface;

class LogService
{
    protected object $logRepository;
    protected object $warningEventRepo;

    public function __construct(
        LogRepositoryInterface $logRepository,
        // WarningEventRepositoryInterface $warningEventRepo
    ) {
        $this->logRepository = $logRepository;
        // $this->warningEventRepo = $warningEventRepo;
    }

    public function saveLog(string $model, string $action, $model_id, array $before_action, array $after_action, $companyId = null, $module = null, $moreOptions = [], $description = null): bool
    {
        $data = [
            'id' => getGUID(),
            'user_id' => auth()->id(),
            'model' => $model,
            'model_id' => $model_id,
            'company_id' => $companyId,
            'action' => $action,
            'before_action' => json_encode($before_action),
            'after_action' => json_encode($after_action),
            'module' => $module,
            'description' => $description,
            'is_delete' => 0
        ];


        //        if (isset($moreOptions)) {
//            foreach ($moreOptions as $key => $moreOption) {
//                $data[$key] = $moreOption;
//            }
//        }

        $log = $this->logRepository->create($data);
        //        return (bool)$log;
        if ($log) {
            return true;
        }
        return false;
    }


    /*
     *  chức năng : ghi log cảnh bảo cho api quẹt thẻ Ở TRÊN
     *  đầu vào: data ứng với bảng tbl_warning_event
     *  trả về : true / false
     */
    // public function log_warning_event($data_param)
    // {

    //     $data = [
    //         "id" => getGUID(),
    //         "event_code" => $data_param["event_code"],
    //         "action" => $data_param["action"],
    //         "cabinet_rentals_id" => $data_param["cabinet_rentals_id"],
    //         "cabinet_history_id" => $data_param["cabinet_history_id"],
    //         "cabinet_payment_id" => $data_param["cabinet_payment_id"],
    //         "company_id" => $data_param["company_id"],
    //         "description" => $data_param["description"],
    //         "user_id" => $data_param["user_id"],
    //         "is_delete" => $data_param["is_delete"],
    //     ];


    //     $log_warning_event = $this->warningEventRepo->create($data);
    //     if ($log_warning_event) {
    //         return true;
    //     }
    //     return false;

    // }
    public function debug($content = [], $start = "Start", $end = "end")
    {
        if (config("app.view_log") == 1) {

            if ($end=="") $end = $start;

            Log::channel('invoice')->info("------------------ $start -----------------------");
            if (!empty($content)) {
                foreach ($content as $item) {
                    Log::channel('invoice')->info($item);
                }
            }
            Log::channel('invoice')->info("------------------ $end-----------------------");
            Log::channel('invoice')->info("");
        }

    }
    
}

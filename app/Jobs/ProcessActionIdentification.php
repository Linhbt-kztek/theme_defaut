<?php
namespace App\Jobs;

use App\Repositories\Device\DeviceRepositoryInterface;
use App\Repositories\Identification\IdentificationRepository;
use Illuminate\Bus\Queueable;
use Illuminate\Contracts\Queue\ShouldQueue;
use Illuminate\Queue\InteractsWithQueue;
use Illuminate\Queue\SerializesModels;
use App\Services\IdentificationService;
use Illuminate\Foundation\Bus\Dispatchable;
use Illuminate\Support\Facades\Log;

class ProcessActionIdentification implements ShouldQueue
{
    use Dispatchable, InteractsWithQueue, Queueable, SerializesModels;

    protected $identificationId;

    public function __construct(
        $identificationId
    ) {
        $this->identificationId = $identificationId;
    }

    public function handle()
    {
        // $identificationRepo = app()->make(IdentificationRepository::class);
        // $identificationService = app()->make(IdentificationService::class);
        // $identification = $identificationRepo->getById($this->identificationId);
        // try {
        //     if ($identification->status == 1 && $identification->type == 2) {
        //         $deviceRepo = app()->make(DeviceRepositoryInterface::class);
        //         $device = $deviceRepo->getDataAllOption([
        //             'filter' => [
        //                 ['type', '=', 1],
        //                 ['default_register', '=', 1]
        //             ]
        //         ])->first();
        //         if ($device == null) {
        //             $device = $deviceRepo->getDataAllOption([
        //                 'filter' => [
        //                     ['type', '=', 1]
        //                 ]
        //             ])->first();
        //         }
        //         $identificationService->sentRequestRegisterFace($identification, $device);
        //     } elseif ($identification->status == 2) {
        //         $identificationService->sendIdentifiSyncDevice($this->identificationId);
        //     }
        // } catch (\Throwable $th) {
        //     Log::info('Send identification false:', [
        //         'id' => $this->identificationId,
        //         'action' => !empty($identification) ? ($identification->status == 1 ? 'send' : 'sync') : 'no-data',
        //         'errors' => $th->getMessage()
        //     ]);
        // }
    }
}
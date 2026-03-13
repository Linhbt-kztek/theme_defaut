<?php
namespace App\Jobs;

use Illuminate\Bus\Queueable;
use Illuminate\Contracts\Queue\ShouldQueue;
use Illuminate\Queue\InteractsWithQueue;
use Illuminate\Queue\SerializesModels;
use App\Repositories\Notification\NotificationRepositoryInterface;
use App\Repositories\Staff\StaffRepositoryInterface;
use App\Services\Firebase\FirebaseService;
use Illuminate\Foundation\Bus\Dispatchable;
use Illuminate\Support\Facades\Log;

class ProcessNewNotification implements ShouldQueue
{
    use Dispatchable, InteractsWithQueue, Queueable, SerializesModels;

    protected $notificationId;

    public function __construct(
        $notificationId
    ) {
        $this->notificationId = $notificationId;
    }

    public function handle()
    {
        $firebaseService = app()->make(FirebaseService::class);
        $staffRepo = app()->make(StaffRepositoryInterface::class);
        $notificationRepo = app()->make(NotificationRepositoryInterface::class);
        $notification = $notificationRepo->getById($this->notificationId);

        try {
            $staff = $staffRepo->getById($notification->staff_id);
            if (!empty($staff->tokent_firebase)) {
                $result = $firebaseService->sendPushNotification(
                    $notification->title,
                    $notification->body,
                    $staff->tokent_firebase
                );

                if ($result['success']) {
                    $notificationRepo->update($notification->id, [
                        'is_sent' => true,
                        'firebase' => $result['message_id']
                    ]);
                }
            }
        } catch (\Throwable $th) {
            Log::info('Send notifiction false:', [
                'id' => $notification->id,
                'errors' => $th->getMessage()
            ]);
        }
    }
}
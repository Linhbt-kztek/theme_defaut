<?php

namespace App\Services;

use App\Enums\StorageFileEnum;
use App\Models\Staff;
use App\Repositories\Notification\NotificationRepositoryInterface;
use App\Repositories\Staff\StaffRepositoryInterface;
use App\Services\Firebase\FirebaseService;
use Carbon\Carbon;
use Exception;
use Illuminate\Database\Eloquent\Model;
use Illuminate\Support\Facades\DB;
use Illuminate\Support\Facades\Log;
use Illuminate\Support\Facades\Storage;

class NotificationService
{
    protected $notifications_cronfile = 'cronjob/has_notifications.json';
    public function __construct(
        protected FirebaseService $firebaseService,
        protected NotificationRepositoryInterface $notificationRepo,
        protected StaffRepositoryInterface $staffRepo
    ) {
    }

    public function sendNotification(
        Model|Staff $staff,
        string $container,
        string $action,
        string $title,
        mixed $body,
        $change_cronfile = true
    ): array {
        if (!$staff || empty($staff->id)) {
            return [
                'status' => false,
                'message' => 'Invalid staff data provided.'
            ];
        }


        try {
            DB::beginTransaction();
            if (!Storage::exists( $this->notifications_cronfile) && $change_cronfile) {
                $this->createNotificationFile();
            }
            $this->saveNotification(
                $staff->id,
                $container,
                $action,
                $title,
                $body,
                $change_cronfile
            );
            DB::commit();
            return [
                'status' => true,
                'message' => 'Notification saved to DB, waiting to be sent.'
            ];
        } catch (Exception $e) {
            DB::rollBack();
            return [
                'status' => false,
                'message' => 'Failed to save notification. Please try again later.'
            ];
        }
    }

    private function saveNotification(
        int|string $staff_id,
        string $container,
        string $action,
        string $title,
        string $body,
        $change_cronfile
    ): void {
        $this->notificationRepo->create([
            'id' => getGUID(),
            'staff_id' => $staff_id,
            'container' => $container,
            'action' => $action,
            'title' => $title,
            'body' => $body,
            'firebase' => '',
            'is_send' => false
        ]);

        if ($change_cronfile) {
            $fileContent = Storage::get($this->notifications_cronfile);
            $data = json_decode($fileContent, true);
            $data['has_notifications'] = true;
            $data['has_notifications_updated_at'] = Carbon::now()->format('Y-m-d H:i:s'); // Cập nhật thời gian thay đổi trạng thái

            Storage::put($this->notifications_cronfile, json_encode($data));
        }
    }


    public function sendPendingNotification(): void
    {
        // Nếu file không tồn tại, tạo file với giá trị mặc định
        if (!Storage::exists($this->notifications_cronfile)) {
            $this->createNotificationFile();
        }

        $data = $this->getNotificationFileData();

        // Nếu không có thông báo mới, bỏ qua cronjob
        if (!$data['has_notifications']) {
            $this->updateNotificationStatusInFile(false);
            return;
        }

        // Sau khi gửi, đặt has_notifications = false
        $this->updateNotificationStatusInFile(false);

        try {
            $pendingNotifications = $this->notificationRepo->getDataAllOption(
                where: [
                    'filter' => [['is_send', '=', 0]]
                ],
                limit: 0,
                with: ['staff']
            );

            foreach ($pendingNotifications as $notification) {
                $staff = $notification->staff;

                if (!$staff || empty($staff->tokent_firebase)) {
                    // Log::critical("User/token firebase empty, notification ID: {$notification->id}");
                    continue;
                }

                $result = $this->firebaseService->sendPushNotification(
                    $notification->title,
                    $notification->body,
                    $staff->tokent_firebase
                );

                if ($result['success']) {
                    $this->notificationRepo->update($notification->id, [
                        'is_send' => true,
                        'firebase' => $result['message_id']
                    ]);
                    // Log::info("Notification ID {$notification->id} sent.");
                } else {
                    // Log::warning("Failed to send notification ID {$notification->id}: " . $result['message']);
                }
            }
        } catch (Exception $e) {
            // Log::error("Cronjob Error: " . $e->getMessage());
        }
    }

    /**
     * Get the data from the notification status file.
     *
     * @return array The status data.
     */
    private function getNotificationFileData(): array
    {
        $fileContent = Storage::get($this->notifications_cronfile);
        return json_decode($fileContent, true);
    }

    /**
     * Creates the notification status file with the initial default values.
     *
     * @return void
     */
    public function createNotificationFile(): void
    {
        $initialData = [
            'has_notifications' => true,
            'has_notifications_updated_at' => Carbon::now()->format('Y-m-d H:i:s'),
            'remind' => [
                // Carbon::now()->format('Y-m-d') => [
                //     'isDayoff' => false,
                //     'onWorkingTime' => []
                // ]
            ]
        ];
        Storage::put($this->notifications_cronfile, json_encode($initialData));
    }

    /**
     * Updates the notification status in the file.
     *
     * @param bool $status The new status value.
     * @return void
     */
    private function updateNotificationStatusInFile($status): void
    {
        $fileContent = Storage::get($this->notifications_cronfile);
        $data = json_decode($fileContent, true);
        $data['has_notifications'] = $status;
        $data['has_notifications_updated_at'] = Carbon::now()->format('Y-m-d H:i:s'); // Cập nhật thời gian thay đổi trạng thái

        Storage::put($this->notifications_cronfile, json_encode($data));
    }

    public function sendToAllStaff($text)
    {
        $status = true;
        $message = 'Gửi thông báo thành công!';
        try {
            if (!empty($text)) {
                $staffs = $this->staffRepo->getDataAllOption([
                    'filter' => [
                        ['tokent_firebase', '!=', null],
                        ['tokent_firebase', '!=', ''],
                    ]
                ]);

                foreach ($staffs as $staff) {
                    $this->sendNotification(
                        $staff,
                        'home',
                        'sentAll',
                        'Thông báo',
                        $text,
                    );
                }
            } else {
                $status = false;
                $message = 'Không có nội dung thông báo!';
            }
        } catch (\Throwable $th) {
            $status = false;
            $message = 'Xảy ra lỗi trong quá trình gửi thông báo!';
        }
        return response()->json([
            'status' => $status ? 200 : 90,
            'message' => $message
        ]);
    }
}

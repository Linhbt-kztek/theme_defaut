<?php

namespace App\Observers;

use App\Jobs\ProcessNewNotification;
use App\Models\Notification;
use App\Repositories\Notification\NotificationRepositoryInterface;
use App\Repositories\Staff\StaffRepositoryInterface;
use App\Services\Firebase\FirebaseService;
use Illuminate\Support\Facades\Log;

class DatabaseNotificationObserver
{
    public function __construct(
        protected FirebaseService $firebaseService,
        protected StaffRepositoryInterface $staffRepo,
        protected NotificationRepositoryInterface $notificationRepo,
    ) {
    }
    public function created(Notification $notification)
    {
        ProcessNewNotification::dispatch($notification->id);     
    }
}
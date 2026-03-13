<?php

namespace App\Services\Firebase;

use Kreait\Firebase\Factory;
use Kreait\Firebase\Messaging\CloudMessage;
use Kreait\Firebase\Messaging\Notification;

class FirebaseService
{
    protected $messaging;
    public function __construct(
    ) {

    }
    public function sendPushNotification($title, $body, $tokent, $topic = 'all'): array
    {

        try {
            $firebase_credentials = base_path('app/Services/Firebase/config/firebase_credentials.json');
            $firebase = (new Factory)->withServiceAccount($firebase_credentials);
            $this->messaging = $firebase->createMessaging();
            // Gửi cách 1
            $message = CloudMessage::fromArray([
                'notification' => [
                    'title' => $title,
                    'body' => $body
                ],
                // 'topic' => $topic,
                'token' => $tokent,
            ]);

            // Gửi cách 2
            // $message = CloudMessage::withTarget('token', $tokent)
            //     ->withNotification(Notification::create(
            //         $title,
            //         $body
            //     ));

            $response = $this->messaging->send($message);

            return [
                'success' => true,
                'message' => 'Notification sent successfully.',
                'message_id' => $response['name'] // Cung cấp ID của thông báo đã gửi
            ];
        } catch (\Exception $e) {
            return [
                'success' => false,
                'message' => $e->getMessage()
            ];
        }
    }
}

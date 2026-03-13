<?php

namespace App\Observers;

use App\Jobs\ProcessActionIdentification;
use App\Jobs\ProcessNewEvent;
use App\Models\Event;
use App\Repositories\Event\EventRepositoryInterface;
use App\Repositories\Meeting\MeetingRepositoryInterface;
use App\Repositories\MeetingDetail\MeetingDetailRepositoryInterface;
use App\Repositories\Schedule\ScheduleRepositoryInterface;
use App\Repositories\Screen\ScreenRepositoryInterface;
use App\Services\MessageMQ\RabbitMQService;
use Carbon\Carbon;

class DatabaseEventObserver
{
    public function __construct(
        protected ScheduleRepositoryInterface $scheduleRepo,
        protected MeetingDetailRepositoryInterface $meetingDetailRepo,
        protected EventRepositoryInterface $eventRepo,
        protected RabbitMQService $rabbitMQService
    ) {
    }
    public function created(Event $event)
    {
        $meeting_detail_id = $event->meeting_detail_id;
        $schedules = $this->scheduleRepo->getDataAllOption([
            'filter' => [
                ['meeting_detail_id', '=', $meeting_detail_id]
            ]
        ]);

        if ($schedules->count() > 0) {
            ProcessNewEvent::dispatch($event->id);
            // $this->sendMessageMQ($event->id);
        }
    }

    protected function sendMessageMQ($event_id)
    {
        $event = $this->eventRepo->getById($event_id);
        $meeting_detail_id = $event->meeting_detail_id;
        $conference_room_id = $event->conference_room_id;
        $schedules = $this->scheduleRepo->getDataAllOption([
            'filter' => [
                ['meeting_detail_id', '=', $meeting_detail_id]
            ]
        ], 0, ['screen']);

        if ($schedules->count() > 0) {

            $meeting_detail = $this->meetingDetailRepo->getById($meeting_detail_id, ['meeting.guest', 'meeting.contact']);

            $all_persion = count($meeting_detail->meeting->guest) + count($meeting_detail->meeting->contact);

            $all_events = $this->eventRepo->getDataAllOption([
                'filter' => [
                    ['meeting_detail_id', '=', $meeting_detail_id],
                    ['conference_room_id', '=', $conference_room_id]
                ]
            ]);

            $all_event_group = $all_events->groupBy('object_id');

            $all_persion_with_event = count($all_event_group);

            foreach ($schedules as $key => $value) {
                if ($value->more_option == 2) {
                    $message = "Số đại biểu đã tham dự: $all_persion_with_event";
                }

                if ($value->more_option == 3) {
                    $message = "Số đại biểu đã tham dự: $all_persion_with_event/$all_persion";
                }

                $screen = $value->screen;

                $name = 'screen_device';
                $routing_key = 'screen_device' . $screen->screen_device_id;
                $to = 'screen_device' . $screen->screen_device_id;
                $data = [
                    'time' => Carbon::now(),
                    'action' => 'update-more-option',
                    'message' => $message
                ];
                $this->rabbitMQService->publish(json_encode($data), $name, $to, $routing_key);
            }
        }
    }

}
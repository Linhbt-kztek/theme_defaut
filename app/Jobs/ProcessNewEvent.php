<?php
namespace App\Jobs;

use App\Repositories\Event\EventRepositoryInterface;
use App\Repositories\MeetingDetail\MeetingDetailRepositoryInterface;
use Illuminate\Bus\Queueable;
use Illuminate\Contracts\Queue\ShouldQueue;
use Illuminate\Queue\InteractsWithQueue;
use Illuminate\Queue\SerializesModels;
use App\Repositories\Schedule\ScheduleRepositoryInterface;
use App\Services\MessageMQ\RabbitMQService;
use Carbon\Carbon;
use Illuminate\Foundation\Bus\Dispatchable;

class ProcessNewEvent implements ShouldQueue
{
    use Dispatchable, InteractsWithQueue, Queueable, SerializesModels;

    protected $event_id;

    public function __construct(
        $event_id
    ) {
        $this->event_id = $event_id;
    }

    public function handle()
    {
        $eventRepo = app()->make(EventRepositoryInterface::class);
        $scheduleRepo = app()->make(ScheduleRepositoryInterface::class);
        $meetingDetailRepo = app()->make(MeetingDetailRepositoryInterface::class);
        $rabbitMQService = app()->make(RabbitMQService::class);




        $event = $eventRepo->getById($this->event_id);
        $meeting_detail_id = $event->meeting_detail_id;
        $conference_room_id = $event->conference_room_id;
        $schedules = $scheduleRepo->getDataAllOption([
            'filter' => [
                ['meeting_detail_id', '=', $meeting_detail_id]
            ]
        ], 0, ['screen']);

        if ($schedules->count() > 0) {

            $meeting_detail = $meetingDetailRepo->getById($meeting_detail_id, ['meeting.guest', 'meeting.contact']);

            $all_persion = count($meeting_detail->meeting->guest) + count($meeting_detail->meeting->contact);

            $all_events = $eventRepo->getDataAllOption([
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
                $rabbitMQService->publish(json_encode($data), $name, $to, $routing_key);
            }
        }
    }
}
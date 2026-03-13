<?php

namespace App\Console;

use App\Models\Tournament;
use Carbon\Carbon;
use Illuminate\Console\Scheduling\Schedule;
use Illuminate\Foundation\Console\Kernel as ConsoleKernel;

class Kernel extends ConsoleKernel
{
    /**
     * Register the commands for the application.
     *
     * @return void
     */
    protected function commands()
    {
        $this->load(__DIR__ . '/Commands');

        require base_path('routes/console.php');
    }

    protected function schedule(Schedule $schedule)
    {
        $schedule->command('send:request')->everyMinute()
            ->withoutOverlapping()
            ->sendOutputTo(storage_path('logs/schedule.log'));
        $schedule->command('send:syncdevice')->everyMinute()
            ->withoutOverlapping()
            ->sendOutputTo(storage_path('logs/schedule.log'));
    }
}

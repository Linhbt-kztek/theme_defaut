<?php

namespace App\Jobs;

use App\Mail\ConfirmMail;
use Carbon\Carbon;
use Illuminate\Bus\Queueable;
use Illuminate\Contracts\Queue\ShouldBeUnique;
use Illuminate\Contracts\Queue\ShouldQueue;
use Illuminate\Foundation\Bus\Dispatchable;
use Illuminate\Queue\InteractsWithQueue;
use Illuminate\Queue\SerializesModels;
use Illuminate\Support\Facades\DB;
use Illuminate\Support\Facades\Mail;

class SendConfirmMailJob implements ShouldQueue
{
    protected $mailsRepo;
    use Dispatchable, InteractsWithQueue, Queueable, SerializesModels;

    /**
     * Create a new job instance.
     *
     * @return void
     */
    protected $athlete;
    public function __construct(
        $athlete,
    )
    {
        $this->athlete = $athlete;

    }

    /**
     * Execute the job.
     *
     * @return void
     */
    public function handle()
    {
        Mail::to($this->athlete['email'])->send(new ConfirmMail($this->athlete));
        DB::table('tbl_athlete_mail')->insert([
            'id' => getGUID(),
            'mail_id' => '54D528AC-7F0A-6B8A-8678-FE3D46DB576F',
            'athlete_id' => $this->athlete['id'],
            'created_date' => Carbon::now(),
            'modified_date' => Carbon::now(),
            'deleted_date' => null,
            'is_delete' => 0,
        ]);
    }
}

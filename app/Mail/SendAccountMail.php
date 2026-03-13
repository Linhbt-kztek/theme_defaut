<?php

namespace App\Mail;

use Illuminate\Bus\Queueable;
use Illuminate\Mail\Mailable;
use Illuminate\Queue\SerializesModels;

class SendAccountMail extends Mailable
{
    use Queueable, SerializesModels;
    protected $staff;

    public function __construct(
        $staff
    ) {
        $this->staff = $staff;
    }

    public function build()
    {
        return $this->subject('Thông báo tài khoản Phòng họp thông minh')->view('mail.sent_account', ['staff' => $this->staff])
            ->from('baotangvutru@baotangvutru.com', 'Phòng họp thông minh');
        ;
    }
}

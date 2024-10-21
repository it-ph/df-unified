<?php

namespace App\Mail;

use Illuminate\Bus\Queueable;
use Illuminate\Contracts\Queue\ShouldQueue;
use Illuminate\Mail\Mailable;
use Illuminate\Queue\SerializesModels;

class ForgotPasswordEmail extends Mailable implements ShouldQueue
{
    use Queueable, SerializesModels;
    public $request_key;

    /**
     * Create a new message instance.
     *
     * @return void
     */
    public function __construct($request_key)
    {
        $this->request_key = $request_key;
    }

    /**
     * Build the message.
     *
     * @return $this
     */
    public function build()
    {
        return $this->subject('SLA Tracker : Forgot Password')->view('pages.admin.users.mail.forgot-password');
    }
}

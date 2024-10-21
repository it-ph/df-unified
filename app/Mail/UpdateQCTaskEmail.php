<?php

namespace App\Mail;

use Illuminate\Bus\Queueable;
use Illuminate\Contracts\Queue\ShouldQueue;
use Illuminate\Mail\Mailable;
use Illuminate\Queue\SerializesModels;

class UpdateQCTaskEmail extends Mailable implements ShouldQueue
{
    use Queueable, SerializesModels;

    public $audit_log;

    /**
     * Create a new message instance.
     *
     * @return void
     */
    public function __construct($audit_log)
    {
        $this->audit_log = $audit_log;
    }

    /**
     * Build the message.
     *
     * @return $this
     */
    public function build()
    {
        return $this->subject('QC Job Status Change Alert')->view('pages.mails.qcs.updates');
    }
}

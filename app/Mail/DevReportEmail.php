<?php

namespace App\Mail;

use Illuminate\Bus\Queueable;
use Illuminate\Mail\Mailable;
use Illuminate\Queue\SerializesModels;
use Illuminate\Support\Facades\Storage;
use Illuminate\Contracts\Queue\ShouldQueue;

class DevReportEmail extends Mailable implements ShouldQueue
{
    use Queueable, SerializesModels;

    public $job, $filename, $filePath, $content, $total, $completed, $sla_met, $pending_external_qc, $isAdmin;

    /**
     * Create a new message instance.
     *
     * @return void
     */
    public function __construct($job, $filename, $filePath, $content, $total, $completed, $sla_met, $pending_external_qc, $isAdmin)
    {
        $this->job = $job;
        $this->filename = $filename;
        $this->filePath = $filePath;
        $this->content = $content;
        $this->total = $total;
        $this->completed = $completed;
        $this->sla_met = $sla_met;
        $this->pending_external_qc = $pending_external_qc;
        $this->isAdmin = $isAdmin;
    }

    /**
     * Build the message.
     *
     * @return $this
     */
    public function build()
    {
        // Attach the Excel file
        $this->attach(Storage::path($this->filePath), [
            'as' => $this->filename,
            'mime' => 'application/vnd.openxmlformats-officedocument.spreadsheetml.sheet',
        ]);

        return $this->subject($this->filename)
                    ->view('pages.mails.dev-report.index')
                    ->with([
                        'content' => $this->content,
                        'total' => $this->total,
                        'completed' => $this->completed,
                        'sla_met_ctr' => $this->sla_met,
                        'pending_external_qc' => $this->pending_external_qc,
                        'isAdmin' => $this->isAdmin,
                    ]);
    }
}

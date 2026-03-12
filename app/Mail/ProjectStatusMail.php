<?php

namespace App\Mail;

use Illuminate\Bus\Queueable;
use Illuminate\Contracts\Queue\ShouldQueue;
use Illuminate\Mail\Mailable;
use Illuminate\Mail\Mailables\Content;
use Illuminate\Mail\Mailables\Envelope;
use Illuminate\Queue\SerializesModels;

class ProjectStatusMail extends Mailable implements ShouldQueue
{
    use Queueable, SerializesModels;

    public $project;
    public $status;
    public $reason;

    /**
     * Create a new message instance.
     */
    public function __construct($project, $status, $reason = null)
    {
        $this->project = $project;
        $this->status = $status;
        $this->reason = $reason;
    }

    /**
     * Email subject
     */
    public function envelope(): Envelope
    {
        return new Envelope(
            subject: 'Project Status Update'
        );
    }

    /**
     * Email view
     */
    public function content(): Content
    {
        return new Content(
            view: 'emails.project_status',
            with: [
                'project' => $this->project,
                'status' => $this->status,
                'reason' => $this->reason
            ]
        );
    }

    /**
     * Attachments
     */
    public function attachments(): array
    {
        return [];
    }
}
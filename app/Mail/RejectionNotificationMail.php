<?php

namespace App\Mail;

use App\Models\Applicant;
use Illuminate\Bus\Queueable;
use Illuminate\Mail\Mailable;
use Illuminate\Mail\Mailables\Content;
use Illuminate\Mail\Mailables\Envelope;
use Illuminate\Queue\SerializesModels;

class RejectionNotificationMail extends Mailable
{
    use Queueable, SerializesModels;

    public $applicant;

    /**
     * Create a new message instance.
     */
    public function __construct(Applicant $applicant)
    {
        $this->applicant = $applicant;
    }

    /**
     * Get the message envelope.
     */
    public function envelope(): Envelope
    {
        return new Envelope(
            to: $this->applicant->information['Email'] ?? 'no-reply@example.com',
            subject: 'Application Status Update - ' . ($this->applicant->job->title ?? 'Job Position'),
        );
    }

    /**
     * Get the message content definition.
     */
    public function content(): Content
    {
        return new Content(
            view: 'emails.rejection',
            with: [
                'applicantName' => $this->applicant->information['Name'] ?? $this->applicant->information['Name'] ?? 'Applicant',
                'applicantEmail' => $this->applicant->information['Email'] ?? $this->applicant->information['Email'] ?? '',
                'applicantPhone' => $this->applicant->information['Phone'] ?? $this->applicant->information['Phone'] ?? '',
                'applicantId' => $this->applicant->id,
                'jobTitle' => $this->applicant->job_app->title ?? '-',
                'feedback' => @$this->applicant->feedback,
                'companyName' => config('app.name', 'Our Company'),
            ]
        );
    }

    /**
     * Get the attachments for the message.
     *
     * @return array<int, \Illuminate\Mail\Mailables\Attachment>
     */
    public function attachments(): array
    {
        return [];
    }
}

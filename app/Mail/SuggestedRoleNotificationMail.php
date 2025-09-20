<?php

namespace App\Mail;

use App\Models\Applicant;
use App\Models\Job;
use App\Models\JobApp;
use Illuminate\Bus\Queueable;
use Illuminate\Mail\Mailable;
use Illuminate\Mail\Mailables\Content;
use Illuminate\Mail\Mailables\Envelope;
use Illuminate\Queue\SerializesModels;

class SuggestedRoleNotificationMail extends Mailable
{
    use Queueable, SerializesModels;

    public $jobApp;
    public $id;
    public $name;
    public $phone;
    public $email;

    /**
     * Create a new message instance.
     */
    public function __construct(JobApp $jobApp, $email, $name, $phone, $id=null)
    {
        $this->jobApp = $jobApp;
        $this->email = $email;
        $this->phone = $phone;
        $this->name = $name;
        $this->id = $id;
    }

    /**
     * Get the message envelope.
     */
    public function envelope(): Envelope
    {
        return new Envelope(
            to: $this->email,
            subject: '🎯 Perfect Job Match Found: ' . @$this->jobApp->title,
        );
    }

    /**
     * Get the message content definition.
     */
    public function content(): Content
    {
        return new Content(
            view: 'emails.suggestedRole',
            with: [
                'applicantName' => $this->name ?? '-',
                'applicantEmail' => $this->email,
                'applicantPhone' => $this->phone ?? null,
                'applicantId' => $this->id,
                'jobTitle' => $this->jobApp->title ?? '-',
                'jobDescription' => @$this->jobApp->description,
                'jobId' => $this->jobApp->id,
                'applyUrl' => route('dashboard.applicant.jobs.show', @$this->jobApp->id),
                'matchReason' => 'Based on your skills and experience, we believe this role would be an excellent fit for your career growth.',
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

<?php

namespace App\Console\Commands;

use App\Mail\InterviewNotificationMail;
use App\Models\Applicant;
use Illuminate\Console\Command;
use Illuminate\Support\Facades\Mail;

class SendInterviewEmails extends Command
{
    /**
     * The name and signature of the console command.
     *
     * @var string
     */
    protected $signature = 'applicants:send-interview-emails
                            {--id= : Send to specific applicant ID}
                            {--all : Send to all waiting applicants}
                            {--recent : Send to recently waiting (last 24 hours)}';

    /**
     * The console command description.
     *
     * @var string
     */
    protected $description = 'Send waiting emails to applicants';

    /**
     * Execute the console command.
     */
    public function handle()
    {
        $query = Applicant::where('status', 'waiting for answering')
            ->where(function($q) {
                $q->whereNull('emails->waiting_for_answers')
                    ->orWhere('emails->waiting_for_answers', false);
            });
        if ($this->option('id')) {
            $query->where('id', $this->option('id'));
        } elseif ($this->option('recent')) {
            $query->where('updated_at', '>=', now()->subDay());
        } elseif (!$this->option('all')) {
            $this->error('Please specify --id, --all, or --recent option');
            return 1;
        }

        $applicants = $query->get()->filter(function ($applicant) {
            return !empty($applicant->information['Email']);
        });

        if ($applicants->isEmpty()) {
            $this->info('No waiting applicants found with email addresses.');
            return 0;
        }

        $this->info("Found {$applicants->count()} waiting applicants with email addresses.");

        $bar = $this->output->createProgressBar($applicants->count());
        $bar->start();

        $sent = 0;
        $failed = 0;

        foreach ($applicants as $applicant) {
            try {
                Mail::send(new InterviewNotificationMail($applicant));
                $sent++;
                $email = $applicant->information['Email'] ?? 'N/A';
                $name = $applicant->information['Name'] ?? $applicant->information['Name'] ?? 'Unknown';
                $this->line("\n✅ Sent to: {$name} ({$email})");
            } catch (\Exception $e) {
                $failed++;
                $email = $applicant->information['Email'] ?? 'N/A';
                $name = $applicant->information['Name'] ?? $applicant->information['Name'] ?? 'Unknown';
                $this->line("\n❌ Failed to send to: {$name} ({$email}) - {$e->getMessage()}");
            }
            $applicant->update([
                'emails' => empty($applicant->emails) ? [
                    'waiting_for_answers' => true,
                    'rejected' => false,
                    'approved' => false,
                ] : array_merge($applicant->emails, ['waiting_for_answers' => true])
            ]);
            $bar->advance();
        }

        $bar->finish();

        $this->line("\n");
        $this->info("✅ Successfully sent: {$sent} emails");
        if ($failed > 0) {
            $this->error("❌ Failed to send: {$failed} emails");
        }

        return 0;
    }
}

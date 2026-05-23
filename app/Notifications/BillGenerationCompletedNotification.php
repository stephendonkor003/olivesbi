<?php

namespace App\Notifications;

use AlhajiAki\Sms\Notification\Messages\SmsMessage;
use Illuminate\Bus\Queueable;
use Illuminate\Contracts\Queue\ShouldQueue;
use Illuminate\Notifications\Messages\MailMessage;
use Illuminate\Notifications\Notification;

class BillGenerationCompletedNotification extends Notification implements ShouldQueue
{
    use Queueable;

    public function __construct(
        public string $batchName,
        public int $totalJobs,
        public int $processedJobs,
        public int $failedJobs,
        public string $billType
    ) {}

    public function via(object $notifiable): array
    {
        $channels = ['mail'];

        if (! empty($notifiable->email)) {
            $channels[] = 'mail';
        }

        if (! empty($notifiable->phone)) {
            $channels[] = 'sms';
        }

        return $channels;
    }

    public function toMail(object $notifiable): MailMessage
    {
        $status = $this->failedJobs > 0 ? 'completed with errors' : 'completed successfully';
        $billTypeLabel = ucfirst($this->billType);

        $message = (new MailMessage)
            ->subject('Bill Generation Completed')
            ->greeting("Hello {$notifiable->name}!")
            ->line("Your {$billTypeLabel} bill generation has {$status}.")
            ->line("Batch: {$this->batchName}")
            ->line("Total Jobs: {$this->totalJobs}")
            ->line("Processed: {$this->processedJobs}");

        if ($this->failedJobs > 0) {
            $message->line("Failed: {$this->failedJobs}")
                ->error('Some jobs failed during processing. Please check the system logs for details.');
        } else {
            $message->success('All bills were generated successfully!');
        }

        return $message->line('Thank you for using ERMS!');
    }

    public function toSms(object $notifiable): SmsMessage
    {
        $status = $this->failedJobs > 0 ? 'completed with errors' : 'completed successfully';
        $billTypeLabel = ucfirst($this->billType);

        $msg = "ERMS: Your {$billTypeLabel} bill generation has {$status}. {$this->processedJobs}/{$this->totalJobs} jobs processed.";

        if ($this->failedJobs > 0) {
            $msg .= " {$this->failedJobs} failed.";
        }

        return (new SmsMessage)
            ->message($msg);
    }
}

<?php

namespace App\Jobs\Bill;

use Illuminate\Bus\Queueable;
use Illuminate\Contracts\Queue\ShouldQueue;
use Illuminate\Foundation\Bus\Dispatchable;
use Illuminate\Queue\InteractsWithQueue;
use Illuminate\Queue\SerializesModels;

class SendGeneratedBillNotificationsJob implements ShouldQueue
{
    use Dispatchable, InteractsWithQueue, Queueable, SerializesModels;

    /**
     * Create a new job instance.
     */
    public function __construct(public array $billIds) {}

    /**
     * Execute the job.
     */
    public function handle(): void
    {
        if (count($this->billIds) <= 0) {
            return;
        }

        foreach ($this->billIds as $billId) {
            SendBillGenerationNotificationJob::dispatch($billId);
        }
    }
}

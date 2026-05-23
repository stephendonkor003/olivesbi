<?php

namespace App\Jobs\Payment;

use App\Actions\SMS\SendSMS;
use Illuminate\Bus\Queueable;
use Illuminate\Contracts\Queue\ShouldQueue;
use Illuminate\Foundation\Bus\Dispatchable;
use Illuminate\Queue\InteractsWithQueue;
use Illuminate\Queue\SerializesModels;

class SendPaymentSMS implements ShouldQueue
{
    use Dispatchable, InteractsWithQueue, Queueable, SerializesModels;

    protected $payment;

    /**
     * Create a new job instance.
     */
    public function __construct($payment)
    {
        $this->payment = $payment;
    }

    /**
     * Execute the job.
     */
    public function handle(): void
    {
        $msg = 'Thank you for paying your property Rate of GHS '.$this->payment->amount.' to Ablekuma North Municipal Assembly.';

        $phone = $this->payment->phone;

        SendSMS::sendSMS($phone, $msg);
    }
}

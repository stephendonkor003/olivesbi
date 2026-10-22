<?php

namespace App\Jobs\OTP;

use App\Actions\SMS\SendSensitiveSMS;
use Illuminate\Bus\Queueable;
use Illuminate\Contracts\Queue\ShouldQueue;
use Illuminate\Foundation\Bus\Dispatchable;
use Illuminate\Queue\InteractsWithQueue;
use Illuminate\Queue\SerializesModels;

class SendOTPBillSMS implements ShouldQueue
{
    use Dispatchable, InteractsWithQueue, Queueable, SerializesModels;

    /**
     * Create a new job instance.
     */
    protected $otp;

    public function __construct($otp)
    {
        $this->otp = $otp;
    }

    /**
     * Execute the job.
     */
    public function handle(): void
    {
        $msg = 'Your bill activation code is '.$this->otp->code;
        $phone = '';

        if ($this->otp?->bill) {
            if (is_null($this->otp->bill->business_id) && $this->otp->bill->property) {
                $phone = $this->otp->bill->property?->customer?->phone_no_primary ?? '';
            } elseif (is_null($this->otp->bill->property_id) && $this->otp->bill->business) {
                $phone = $this->otp->bill->business?->customer?->phone_no_primary ?? '';
            }
        }

        SendSensitiveSMS::sendSMS($phone, $msg);
    }
}

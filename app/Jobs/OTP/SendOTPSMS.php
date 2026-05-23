<?php

namespace App\Jobs\OTP;

use App\Actions\SMS\SendSMS;
use App\Models\OTP;
use Illuminate\Bus\Queueable;
use Illuminate\Contracts\Queue\ShouldQueue;
use Illuminate\Foundation\Bus\Dispatchable;
use Illuminate\Queue\InteractsWithQueue;
use Illuminate\Queue\SerializesModels;

class SendOTPSMS implements ShouldQueue
{
    use Dispatchable, InteractsWithQueue, Queueable, SerializesModels;

    /**
     * Create a new job instance.
     */
    protected $citizen;

    public function __construct($citizen)
    {
        $this->citizen = $citizen;
    }

    /**
     * Execute the job.
     */
    public function handle(): void
    {
        $otp = mt_rand(100000, 999999);

        $data = [
            'citizen_id' => $this->citizen->id,
            'code' => $otp,
        ];

        $otpData = OTP::create($data);

        $msg = 'Your citizen activation code is '.$otpData->code;

        $phone = $this->citizen->telephone_number;

        SendSMS::sendSMS($phone, $msg);
    }
}

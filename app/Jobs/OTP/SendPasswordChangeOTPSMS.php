<?php

namespace App\Jobs\OTP;

use App\Actions\SMS\SendSensitiveSMS;
use App\Models\OTP;
use App\Support\PasswordResetCodes;
use Illuminate\Bus\Queueable;
use Illuminate\Contracts\Queue\ShouldQueue;
use Illuminate\Foundation\Bus\Dispatchable;
use Illuminate\Queue\InteractsWithQueue;
use Illuminate\Queue\SerializesModels;

class SendPasswordChangeOTPSMS implements ShouldQueue
{
    use Dispatchable, InteractsWithQueue, Queueable, SerializesModels;

    protected $user;

    /**
     * Create a new job instance.
     */
    public function __construct($user)
    {
        $this->user = $user;
    }

    /**
     * Execute the job.
     */
    public function handle(): void
    {
        SendSensitiveSMS::ensureAvailable();
        OTP::query()->where('user_id', $this->user->id)->where('type', 'password')->delete();

        do {
            $otp = random_int(100000, 999999);
        } while (OTP::query()->where('type', 'password')->where('code', $otp)
            ->where('created_at', '>=', now()->subMinutes(PasswordResetCodes::LIFETIME_MINUTES))->exists());

        $data = [
            'user_id' => $this->user->id,
            'code' => $otp,
            'type' => 'password',
        ];

        $otpData = OTP::create($data);

        $msg = 'Your password change OTP code is '.$otpData->code;

        $phone = $this->user->phone;

        try {
            SendSensitiveSMS::sendSMS($phone, $msg);
        } catch (\Throwable $exception) {
            $otpData->delete();
            throw $exception;
        }
    }
}

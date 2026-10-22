<?php

namespace App\Jobs\OTP;

use App\Actions\SMS\SendSensitiveSMS;
use App\Models\OTP;
use App\Support\BillOtpCodes;
use Illuminate\Bus\Queueable;
use Illuminate\Contracts\Queue\ShouldQueue;
use Illuminate\Foundation\Bus\Dispatchable;
use Illuminate\Queue\InteractsWithQueue;
use Illuminate\Queue\SerializesModels;
use Illuminate\Validation\ValidationException;

class SendOTPBillSMS implements ShouldQueue
{
    use Dispatchable, InteractsWithQueue, Queueable, SerializesModels;

    /**
     * Create a new job instance.
     */
    protected ?int $otpId = null;

    protected ?string $billId = null;

    protected ?string $codeIdentity = null;

    public function __construct($otp)
    {
        if ($otp instanceof OTP) {
            $this->otpId = $otp->getKey();
            $this->billId = $otp->bills_id;
            $this->codeIdentity = hash_hmac('sha256', (string) $otp->code, config('app.key'));
        }
    }

    /**
     * Execute the job.
     */
    public function handle(): void
    {
        $otp = null;
        try {
            SendSensitiveSMS::ensureAvailable();
            $current = OTP::query()->whereKey($this->otpId)->first();
            if (! $current || (string) $current->bills_id !== $this->billId || $this->codeIdentity === null
                || ! hash_equals($this->codeIdentity, hash_hmac('sha256', (string) $current->code, config('app.key')))) {
                throw ValidationException::withMessages(['code' => 'The bill verification code is invalid or expired.']);
            }
            $otp = $current;
            $phone = BillOtpCodes::recipient($otp);
            SendSensitiveSMS::sendSMS($phone, 'Your bill activation code is '.$otp->code);
        } catch (\Throwable $exception) {
            if ($otp instanceof OTP) {
                BillOtpCodes::discardFailed($otp);
            }
            throw $exception;
        }
    }
}

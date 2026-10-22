<?php

namespace App\Jobs\Registration;

use App\Actions\SMS\SendSMS;
use Illuminate\Bus\Queueable;
use Illuminate\Contracts\Queue\ShouldQueue;
use Illuminate\Foundation\Bus\Dispatchable;
use Illuminate\Queue\InteractsWithQueue;
use Illuminate\Queue\SerializesModels;

class SendRegistrationReminder implements ShouldQueue
{
    use Dispatchable, InteractsWithQueue, Queueable, SerializesModels;

    /**
     * Create a new job instance.
     */
    protected $citizen;

    protected $password;

    public function __construct($citizen, $password = null)
    {
        $this->citizen = $citizen;
        // Retain the optional legacy argument for existing callers without
        // serializing or sending a credential. Previously queued jobs may still
        // contain the old property; handle() deliberately never reads it.
        $this->password = null;
    }

    /**
     * Execute the job.
     */
    public function handle(): void
    {
        $name = $this->citizen->fname ?? $this->citizen->first_name;
        $account = $this->citizen->erms_id ?? $this->citizen->account_number;
        $phone = $this->citizen->phone_no_primary ?? $this->citizen->telephone_number;
        $msg = 'Hello '.$name.",\n";
        $msg .= 'Thanks for registering on ERMS Plus, your Account No. is: '.$account.".\n";
        $msg .= 'Set or reset your password at '.route('auth.sendOTP').".\n";
        $msg .= "Request a one-time code using your registered phone number.\n";
        $msg .= 'You can use your phone number or email submitted during registration to login at '.config('erms.website')."\n";
        $msg .= 'For further inquiries, please call this number '.config('erms.contact');

        SendSMS::sendSMS($phone, $msg);
    }
}

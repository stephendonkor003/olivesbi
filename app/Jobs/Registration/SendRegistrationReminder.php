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

    public function __construct($citizen, $password)
    {
        $this->citizen = $citizen;
        $this->password = $password;
    }

    /**
     * Execute the job.
     */
    public function handle(): void
    {
        $msg = 'Hello '.$this->citizen->fname.",\n";
        $msg .= 'Thanks for registering on ERMS Plus, your Account No. is: '.$this->citizen->erms_id.".\n";
        $msg .= 'Your Password is: '.$this->password."\n";
        $msg .= 'You can use your phone number or email submitted during registration to login at '.config('erms.website')."\n";
        $msg .= "You will be required to change your password after logging in the first time.\n";
        $msg .= 'For further inquiries, please call this number '.config('erms.contact');

        SendSMS::sendSMS($this->citizen->phone_no_primary, $msg);
    }
}

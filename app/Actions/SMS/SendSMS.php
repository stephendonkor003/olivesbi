<?php

namespace App\Actions\SMS;

use AlhajiAki\Sms\Sms;

class SendSMS
{
    public static function sendSMS($phone, $msg, $senderId = null): void
    {
        $phone = phone($phone, ['GH'])->formatE164();

        Sms::send($msg, $phone, $senderId);
    }
}

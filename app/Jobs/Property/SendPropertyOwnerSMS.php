<?php

namespace App\Jobs\Property;

use App\Actions\SMS\SendSMS;
use Illuminate\Bus\Queueable;
use Illuminate\Contracts\Queue\ShouldQueue;
use Illuminate\Foundation\Bus\Dispatchable;
use Illuminate\Queue\InteractsWithQueue;
use Illuminate\Queue\SerializesModels;

class SendPropertyOwnerSMS implements ShouldQueue
{
    use Dispatchable, InteractsWithQueue, Queueable, SerializesModels;

    /**
     * Create a new job instance.
     */
    protected $property;

    public function __construct($property)
    {
        $this->property = $property;
    }

    /**
     * Execute the job.
     */
    public function handle(): void
    {
        $msg = 'Hello '.$this->property->customer->first_name.'.';
        $msg .= ' Your property number of '.$this->property->property_number;
        $msg .= ' has been assigned to your account No. '.$this->property->customer->account_number.'.';

        $phone = $this->property->customer->telephone_number;

        SendSMS::sendSMS($phone, $msg);
    }
}

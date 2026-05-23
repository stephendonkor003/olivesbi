<?php

namespace App\Jobs\Bill;

use App\Actions\SMS\SendSMS;
use App\Models\Bill;
use Carbon\Carbon;
use Illuminate\Bus\Queueable;
use Illuminate\Contracts\Queue\ShouldQueue;
use Illuminate\Foundation\Bus\Dispatchable;
use Illuminate\Queue\InteractsWithQueue;
use Illuminate\Queue\SerializesModels;

class SendBillGenerationNotificationJob implements ShouldQueue
{
    use Dispatchable, InteractsWithQueue, Queueable, SerializesModels;

    /**
     * Create a new job instance.
     */
    public function __construct(public int $billId) {}

    /**
     * Execute the job.
     */
    public function handle(): void
    {
        $bill = Bill::with(['property.customer', 'business.customer', 'assembly'])
            ->find($this->billId);

        if (! $bill) {
            return;
        }

        if ($bill->bills_year != now()->year) {
            return;
        }

        // Determine customer based on property or business
        $phone = '';
        $firstName = '';
        $lastName = '';

        if ($bill->property && $bill->property->customer) {
            $customer = $bill->property->customer;
            $firstName = $customer->fname ?? '';
            $lastName = $customer->lname ?? '';
            $phone = $customer->phone_no_primary ?? '';
        } elseif ($bill->business && $bill->business->customer) {
            $customer = $bill->business->customer;
            $firstName = $customer->fname ?? '';
            $lastName = $customer->lname ?? '';
            $phone = $customer->phone_no_primary ?? '';
        }

        $hasValidPhone = ! empty($phone) && phone($phone, ['GH'])->isValid();

        if (! $hasValidPhone) {
            return;
        }

        $dueDate = Carbon::parse($bill->billing_date)->addMonths(3)->endOfMonth();
        $link = route('public.bills.show', $bill->bills_id);

        $arrears = number_format($bill->arrears, 2);
        $amount = number_format($bill->amount, 2);
        $total = number_format($bill->amount + $bill->arrears, 2);

        $msg = "Dear {$firstName} {$lastName},\n";
        $msg .= "You have a new bill from {$bill->assembly_code} for {$bill->bills_year}\n";
        $msg .= "Arrears: {$arrears}\n";
        $msg .= "Current Amount: {$amount}\n";
        $msg .= "Total: {$total}\n\n";
        $msg .= "Click on the link below to get your BILL.\n";
        $msg .= "{$link}\n\n";
        $msg .= "Settle your bill IMMEDIATELY or by {$dueDate->format('jS F, Y')} to avoid ENFORCEMENT.\n";
        $msg .= "SHOW PAST RECEIPTS AS PROOF OF PAYMENT.\n\n";

        $msg .= 'THANK YOU.';

        SendSMS::sendSMS($phone, $msg, $bill->assembly_code);
    }
}

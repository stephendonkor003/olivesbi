<?php

namespace App\Services;

use App\Actions\SMS\SendSMS;
use App\Models\WastePickup;
use Illuminate\Support\Facades\Mail;
use Throwable;

class WastePickupNotifier
{
    public function due(WastePickup $pickup): void
    {
        $pickup->loadMissing(['booking.user', 'booking.property', 'booking.business', 'company.registeredBy']);

        $asset = $pickup->booking?->property ?: $pickup->booking?->business;
        $assetName = $asset?->account_no ?: ($asset?->business_name ?: ($asset?->erms_id ?: 'your asset'));
        $when = $pickup->scheduled_for?->format('d M Y H:i');
        $message = "Olives BI Waste: Pickup for {$assetName} is due on {$when} with {$pickup->company?->name}.";

        $this->notifyUserAndCompany($pickup, 'Waste pickup due', $message);
    }

    public function missed(WastePickup $pickup): void
    {
        $pickup->loadMissing(['booking.user', 'booking.property', 'booking.business', 'company.registeredBy']);

        $asset = $pickup->booking?->property ?: $pickup->booking?->business;
        $assetName = $asset?->account_no ?: ($asset?->business_name ?: ($asset?->erms_id ?: 'your asset'));
        $when = $pickup->scheduled_for?->format('d M Y H:i');
        $message = "Olives BI Waste: Pickup for {$assetName} scheduled on {$when} was not completed. Please follow up or switch provider.";

        $this->notifyUserAndCompany($pickup, 'Waste pickup missed', $message);
    }

    private function notifyUserAndCompany(WastePickup $pickup, string $subject, string $message): void
    {
        $user = $pickup->booking?->user;
        $company = $pickup->company;
        $proOwner = $company?->registeredBy;

        foreach ([$user?->email, $company?->email] as $email) {
            if (blank($email)) {
                continue;
            }

            try {
                Mail::raw($message, fn ($mail) => $mail->to($email)->subject($subject));
            } catch (Throwable $exception) {
                report($exception);
            }
        }

        foreach ([$user?->phone, $company?->phone] as $phone) {
            if (blank($phone)) {
                continue;
            }

            try {
                if ($proOwner) {
                    app(ProSmsService::class)->sendForPro($proOwner, $phone, $message);
                } else {
                    SendSMS::sendSMS($phone, $message, $pickup->company?->assembly_code);
                }
            } catch (Throwable $exception) {
                report($exception);
            }
        }
    }
}

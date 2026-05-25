<?php

namespace App\Services;

use App\Models\WastePickup;
use App\Models\WastePickupBooking;
use Carbon\Carbon;
use Carbon\CarbonInterface;

class WastePickupScheduler
{
    public function scheduleFirst(WastePickupBooking $booking): WastePickup
    {
        $scheduledFor = Carbon::parse(
            $booking->scheduled_date->toDateString().' '.$booking->scheduled_time
        );

        $booking->update(['next_pickup_at' => $scheduledFor]);

        return WastePickup::firstOrCreate(
            [
                'waste_pickup_booking_uuid' => $booking->uuid,
                'scheduled_for' => $scheduledFor,
            ],
            [
                'waste_company_uuid' => $booking->waste_company_uuid,
                'status' => 'scheduled',
            ]
        );
    }

    public function scheduleNextAfter(WastePickupBooking $booking, CarbonInterface $after): ?WastePickup
    {
        if ($booking->status !== 'active') {
            return null;
        }

        $next = $this->nextDate($booking, Carbon::instance($after));

        if (! $next) {
            $booking->update(['next_pickup_at' => null]);

            return null;
        }

        $futurePickup = $booking->pickups()
            ->whereIn('status', ['scheduled', 'due'])
            ->where('scheduled_for', '>=', now())
            ->orderBy('scheduled_for')
            ->first();

        if ($futurePickup) {
            $booking->update(['next_pickup_at' => $futurePickup->scheduled_for]);

            return $futurePickup;
        }

        $booking->update(['next_pickup_at' => $next]);

        return WastePickup::create([
            'waste_pickup_booking_uuid' => $booking->uuid,
            'waste_company_uuid' => $booking->waste_company_uuid,
            'scheduled_for' => $next,
            'status' => 'scheduled',
        ]);
    }

    public function cancelFuturePickups(WastePickupBooking $booking): void
    {
        $booking->pickups()
            ->whereIn('status', ['scheduled', 'due'])
            ->where('scheduled_for', '>=', now())
            ->update(['status' => 'cancelled']);
    }

    private function nextDate(WastePickupBooking $booking, Carbon $after): ?Carbon
    {
        if ($booking->frequency === 'once') {
            return null;
        }

        $candidate = $after->copy();

        do {
            $candidate = match ($booking->frequency) {
                'daily' => $candidate->addDay(),
                'biweekly' => $candidate->addWeeks(2),
                'monthly' => $candidate->addMonthNoOverflow(),
                default => $candidate->addWeek(),
            };
        } while ($candidate->lessThanOrEqualTo(now()));

        return $candidate;
    }
}

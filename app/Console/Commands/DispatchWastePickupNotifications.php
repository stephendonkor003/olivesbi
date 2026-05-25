<?php

namespace App\Console\Commands;

use App\Models\WastePickup;
use App\Services\WastePickupNotifier;
use App\Services\WastePickupScheduler;
use Illuminate\Console\Command;

class DispatchWastePickupNotifications extends Command
{
    protected $signature = 'waste:dispatch-notifications';

    protected $description = 'Send due and missed waste pickup notifications.';

    public function handle(WastePickupNotifier $notifier, WastePickupScheduler $scheduler): int
    {
        $dueCount = 0;
        $missedCount = 0;

        WastePickup::with(['booking.user', 'booking.property', 'booking.business', 'company'])
            ->whereIn('status', ['scheduled', 'due'])
            ->whereNull('due_notified_at')
            ->whereBetween('scheduled_for', [now(), now()->addDay()])
            ->orderBy('scheduled_for')
            ->chunkById(100, function ($pickups) use ($notifier, &$dueCount) {
                foreach ($pickups as $pickup) {
                    $notifier->due($pickup);
                    $pickup->update([
                        'due_notified_at' => now(),
                        'status' => $pickup->scheduled_for->isPast() ? 'due' : $pickup->status,
                    ]);
                    $dueCount++;
                }
            });

        WastePickup::with(['booking.user', 'booking.property', 'booking.business', 'company'])
            ->whereIn('status', ['scheduled', 'due'])
            ->whereNull('completed_at')
            ->whereNull('missed_notified_at')
            ->where('scheduled_for', '<', now()->subHours(2))
            ->orderBy('scheduled_for')
            ->chunkById(100, function ($pickups) use ($notifier, $scheduler, &$missedCount) {
                foreach ($pickups as $pickup) {
                    $pickup->update([
                        'status' => 'missed',
                        'missed_notified_at' => now(),
                    ]);

                    $notifier->missed($pickup);
                    $scheduler->scheduleNextAfter($pickup->booking, $pickup->scheduled_for);
                    $missedCount++;
                }
            });

        $this->info("Waste notifications dispatched. Due: {$dueCount}; missed: {$missedCount}.");

        return self::SUCCESS;
    }
}

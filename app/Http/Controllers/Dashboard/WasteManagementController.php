<?php

namespace App\Http\Controllers\Dashboard;

use App\Http\Controllers\Controller;
use App\Models\WasteCompany;
use App\Models\WastePickup;
use App\Models\WastePickupBooking;
use App\Models\WasteProviderChange;
use App\Models\WasteServicePlan;
use App\Services\WastePickupNotifier;
use App\Services\WastePickupScheduler;
use App\Support\GlobalLocation;
use Illuminate\Http\Request;
use Illuminate\Validation\Rule;

class WasteManagementController extends Controller
{
    public function accountIndex(Request $request)
    {
        $user = $request->user();
        $accountData = app(AccountDashboardController::class)->accountHolderData($user);

        $companies = WasteCompany::with(['activePlans'])
            ->where('status', 'active')
            ->when($user->assembly_code, fn ($query) => $query->where(function ($query) use ($user) {
                $query->where('assembly_code', $user->assembly_code)->orWhereNull('assembly_code');
            }))
            ->orderBy('name')
            ->get();

        $plans = WasteServicePlan::with('company')
            ->where('active', true)
            ->whereHas('company', fn ($query) => $this->accountCompanyScope($query, $user))
            ->orderBy('name')
            ->get();

        $bookings = WastePickupBooking::with([
            'property',
            'business',
            'company',
            'plan',
            'pickups' => fn ($query) => $query->latest('scheduled_for')->limit(8),
            'providerChanges.toCompany',
        ])
            ->where('user_uuid', $user->uuid)
            ->latest()
            ->get();

        $pickups = WastePickup::with(['booking.property', 'booking.business', 'company'])
            ->whereHas('booking', fn ($query) => $query->where('user_uuid', $user->uuid))
            ->latest('scheduled_for')
            ->limit(24)
            ->get();

        $wasteStats = $this->wasteStats($user->uuid);

        return view('dashboard.tiers.partials.shell', [
            'accent' => '#2d9e4b',
            'tier' => 'Olives Account',
            'tierSlug' => 'account',
            'resolvedTier' => 'account',
            'scope' => 'Waste Pickup',
            'subtitle' => 'Book, monitor, and switch waste pickup providers for your assets.',
            'user' => $user,
            'profileRoute' => route('dashboard.olives-account.profile'),
            'sidebarNav' => $this->accountNav(),
            'metricKeys' => [
                'Providers' => 'providers',
                'Bookings' => 'bookings',
                'Upcoming' => 'upcoming',
                'Waste KG' => 'waste',
            ],
            'stats' => [
                'providers' => $companies->count(),
                'bookings' => $bookings->where('status', 'active')->count(),
                'upcoming' => $pickups->whereIn('status', ['scheduled', 'due'])->count(),
                'waste' => round($wasteStats['year'], 1),
            ],
            'quickLinks' => [
                ['label' => 'My Properties', 'url' => route('dashboard.olives-account.properties'), 'hint' => 'Property records'],
                ['label' => 'My Businesses', 'url' => route('dashboard.olives-account.businesses'), 'hint' => 'Business records'],
                ['label' => 'My Bills', 'url' => route('dashboard.olives-account.bills'), 'hint' => 'Rates and bills'],
                ['label' => 'Reports', 'url' => route('dashboard.olives-account.reports'), 'hint' => 'Bills, payments, waste, audit'],
                ['label' => 'Sign Out', 'url' => route('auth.logout'), 'hint' => 'End your session securely', 'danger' => true],
            ],
            'contentView' => 'dashboard.tiers.account.partials.waste',
            'contentWide' => true,
            'contentData' => compact('accountData', 'companies', 'plans', 'bookings', 'pickups', 'wasteStats'),
        ]);
    }

    public function storeBooking(Request $request, WastePickupScheduler $scheduler)
    {
        $data = $request->validate([
            'asset_key' => ['required', 'string'],
            'waste_service_plan_uuid' => ['required', 'uuid', 'exists:waste_service_plans,uuid'],
            'frequency' => ['required', Rule::in(['once', 'daily', 'weekly', 'biweekly', 'monthly'])],
            'scheduled_date' => ['required', 'date', 'after_or_equal:today'],
            'scheduled_time' => ['required', 'date_format:H:i'],
            'estimated_waste_kg' => ['nullable', 'numeric', 'min:0', 'max:999999'],
            'notes' => ['nullable', 'string', 'max:1000'],
        ]);

        [$propertyUuid, $businessUuid] = $this->assetUuidsForUser($request, $data['asset_key']);
        $plan = $this->accountPlanForUser($request, $data['waste_service_plan_uuid']);

        $booking = WastePickupBooking::create([
            'user_uuid' => $request->user()->uuid,
            'property_uuid' => $propertyUuid,
            'business_uuid' => $businessUuid,
            'waste_company_uuid' => $plan->waste_company_uuid,
            'waste_service_plan_uuid' => $plan->uuid,
            'frequency' => $data['frequency'],
            'scheduled_date' => $data['scheduled_date'],
            'scheduled_time' => $data['scheduled_time'],
            'estimated_waste_kg' => $data['estimated_waste_kg'] ?? null,
            'notes' => $data['notes'] ?? null,
            'status' => 'active',
        ]);

        $scheduler->scheduleFirst($booking);

        return back()->with('status', 'Waste pickup booking created.');
    }

    public function switchProvider(Request $request, WastePickupBooking $booking, WastePickupScheduler $scheduler)
    {
        $this->authorizeBooking($request, $booking);

        $data = $request->validate([
            'waste_service_plan_uuid' => ['required', 'uuid', 'exists:waste_service_plans,uuid'],
            'reason' => ['required', 'string', 'min:5', 'max:1000'],
            'scheduled_date' => ['required', 'date', 'after_or_equal:today'],
            'scheduled_time' => ['required', 'date_format:H:i'],
        ]);

        $plan = $this->accountPlanForUser($request, $data['waste_service_plan_uuid']);

        if ($plan->waste_company_uuid === $booking->waste_company_uuid) {
            return back()->with('error', 'Select a different waste service provider.');
        }

        $scheduler->cancelFuturePickups($booking);

        WasteProviderChange::create([
            'waste_pickup_booking_uuid' => $booking->uuid,
            'from_company_uuid' => $booking->waste_company_uuid,
            'to_company_uuid' => $plan->waste_company_uuid,
            'from_plan_uuid' => $booking->waste_service_plan_uuid,
            'to_plan_uuid' => $plan->uuid,
            'changed_by_uuid' => $request->user()->uuid,
            'reason' => $data['reason'],
            'changed_at' => now(),
        ]);

        $booking->update([
            'waste_company_uuid' => $plan->waste_company_uuid,
            'waste_service_plan_uuid' => $plan->uuid,
            'scheduled_date' => $data['scheduled_date'],
            'scheduled_time' => $data['scheduled_time'],
            'status' => 'active',
            'cancellation_reason' => $data['reason'],
            'cancelled_at' => null,
        ]);

        $scheduler->scheduleFirst($booking->refresh());

        return back()->with('status', 'Waste provider changed and next pickup scheduled.');
    }

    public function completePickup(Request $request, WastePickup $pickup, WastePickupScheduler $scheduler)
    {
        $this->authorizePickup($request, $pickup);

        $data = $request->validate([
            'waste_amount_kg' => ['required', 'numeric', 'min:0', 'max:999999'],
            'notes' => ['nullable', 'string', 'max:1000'],
        ]);

        $pickup->update([
            'status' => 'completed',
            'waste_amount_kg' => $data['waste_amount_kg'],
            'completed_at' => now(),
            'notes' => $data['notes'] ?? $pickup->notes,
        ]);

        $scheduler->scheduleNextAfter($pickup->booking, $pickup->scheduled_for);

        return back()->with('status', 'Pickup marked completed and waste amount recorded.');
    }

    public function reportMissed(Request $request, WastePickup $pickup, WastePickupNotifier $notifier, WastePickupScheduler $scheduler)
    {
        $this->authorizePickup($request, $pickup);

        $data = $request->validate([
            'notes' => ['nullable', 'string', 'max:1000'],
        ]);

        $pickup->update([
            'status' => 'missed',
            'missed_notified_at' => now(),
            'notes' => $data['notes'] ?? $pickup->notes,
        ]);

        $notifier->missed($pickup);
        $scheduler->scheduleNextAfter($pickup->booking, $pickup->scheduled_for);

        return back()->with('status', 'Missed pickup recorded and notifications sent.');
    }

    public function proIndex(Request $request)
    {
        $user = $request->user() ?: auth()->user();

        $companies = WasteCompany::with(['plans', 'bookings'])
            ->where(function ($query) use ($user) {
                if ($user?->assembly_code) {
                    $query->where('assembly_code', $user->assembly_code)
                        ->orWhere('registered_by_uuid', $user->uuid);

                    return;
                }

                $query->where('registered_by_uuid', $user?->uuid);
            })
            ->latest()
            ->get();

        return view('dashboard.tiers.partials.shell', [
            'accent' => '#d4a017',
            'tier' => 'Olives Pro',
            'tierSlug' => 'pro',
            'resolvedTier' => 'pro',
            'scope' => 'Waste Companies',
            'subtitle' => 'Register waste companies and publish service charges for account holders.',
            'user' => $user,
            'profileRoute' => route('dashboard.olives-pro.profile'),
            'sidebarNav' => $this->proNav(),
            'metricKeys' => [
                'Companies' => 'companies',
                'Service Plans' => 'plans',
                'Active Bookings' => 'bookings',
                'Suspended' => 'suspended',
            ],
            'stats' => [
                'companies' => $companies->count(),
                'plans' => $companies->sum(fn ($company) => $company->plans->count()),
                'bookings' => $companies->sum(fn ($company) => $company->bookings->where('status', 'active')->count()),
                'suspended' => $companies->where('status', 'suspended')->count(),
            ],
            'quickLinks' => [
                ['label' => 'Properties', 'url' => route('dashboard.olives-pro.properties'), 'hint' => 'Jurisdiction properties'],
                ['label' => 'Businesses', 'url' => route('dashboard.olives-pro.businesses'), 'hint' => 'Jurisdiction businesses'],
                ['label' => 'Reports', 'url' => route('dashboard.olives-pro.reports'), 'hint' => 'Summary reports'],
                ['label' => 'Check Locality', 'url' => route('locality.check'), 'hint' => 'Environmental context'],
                ['label' => 'Sign Out', 'url' => route('auth.logout'), 'hint' => 'End your session securely', 'danger' => true],
            ],
            'contentView' => 'dashboard.tiers.pro.partials.waste-companies',
            'contentData' => compact('companies'),
        ]);
    }

    public function storeCompany(Request $request)
    {
        $data = $request->validate([
            'name' => ['required', 'string', 'max:255'],
            'contact_person' => ['nullable', 'string', 'max:255'],
            'phone' => ['nullable', 'string', 'max:40'],
            'email' => ['nullable', 'email', 'max:255'],
            'service_area' => ['nullable', 'string', 'max:255'],
            'notes' => ['nullable', 'string', 'max:1000'],
            'status' => ['required', Rule::in(['active', 'suspended'])],
            ...GlobalLocation::rules(),
        ]);

        WasteCompany::create([
            ...$data,
            ...GlobalLocation::from($data, $data['address_line'] ?? null),
            'registered_by_uuid' => $request->user()->uuid,
            'assembly_code' => $request->user()->assembly_code,
        ]);

        return back()->with('status', 'Waste company registered.');
    }

    public function storePlan(Request $request, WasteCompany $company)
    {
        $this->authorizeCompanyForPro($request, $company);

        $data = $request->validate([
            'name' => ['required', 'string', 'max:255'],
            'waste_stream' => ['required', 'string', 'max:255'],
            'default_frequency' => ['required', Rule::in(['once', 'daily', 'weekly', 'biweekly', 'monthly'])],
            'service_charge' => ['required', 'numeric', 'min:0', 'max:999999999'],
            'currency_code' => ['required', 'string', 'size:3'],
            'currency_symbol' => ['nullable', 'string', 'max:12'],
            'charge_period' => ['required', 'string', 'max:80'],
            'description' => ['nullable', 'string', 'max:1000'],
        ]);

        $company->plans()->create([
            ...$data,
            'currency_code' => strtoupper($data['currency_code']),
            'currency_symbol' => $data['currency_symbol'] ?: strtoupper($data['currency_code']),
            'active' => true,
        ]);

        return back()->with('status', 'Waste service plan added.');
    }

    private function assetUuidsForUser(Request $request, string $assetKey): array
    {
        [$type, $uuid] = array_pad(explode(':', $assetKey, 2), 2, null);
        $accountData = app(AccountDashboardController::class)->accountHolderData($request->user());

        if ($type === 'property' && $accountData['properties']->contains('uuid', $uuid)) {
            return [$uuid, null];
        }

        if ($type === 'business' && $accountData['businesses']->contains('uuid', $uuid)) {
            return [null, $uuid];
        }

        abort(403, 'This asset is not linked to your account.');
    }

    private function accountPlanForUser(Request $request, string $planUuid): WasteServicePlan
    {
        return WasteServicePlan::with('company')
            ->where('uuid', $planUuid)
            ->where('active', true)
            ->whereHas('company', fn ($query) => $this->accountCompanyScope($query, $request->user()))
            ->firstOrFail();
    }

    private function accountCompanyScope($query, $user): void
    {
        $query->where('status', 'active')
            ->when($user->assembly_code, fn ($query) => $query->where(function ($query) use ($user) {
                $query->where('assembly_code', $user->assembly_code)->orWhereNull('assembly_code');
            }));
    }

    private function authorizeCompanyForPro(Request $request, WasteCompany $company): void
    {
        $user = $request->user();

        abort_unless(
            $company->registered_by_uuid === $user->uuid
                || ($user->assembly_code && $company->assembly_code === $user->assembly_code),
            403,
            'This waste company is not linked to your account.'
        );
    }

    private function authorizeBooking(Request $request, WastePickupBooking $booking): void
    {
        abort_unless($booking->user_uuid === $request->user()->uuid, 403, 'This booking is not linked to your account.');
    }

    private function authorizePickup(Request $request, WastePickup $pickup): void
    {
        $pickup->loadMissing('booking');
        $this->authorizeBooking($request, $pickup->booking);
    }

    private function wasteStats(string $userUuid): array
    {
        $base = WastePickup::whereHas('booking', fn ($query) => $query->where('user_uuid', $userUuid))
            ->where('status', 'completed');

        return [
            'day' => (float) (clone $base)->whereDate('completed_at', today())->sum('waste_amount_kg'),
            'week' => (float) (clone $base)->whereBetween('completed_at', [now()->startOfWeek(), now()->endOfWeek()])->sum('waste_amount_kg'),
            'month' => (float) (clone $base)->whereBetween('completed_at', [now()->startOfMonth(), now()->endOfMonth()])->sum('waste_amount_kg'),
            'year' => (float) (clone $base)->whereBetween('completed_at', [now()->startOfYear(), now()->endOfYear()])->sum('waste_amount_kg'),
        ];
    }

    private function accountNav(): array
    {
        return [
            ['icon' => 'bi-speedometer2', 'label' => 'Dashboard', 'url' => route('dashboard.olives-account')],
            ['icon' => 'bi-buildings', 'label' => 'My Properties', 'url' => route('dashboard.olives-account.properties')],
            ['icon' => 'bi-briefcase', 'label' => 'My Business', 'url' => route('dashboard.olives-account.businesses')],
            ['icon' => 'bi-receipt', 'label' => 'My Bills', 'url' => route('dashboard.olives-account.bills')],
            ['icon' => 'bi-recycle', 'label' => 'Waste Pickup', 'url' => route('dashboard.olives-account.waste'), 'active' => true],
            [
                'icon' => 'bi-file-earmark-bar-graph',
                'label' => 'Reports',
                'url' => route('dashboard.olives-account.reports'),
                'active' => request()->routeIs('dashboard.olives-account.reports*'),
                'children' => [
                    ['icon' => 'bi-grid-1x2', 'label' => 'Overview', 'url' => route('dashboard.olives-account.reports'), 'active' => request()->routeIs('dashboard.olives-account.reports')],
                    ['icon' => 'bi-receipt-cutoff', 'label' => 'Bill Report', 'url' => route('dashboard.olives-account.reports.bills'), 'active' => request()->routeIs('dashboard.olives-account.reports.bills')],
                    ['icon' => 'bi-credit-card-2-front', 'label' => 'Payment Report', 'url' => route('dashboard.olives-account.reports.payments'), 'active' => request()->routeIs('dashboard.olives-account.reports.payments')],
                    ['icon' => 'bi-recycle', 'label' => 'Waste Report', 'url' => route('dashboard.olives-account.reports.waste'), 'active' => request()->routeIs('dashboard.olives-account.reports.waste')],
                    ['icon' => 'bi-shield-check', 'label' => 'Audit Trail', 'url' => route('dashboard.olives-account.reports.audit'), 'active' => request()->routeIs('dashboard.olives-account.reports.audit')],
                ],
            ],
            ['icon' => 'bi-person-circle', 'label' => 'My Profile', 'url' => route('dashboard.olives-account.profile')],
            ['icon' => 'bi-box-arrow-left', 'label' => 'Sign Out', 'url' => route('auth.logout')],
        ];
    }

    private function proNav(): array
    {
        return [
            ['icon' => 'bi-speedometer2', 'label' => 'Dashboard', 'url' => route('dashboard.olives-pro')],
            ['icon' => 'bi-recycle', 'label' => 'Waste Companies', 'url' => route('dashboard.olives-pro.waste-companies'), 'active' => true],
            ['icon' => 'bi-buildings', 'label' => 'Properties', 'url' => route('dashboard.olives-pro.properties')],
            ['icon' => 'bi-briefcase', 'label' => 'Businesses', 'url' => route('dashboard.olives-pro.businesses')],
            ['icon' => 'bi-receipt', 'label' => 'Bills & Arrears', 'url' => route('dashboard.olives-pro.bills')],
            ['icon' => 'bi-cash-coin', 'label' => 'Payments', 'url' => route('dashboard.olives-pro.payments')],
            ['icon' => 'bi-bank', 'label' => 'Laws & Fees', 'url' => route('dashboard.olives-pro.laws-fees')],
            ['icon' => 'bi-people', 'label' => 'Citizens', 'url' => route('dashboard.olives-pro.citizens')],
            ['icon' => 'bi-person-gear', 'label' => 'Agents & Users', 'url' => route('dashboard.olives-pro.users')],
            ['icon' => 'bi-file-earmark-bar-graph', 'label' => 'Reports', 'url' => route('dashboard.olives-pro.reports')],
            ['icon' => 'bi-geo-alt', 'label' => 'Check Locality', 'url' => route('locality.check')],
            ['icon' => 'bi-person-circle', 'label' => 'My Profile', 'url' => route('dashboard.olives-pro.profile')],
            ['icon' => 'bi-box-arrow-left', 'label' => 'Sign Out', 'url' => route('auth.logout')],
        ];
    }
}

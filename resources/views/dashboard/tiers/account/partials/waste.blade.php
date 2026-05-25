@php
    $errors = $errors ?? new \Illuminate\Support\ViewErrorBag;
    $assetOptions = collect();

    foreach ($accountData['properties'] as $property) {
        $label = $property->account_no ?: ($property->erms_id ?: 'Property '.$property->uuid);
        $assetOptions->push(['key' => 'property:'.$property->uuid, 'label' => 'Property - '.$label]);
    }

    foreach ($accountData['businesses'] as $business) {
        $label = $business->business_name ?: ($business->account_no ?: 'Business '.$business->uuid);
        $assetOptions->push(['key' => 'business:'.$business->uuid, 'label' => 'Business - '.$label]);
    }

    $frequencyLabels = [
        'once' => 'One time',
        'daily' => 'Daily',
        'weekly' => 'Weekly',
        'biweekly' => 'Every two weeks',
        'monthly' => 'Monthly',
    ];

    $activeBookings = $bookings->where('status', 'active');
    $nextPickup = $pickups->whereIn('status', ['scheduled', 'due'])->sortBy('scheduled_for')->first();
    $completedPickups = $pickups->where('status', 'completed')->count();
    $openPickups = $pickups->whereIn('status', ['scheduled', 'due'])->count();

    $statusClass = fn ($status) => match ($status) {
        'completed' => 'good',
        'missed' => 'danger',
        'cancelled' => 'muted',
        'due' => 'warn',
        default => 'info',
    };
@endphp

<style>
    .waste-page { display:flex; flex-direction:column; gap:1rem; }
    .waste-alert { border:1px solid #fecaca; background:#fef2f2; color:#991b1b; border-radius:12px; padding:.85rem 1rem; font-weight:800; }
    .waste-alert strong { display:block; margin-bottom:.15rem; }
    .waste-topline { display:grid; grid-template-columns:repeat(4,minmax(0,1fr)); gap:.7rem; }
    .waste-stat {
        min-height:96px; border:1px solid var(--line); border-radius:14px; background:#fff;
        padding:.9rem; display:flex; flex-direction:column; justify-content:space-between;
        box-shadow:0 1px 8px rgba(26,92,42,.04);
    }
    .waste-stat-head { display:flex; align-items:center; justify-content:space-between; gap:.55rem; color:var(--muted); font-size:.68rem; font-weight:900; text-transform:uppercase; letter-spacing:.45px; }
    .waste-stat-icon { width:32px; height:32px; border-radius:10px; display:grid; place-items:center; background:var(--soft); color:var(--primary); font-size:1rem; }
    .waste-stat-value { font-family:'Poppins',sans-serif; font-size:1.25rem; font-weight:800; color:#17251b; line-height:1.1; }
    .waste-stat-foot { color:var(--muted); font-size:.72rem; margin-top:.2rem; }
    .waste-board { display:grid; grid-template-columns:minmax(0,1.05fr) minmax(260px,.95fr); gap:1rem; align-items:start; }
    .waste-panel { background:#fff; border:1px solid var(--line); border-radius:14px; padding:1rem; box-shadow:0 1px 8px rgba(26,92,42,.04); }
    .waste-panel-head { display:flex; justify-content:space-between; gap:.8rem; align-items:flex-start; margin-bottom:.9rem; }
    .waste-title { font-family:'Poppins',sans-serif; font-weight:800; color:var(--primary); font-size:.92rem; display:flex; gap:.45rem; align-items:center; }
    .waste-sub { color:var(--muted); font-size:.77rem; line-height:1.45; margin-top:.2rem; }
    .waste-chip { display:inline-flex; align-items:center; gap:.35rem; border-radius:999px; background:var(--soft); color:var(--primary); padding:.22rem .55rem; font-size:.68rem; font-weight:900; text-transform:uppercase; white-space:nowrap; }
    .waste-form { display:grid; grid-template-columns:1fr; gap:.75rem; }
    .waste-form .wide { grid-column:auto; }
    .waste-field label { display:block; font-size:.67rem; font-weight:900; text-transform:uppercase; letter-spacing:.45px; color:var(--muted); margin-bottom:.3rem; }
    .waste-input, .waste-select, .waste-textarea {
        width:100%; border:1px solid var(--line); border-radius:10px; padding:.68rem .75rem;
        color:#17251b; background:#fff; font:inherit; outline:0; min-height:42px;
    }
    .waste-input:focus, .waste-select:focus, .waste-textarea:focus { border-color:rgba(45,158,75,.55); box-shadow:0 0 0 3px rgba(45,158,75,.12); }
    .waste-textarea { min-height:86px; resize:vertical; }
    .waste-actions { display:flex; flex-wrap:wrap; gap:.45rem; align-items:center; }
    .waste-btn {
        display:inline-flex; align-items:center; justify-content:center; gap:.42rem; border-radius:9px;
        border:1px solid var(--line); background:var(--soft); color:var(--primary);
        font-size:.78rem; font-weight:900; padding:.6rem .8rem; text-decoration:none; cursor:pointer; min-height:38px;
    }
    .waste-btn.primary { background:var(--accent); border-color:var(--accent); color:#fff; }
    .waste-btn.warn { background:#fff7ed; border-color:#fed7aa; color:#92400e; }
    .waste-btn.ghost { background:#fff; }
    .provider-list { display:grid; gap:.65rem; }
    .provider-card { border:1px solid var(--line); border-radius:12px; padding:.85rem; background:#fbfefc; }
    .provider-top { display:flex; justify-content:space-between; gap:.7rem; align-items:flex-start; }
    .provider-name { font-weight:900; color:#17251b; line-height:1.25; }
    .provider-meta { color:var(--muted); font-size:.76rem; line-height:1.45; margin-top:.25rem; overflow-wrap:anywhere; }
    .plan-row { display:flex; gap:.35rem; flex-wrap:wrap; margin-top:.65rem; }
    .price-pill { display:inline-flex; border-radius:999px; padding:.22rem .55rem; background:#eef8f1; color:var(--primary); font-size:.68rem; font-weight:900; }
    .booking-stack, .pickup-stack { display:grid; gap:.75rem; }
    .booking-card, .pickup-card { border:1px solid var(--line); border-radius:13px; background:#fff; padding:.9rem; }
    .booking-main { display:grid; grid-template-columns:minmax(0,1fr) auto; gap:.7rem; align-items:start; }
    .asset-name { font-weight:900; color:#17251b; overflow-wrap:anywhere; }
    .booking-meta { color:var(--muted); font-size:.76rem; line-height:1.45; margin-top:.2rem; }
    .booking-detail-grid { display:grid; grid-template-columns:repeat(3,minmax(0,1fr)); gap:.55rem; margin-top:.8rem; }
    .detail-box { background:#f7fbf8; border:1px solid var(--line); border-radius:10px; padding:.62rem; }
    .detail-label { color:var(--muted); font-size:.62rem; font-weight:900; text-transform:uppercase; letter-spacing:.4px; margin-bottom:.18rem; }
    .detail-value { color:#17251b; font-size:.78rem; font-weight:800; line-height:1.35; }
    .status-pill { display:inline-flex; align-items:center; border-radius:999px; padding:.22rem .58rem; font-size:.67rem; font-weight:900; text-transform:uppercase; white-space:nowrap; }
    .status-pill.good { background:#dcfce7; color:#166534; }
    .status-pill.warn { background:#fff7ed; color:#92400e; }
    .status-pill.danger { background:#fee2e2; color:#991b1b; }
    .status-pill.muted { background:#f3f4f6; color:#4b5563; }
    .status-pill.info { background:#eef8f1; color:var(--primary); }
    .switch-box { margin-top:.85rem; border-top:1px solid var(--line); padding-top:.75rem; }
    .switch-box summary { cursor:pointer; color:#92400e; font-weight:900; font-size:.78rem; display:inline-flex; gap:.35rem; align-items:center; }
    .switch-box[open] summary { margin-bottom:.65rem; }
    .switch-form { display:grid; grid-template-columns:1fr 1fr; gap:.5rem; }
    .switch-form .wide { grid-column:1 / -1; }
    .pickup-card { display:grid; grid-template-columns:minmax(0,1fr) auto; gap:.75rem; align-items:start; }
    .pickup-when { font-weight:900; color:#17251b; }
    .pickup-provider { color:var(--muted); font-size:.76rem; margin-top:.18rem; }
    .pickup-action-grid { display:grid; grid-template-columns:110px auto; gap:.4rem; align-items:start; }
    .pickup-action-grid .wide { grid-column:1 / -1; }
    .empty-state { border:1px dashed rgba(26,92,42,.22); border-radius:13px; padding:1rem; color:var(--muted); background:#fbfefc; font-size:.82rem; line-height:1.5; }
    @media (max-width:1280px) {
        .waste-board, .booking-detail-grid { grid-template-columns:1fr; }
    }
    @media (max-width:900px) {
        .waste-topline { grid-template-columns:repeat(2,minmax(0,1fr)); }
        .booking-main, .pickup-card, .switch-form, .pickup-action-grid { grid-template-columns:1fr; }
        .switch-form .wide, .pickup-action-grid .wide { grid-column:auto; }
    }
    @media (max-width:520px) {
        .waste-topline { grid-template-columns:1fr; }
        .waste-panel-head, .provider-top { flex-direction:column; }
        .waste-actions, .waste-btn { width:100%; }
    }
</style>

<div class="waste-page">
    @if (session('error'))
        <div class="waste-alert">{{ session('error') }}</div>
    @endif

    @if ($errors->any())
        <div class="waste-alert">
            <strong>Check the form:</strong>
            {{ $errors->first() }}
        </div>
    @endif

    <div class="waste-topline">
        <div class="waste-stat">
            <div class="waste-stat-head"><span>Today</span><span class="waste-stat-icon"><i class="bi bi-calendar-day"></i></span></div>
            <div><div class="waste-stat-value">{{ number_format($wasteStats['day'], 2) }} kg</div><div class="waste-stat-foot">Completed pickups</div></div>
        </div>
        <div class="waste-stat">
            <div class="waste-stat-head"><span>This Week</span><span class="waste-stat-icon"><i class="bi bi-calendar-week"></i></span></div>
            <div><div class="waste-stat-value">{{ number_format($wasteStats['week'], 2) }} kg</div><div class="waste-stat-foot">{{ $completedPickups }} recent completed</div></div>
        </div>
        <div class="waste-stat">
            <div class="waste-stat-head"><span>This Month</span><span class="waste-stat-icon"><i class="bi bi-bar-chart"></i></span></div>
            <div><div class="waste-stat-value">{{ number_format($wasteStats['month'], 2) }} kg</div><div class="waste-stat-foot">Measured waste</div></div>
        </div>
        <div class="waste-stat">
            <div class="waste-stat-head"><span>Next Pickup</span><span class="waste-stat-icon"><i class="bi bi-truck"></i></span></div>
            <div><div class="waste-stat-value" style="font-size:1rem;">{{ $nextPickup?->scheduled_for?->format('d M, H:i') ?: 'None' }}</div><div class="waste-stat-foot">{{ $openPickups }} open pickup{{ $openPickups === 1 ? '' : 's' }}</div></div>
        </div>
    </div>

    <div class="waste-board">
        <section class="waste-panel">
            <div class="waste-panel-head">
                <div>
                    <div class="waste-title"><i class="bi bi-calendar-check" style="color:var(--accent);"></i> Book Waste Pickup</div>
                    <div class="waste-sub">{{ $assetOptions->count() }} linked asset{{ $assetOptions->count() === 1 ? '' : 's' }} available for pickup.</div>
                </div>
                <span class="waste-chip"><i class="bi bi-building-check"></i> {{ $companies->count() }} provider{{ $companies->count() === 1 ? '' : 's' }}</span>
            </div>

            <form method="POST" action="{{ route('dashboard.olives-account.waste.book') }}" class="waste-form">
                @csrf
                <div class="waste-field wide">
                    <label>Property or Business</label>
                    <select class="waste-select" name="asset_key" required>
                        <option value="">Select asset</option>
                        @foreach ($assetOptions as $asset)
                            <option value="{{ $asset['key'] }}" @selected(old('asset_key') === $asset['key'])>{{ $asset['label'] }}</option>
                        @endforeach
                    </select>
                </div>
                <div class="waste-field wide">
                    <label>Service Provider & Charge</label>
                    <select class="waste-select" name="waste_service_plan_uuid" required>
                        <option value="">Select service</option>
                        @foreach ($plans as $plan)
                            <option value="{{ $plan->uuid }}" @selected(old('waste_service_plan_uuid') === $plan->uuid)>
                                {{ $plan->company->name }} - {{ $plan->name }} ({{ $plan->currency_symbol }} {{ number_format((float) $plan->service_charge, 2) }} {{ $plan->charge_period }})
                            </option>
                        @endforeach
                    </select>
                </div>
                <div class="waste-field">
                    <label>Frequency</label>
                    <select class="waste-select" name="frequency" required>
                        @foreach ($frequencyLabels as $value => $label)
                            <option value="{{ $value }}" @selected(old('frequency', 'weekly') === $value)>{{ $label }}</option>
                        @endforeach
                    </select>
                </div>
                <div class="waste-field">
                    <label>Estimated Waste KG</label>
                    <input class="waste-input" type="number" step="0.01" min="0" name="estimated_waste_kg" value="{{ old('estimated_waste_kg') }}" placeholder="12.5">
                </div>
                <div class="waste-field">
                    <label>Pickup Date</label>
                    <input class="waste-input" type="date" name="scheduled_date" min="{{ now()->toDateString() }}" value="{{ old('scheduled_date', now()->addDay()->toDateString()) }}" required>
                </div>
                <div class="waste-field">
                    <label>Pickup Time</label>
                    <input class="waste-input" type="time" name="scheduled_time" value="{{ old('scheduled_time', '08:00') }}" required>
                </div>
                <div class="waste-field wide">
                    <label>Notes</label>
                    <textarea class="waste-textarea" name="notes" placeholder="Gate directions, pickup point, bin type">{{ old('notes') }}</textarea>
                </div>
                <div class="waste-actions wide">
                    <button class="waste-btn primary" type="submit"><i class="bi bi-calendar-plus"></i> Book pickup</button>
                </div>
            </form>
        </section>

        <section class="waste-panel">
            <div class="waste-panel-head">
                <div>
                    <div class="waste-title"><i class="bi bi-recycle" style="color:var(--accent);"></i> Available Providers</div>
                    <div class="waste-sub">Registered waste companies and their active service charges.</div>
                </div>
            </div>

            <div class="provider-list">
                @forelse ($companies as $company)
                    <article class="provider-card">
                        <div class="provider-top">
                            <div>
                                <div class="provider-name">{{ $company->name }}</div>
                                <div class="provider-meta">
                                    {{ $company->service_area ?: 'Service area not specified' }}<br>
                                    {{ $company->phone ?: 'No phone' }} @if($company->email) &middot; {{ $company->email }} @endif
                                </div>
                            </div>
                            <span class="status-pill good">{{ $company->status }}</span>
                        </div>
                        <div class="plan-row">
                            @forelse ($company->activePlans as $plan)
                                <span class="price-pill">{{ $plan->currency_symbol }} {{ number_format((float) $plan->service_charge, 2) }} {{ $plan->charge_period }}</span>
                            @empty
                                <span class="provider-meta">No active plans</span>
                            @endforelse
                        </div>
                    </article>
                @empty
                    <div class="empty-state">No active waste companies have been registered yet.</div>
                @endforelse
            </div>
        </section>
    </div>

    <section class="waste-panel">
        <div class="waste-panel-head">
            <div>
                <div class="waste-title"><i class="bi bi-calendar2-week" style="color:var(--accent);"></i> Active Bookings</div>
                <div class="waste-sub">{{ $activeBookings->count() }} active booking{{ $activeBookings->count() === 1 ? '' : 's' }} for your linked properties and businesses.</div>
            </div>
        </div>

        <div class="booking-stack">
            @forelse ($bookings as $booking)
                @php
                    $asset = $booking->property ?: $booking->business;
                    $assetName = $asset?->account_no ?: ($asset?->business_name ?: ($asset?->erms_id ?: 'Asset'));
                    $otherPlans = $plans->where('waste_company_uuid', '!==', $booking->waste_company_uuid);
                @endphp
                <article class="booking-card">
                    <div class="booking-main">
                        <div>
                            <div class="asset-name">{{ $assetName }}</div>
                            <div class="booking-meta">{{ $booking->company->name ?? 'N/A' }} &middot; {{ $booking->plan->name ?? 'N/A' }}</div>
                        </div>
                        <span class="status-pill {{ $booking->status === 'active' ? 'good' : 'muted' }}">{{ $booking->status }}</span>
                    </div>

                    <div class="booking-detail-grid">
                        <div class="detail-box">
                            <div class="detail-label">Next Pickup</div>
                            <div class="detail-value">{{ $booking->next_pickup_at?->format('d M Y H:i') ?: 'No upcoming pickup' }}</div>
                        </div>
                        <div class="detail-box">
                            <div class="detail-label">Frequency</div>
                            <div class="detail-value">{{ $frequencyLabels[$booking->frequency] ?? ucfirst($booking->frequency) }}</div>
                        </div>
                        <div class="detail-box">
                            <div class="detail-label">Estimated Waste</div>
                            <div class="detail-value">{{ $booking->estimated_waste_kg ? number_format((float) $booking->estimated_waste_kg, 2).' kg' : 'Not recorded' }}</div>
                        </div>
                    </div>

                    @if ($booking->status === 'active')
                        <details class="switch-box">
                            <summary><i class="bi bi-arrow-left-right"></i> Switch provider</summary>
                            <form method="POST" action="{{ route('dashboard.olives-account.waste.bookings.switch-provider', $booking) }}" class="switch-form">
                                @csrf
                                <select class="waste-select wide" name="waste_service_plan_uuid" required>
                                    <option value="">New provider and plan</option>
                                    @foreach ($otherPlans as $plan)
                                        <option value="{{ $plan->uuid }}">{{ $plan->company->name }} - {{ $plan->name }}</option>
                                    @endforeach
                                </select>
                                <input class="waste-input" type="date" name="scheduled_date" min="{{ now()->toDateString() }}" value="{{ now()->addDay()->toDateString() }}" required>
                                <input class="waste-input" type="time" name="scheduled_time" value="08:00" required>
                                <textarea class="waste-textarea wide" name="reason" placeholder="Reason for changing provider" required></textarea>
                                <button class="waste-btn warn wide" type="submit"><i class="bi bi-check2-circle"></i> Confirm switch</button>
                            </form>
                        </details>
                    @endif
                </article>
            @empty
                <div class="empty-state">No waste pickup bookings yet.</div>
            @endforelse
        </div>
    </section>

    <section class="waste-panel">
        <div class="waste-panel-head">
            <div>
                <div class="waste-title"><i class="bi bi-clock-history" style="color:var(--accent);"></i> Pickup Tracking</div>
                <div class="waste-sub">Recent scheduled, completed, missed, and cancelled pickup records.</div>
            </div>
        </div>

        <div class="pickup-stack">
            @forelse ($pickups as $pickup)
                <article class="pickup-card">
                    <div>
                        <div class="pickup-when">{{ $pickup->scheduled_for?->format('d M Y H:i') }}</div>
                        <div class="pickup-provider">{{ $pickup->company->name ?? 'N/A' }} &middot; {{ $pickup->waste_amount_kg ? number_format((float) $pickup->waste_amount_kg, 2).' kg recorded' : 'Waste amount pending' }}</div>
                    </div>
                    <div style="min-width:230px;">
                        <div style="display:flex;justify-content:flex-end;margin-bottom:.5rem;">
                            <span class="status-pill {{ $statusClass($pickup->status) }}">{{ $pickup->status }}</span>
                        </div>
                        @if (! in_array($pickup->status, ['completed', 'cancelled'], true))
                            <form method="POST" action="{{ route('dashboard.olives-account.waste.pickups.complete', $pickup) }}" class="pickup-action-grid" style="margin-bottom:.4rem;">
                                @csrf
                                <input class="waste-input" type="number" step="0.01" min="0" name="waste_amount_kg" placeholder="KG" required>
                                <button class="waste-btn primary" type="submit"><i class="bi bi-check2"></i> Complete</button>
                            </form>
                            <form method="POST" action="{{ route('dashboard.olives-account.waste.pickups.missed', $pickup) }}" class="pickup-action-grid">
                                @csrf
                                <input class="waste-input wide" name="notes" placeholder="Reason missed">
                                <button class="waste-btn warn wide" type="submit"><i class="bi bi-exclamation-triangle"></i> Report missed</button>
                            </form>
                        @else
                            <div class="waste-sub" style="text-align:right;">No action needed</div>
                        @endif
                    </div>
                </article>
            @empty
                <div class="empty-state">No pickup records yet.</div>
            @endforelse
        </div>
    </section>
</div>

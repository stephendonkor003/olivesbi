@php
    $section = $section ?? 'overview';
    $errors = $errors ?? new \Illuminate\Support\ViewErrorBag;
    $proOwner = $proData['proOwner'] ?? $user;
    $isProAgent = filled($user?->pro_owner_uuid);
    $agentCapabilities = collect($user?->agent_capabilities ?: []);
    $canRegisterProperty = ! $isProAgent || $agentCapabilities->contains('property_registration');
    $canValidateProperty = ! $isProAgent || $agentCapabilities->contains('property_validation');
    $canRegisterBusiness = ! $isProAgent || $agentCapabilities->contains('business_registration');
    $canUpdateBusiness = ! $isProAgent || $agentCapabilities->contains('business_update');
    $assembly = $proData['assembly'] ?? null;
    $assemblyCode = $proData['assemblyCode'] ?? null;
    $properties = $proData['properties'] ?? collect();
    $businesses = $proData['businesses'] ?? collect();
    $bills = $proData['bills'] ?? collect();
    $payments = $proData['payments'] ?? collect();
    $citizens = $proData['citizens'] ?? collect();
    $users = $proData['users'] ?? collect();
    $agents = $proData['agents'] ?? collect();
    $revenueStreams = $proData['revenueStreams'] ?? collect();
    $wasteCompanies = $proData['wasteCompanies'] ?? collect();
    $wasteServicePlans = $proData['wasteServicePlans'] ?? collect();
    $integrations = $proData['integrations'] ?? collect();
    $paymentRequests = $proData['paymentRequests'] ?? collect();
    $successfulPayments = $payments->filter(fn ($payment) => strtolower((string) $payment->transaction_status) === 'success');
    $money = fn ($amount, $symbol = null) => trim(($symbol ?: optional($assembly)->currency_symbol ?: optional($assembly)->currency_code ?: 'GHS').' '.number_format((float) $amount, 2));
    $billTotal = $bills->sum(fn ($bill) => (float) $bill->amount + (float) $bill->arrears - (float) $bill->discount_amount);
    $paidTotal = $successfulPayments->sum(fn ($payment) => (float) $payment->amount);
    $statusClass = fn ($status) => match (strtolower((string) $status)) {
        'active', 'success', 'paid', 'completed', 'rated' => 'good',
        'failed', 'inactive', 'suspended' => 'danger',
        'pending', 'not rated' => 'warn',
        default => 'muted',
    };
    $paymentIntegrations = $integrations->where('category', 'payment')->where('is_active', true);
    $smsIntegrations = $integrations->where('category', 'sms')->where('is_active', true);
    $propertyRatePreview = session('property_rate_preview');
    $agentCapabilityLabels = [
        'business_registration' => 'Business registration',
        'business_update' => 'Business updates',
        'property_registration' => 'Property registration',
        'property_validation' => 'Property validation',
        'payment_collection' => 'Bill payment collection',
        'waste_payment_collection' => 'Waste service payment collection',
    ];
    $paymentProviderNames = \App\Models\ProIntegration::PAYMENT_PROVIDERS;
    $smsProviderNames = \App\Models\ProIntegration::SMS_PROVIDERS;
@endphp

<style>
    .pro-workspace { display:flex; flex-direction:column; gap:1rem; }
    .pro-alert { border:1px solid #fed7aa; background:#fff7ed; color:#92400e; border-radius:12px; padding:.85rem 1rem; font-weight:800; }
    .pro-summary { display:grid; grid-template-columns:repeat(4,minmax(0,1fr)); gap:.7rem; }
    .pro-stat { border:1px solid var(--line); border-radius:13px; background:#fff; padding:.85rem; box-shadow:0 1px 8px rgba(26,92,42,.04); }
    .pro-stat-label { color:var(--muted); font-size:.64rem; font-weight:900; text-transform:uppercase; letter-spacing:.45px; margin-bottom:.25rem; }
    .pro-stat-value { font-family:'Poppins',sans-serif; font-weight:800; color:#17251b; font-size:1.05rem; line-height:1.25; }
    .pro-panel { background:#fff; border:1px solid var(--line); border-radius:14px; padding:1rem; box-shadow:0 1px 8px rgba(26,92,42,.04); }
    .pro-head { display:flex; justify-content:space-between; align-items:flex-start; gap:1rem; margin-bottom:1rem; }
    .pro-title { font-family:'Poppins',sans-serif; font-weight:800; color:var(--primary); display:flex; align-items:center; gap:.45rem; }
    .pro-sub { color:var(--muted); font-size:.77rem; line-height:1.45; margin-top:.2rem; }
    .pro-actions { display:flex; gap:.45rem; flex-wrap:wrap; justify-content:flex-end; }
    .pro-btn { display:inline-flex; align-items:center; justify-content:center; gap:.35rem; border:1px solid var(--line); border-radius:9px; padding:.5rem .7rem; color:var(--primary); background:var(--soft); text-decoration:none; font-weight:900; font-size:.75rem; }
    .pro-grid { display:grid; grid-template-columns:repeat(3,minmax(0,1fr)); gap:.75rem; }
    .pro-card { border:1px solid var(--line); border-radius:12px; background:#fbfefc; padding:.85rem; min-height:112px; }
    .pro-card-title { font-family:'Poppins',sans-serif; font-weight:800; color:#17251b; }
    .pro-card-meta { color:var(--muted); font-size:.76rem; line-height:1.45; margin-top:.25rem; overflow-wrap:anywhere; }
    .pro-table-wrap { overflow:auto; }
    .pro-table { width:100%; border-collapse:collapse; min-width:820px; }
    .pro-table th, .pro-table td { border-bottom:1px solid var(--line); padding:.72rem .55rem; text-align:left; vertical-align:top; font-size:.8rem; }
    .pro-table th { color:var(--primary); font-size:.66rem; font-weight:900; text-transform:uppercase; letter-spacing:.45px; background:#fbfefc; }
    .pro-muted { color:var(--muted); font-size:.74rem; line-height:1.4; }
    .pro-pill { display:inline-flex; border-radius:999px; padding:.2rem .55rem; font-size:.66rem; font-weight:900; text-transform:uppercase; white-space:nowrap; }
    .pro-pill.good { background:#dcfce7; color:#166534; }
    .pro-pill.warn { background:#fff7ed; color:#92400e; }
    .pro-pill.danger { background:#fee2e2; color:#991b1b; }
    .pro-pill.muted { background:#f3f4f6; color:#4b5563; }
    .empty-pro { border:1px dashed rgba(26,92,42,.22); border-radius:12px; padding:1.25rem; text-align:center; color:var(--muted); background:#fbfefc; }
    .pro-form { display:grid; grid-template-columns:repeat(2,minmax(0,1fr)); gap:.75rem; }
    .pro-field.wide { grid-column:1 / -1; }
    .pro-label { display:block; font-size:.67rem; font-weight:900; text-transform:uppercase; letter-spacing:.45px; color:var(--muted); margin-bottom:.3rem; }
    .pro-input, .pro-select, .pro-textarea { width:100%; border:1px solid var(--line); border-radius:10px; padding:.68rem .75rem; color:#17251b; background:#fff; font:inherit; outline:0; }
    .pro-textarea { min-height:80px; resize:vertical; }
    .pro-check-grid { display:grid; grid-template-columns:repeat(2,minmax(0,1fr)); gap:.45rem; }
    .pro-check { display:flex; align-items:center; gap:.45rem; border:1px solid var(--line); border-radius:10px; background:#fbfefc; padding:.55rem .65rem; font-size:.78rem; font-weight:800; color:#17251b; }
    .pro-notice { border:1px solid #bbf7d0; background:#f0fdf4; color:#166534; border-radius:12px; padding:.8rem 1rem; font-weight:800; }
    .pro-notice.error { border-color:#fecaca; background:#fef2f2; color:#991b1b; }
    @media (max-width:1100px) { .pro-summary, .pro-grid { grid-template-columns:repeat(2,minmax(0,1fr)); } }
    @media (max-width:640px) { .pro-summary, .pro-grid, .pro-form, .pro-check-grid { grid-template-columns:1fr; } .pro-field.wide { grid-column:auto; } .pro-head { flex-direction:column; } .pro-actions, .pro-btn { width:100%; } }
</style>

<div class="pro-workspace">
    @if (session('status'))
        <div class="pro-notice">{{ session('status') }}</div>
    @endif

    @if ($errors->any())
        <div class="pro-notice error">{{ $errors->first() }}</div>
    @endif

    @if (! $assemblyCode)
        <div class="pro-alert">
            This Olives Pro account is not linked to an assembly yet. Assign an assembly code to unlock jurisdiction data.
        </div>
    @endif

    <div class="pro-summary">
        <div class="pro-stat"><div class="pro-stat-label">Assembly</div><div class="pro-stat-value">{{ $assembly->name ?? ($assemblyCode ?: 'Not assigned') }}</div></div>
        <div class="pro-stat"><div class="pro-stat-label">Bills Due</div><div class="pro-stat-value">{{ $money($billTotal) }}</div></div>
        <div class="pro-stat"><div class="pro-stat-label">Payments</div><div class="pro-stat-value">{{ $money($paidTotal) }}</div></div>
        <div class="pro-stat"><div class="pro-stat-label">Waste Companies</div><div class="pro-stat-value">{{ number_format($wasteCompanies->count()) }}</div></div>
    </div>

    @if ($section === 'properties')
        <section class="pro-panel">
            <div class="pro-head"><div><div class="pro-title"><i class="bi bi-buildings" style="color:var(--accent);"></i> Properties</div><div class="pro-sub">Register, view, and validate property records in your jurisdiction.</div></div></div>

            @if ($canRegisterProperty)
                <form method="POST" action="{{ route('dashboard.olives-pro.properties.store') }}" class="pro-form" style="margin-bottom:1rem;">
                    @csrf
                    <div class="pro-field"><label class="pro-label">Property Account</label><input class="pro-input" name="account_no" value="{{ old('account_no') }}" placeholder="Auto-generated if blank"></div>
                    <div class="pro-field"><label class="pro-label">Digital Address</label><input class="pro-input" name="digitaladdress" value="{{ old('digitaladdress') }}"></div>
                    <div class="pro-field wide"><label class="pro-label">Address</label><input class="pro-input" name="address_line" value="{{ old('address_line') }}"></div>
                    <div class="pro-field"><label class="pro-label">City / Town</label><input class="pro-input" name="city" value="{{ old('city') }}"></div>
                    <div class="pro-field"><label class="pro-label">District / County</label><input class="pro-input" name="district" value="{{ old('district') }}"></div>
                    <div class="pro-field"><label class="pro-label">Country</label><input class="pro-input" name="country" value="{{ old('country', optional($assembly)->country ?: 'Ghana') }}"></div>
                    <div class="pro-field"><label class="pro-label">Rateable Value</label><input class="pro-input" type="number" step="0.01" min="0" name="rateable_value" value="{{ old('rateable_value') }}"></div>
                    <div class="pro-field wide"><button class="pro-btn" type="submit" style="background:var(--accent);border-color:var(--accent);color:#fff;"><i class="bi bi-building-add"></i> Register property</button></div>
                </form>
            @endif

            <div class="pro-table-wrap">
                <table class="pro-table">
                    <thead><tr><th>Account</th><th>Address</th><th>Location</th><th>Rateable Value</th><th>Status</th><th>Validate</th></tr></thead>
                    <tbody>
                        @forelse ($properties as $property)
                            <tr>
                                <td><strong>{{ $property->account_no ?: $property->erms_id }}</strong><div class="pro-muted">{{ $property->uuid }}</div></td>
                                <td>{{ $property->address_line ?: ($property->digitaladdress ?: 'No address') }}</td>
                                <td>{{ collect([$property->city, $property->district, $property->country])->filter()->implode(', ') ?: 'N/A' }}</td>
                                <td>{{ $money($property->rateable_value) }}</td>
                                <td><span class="pro-pill {{ $property->is_rated ? 'good' : 'warn' }}">{{ $property->is_rated ? 'Rated' : 'Not Rated' }}</span></td>
                                <td>
                                    @if ($canValidateProperty)
                                        <form method="POST" action="{{ route('dashboard.olives-pro.properties.validate', $property) }}" style="display:flex;gap:.35rem;align-items:center;">
                                            @csrf
                                            <input class="pro-input" style="max-width:120px;padding:.45rem .5rem;" type="number" step="0.01" min="0" name="rateable_value" value="{{ $property->rateable_value }}">
                                            <button class="pro-btn" type="submit" style="padding:.45rem .55rem;"><i class="bi bi-check2-circle"></i></button>
                                        </form>
                                    @else
                                        <span class="pro-muted">Not assigned</span>
                                    @endif
                                </td>
                            </tr>
                        @empty
                            <tr><td colspan="6"><div class="empty-pro">No property records found for this Pro account.</div></td></tr>
                        @endforelse
                    </tbody>
                </table>
            </div>
        </section>
    @endif

    @if ($section === 'businesses')
        <section class="pro-panel">
            <div class="pro-head"><div><div class="pro-title"><i class="bi bi-briefcase" style="color:var(--accent);"></i> Businesses</div><div class="pro-sub">Register and update business records linked to your jurisdiction.</div></div></div>

            @if ($canRegisterBusiness)
                <form method="POST" action="{{ route('dashboard.olives-pro.businesses.store') }}" class="pro-form" style="margin-bottom:1rem;">
                    @csrf
                    <div class="pro-field"><label class="pro-label">Business Account</label><input class="pro-input" name="account_no" value="{{ old('account_no') }}" placeholder="Auto-generated if blank"></div>
                    <div class="pro-field"><label class="pro-label">Business Name</label><input class="pro-input" name="business_name" value="{{ old('business_name') }}" required></div>
                    <div class="pro-field"><label class="pro-label">Registration No.</label><input class="pro-input" name="registration_no" value="{{ old('registration_no') }}"></div>
                    <div class="pro-field"><label class="pro-label">Permit No.</label><input class="pro-input" name="permit_no" value="{{ old('permit_no') }}"></div>
                    <div class="pro-field"><label class="pro-label">Phone</label><input class="pro-input" name="phone_no" value="{{ old('phone_no') }}"></div>
                    <div class="pro-field"><label class="pro-label">Email</label><input class="pro-input" type="email" name="email" value="{{ old('email') }}"></div>
                    <div class="pro-field wide"><label class="pro-label">Address</label><input class="pro-input" name="address_line" value="{{ old('address_line') }}"></div>
                    <div class="pro-field"><label class="pro-label">City / Town</label><input class="pro-input" name="city" value="{{ old('city') }}"></div>
                    <div class="pro-field"><label class="pro-label">Country</label><input class="pro-input" name="country" value="{{ old('country', optional($assembly)->country ?: 'Ghana') }}"></div>
                    <div class="pro-field wide"><button class="pro-btn" type="submit" style="background:var(--accent);border-color:var(--accent);color:#fff;"><i class="bi bi-briefcase-fill"></i> Register business</button></div>
                </form>
            @endif

            <div class="pro-table-wrap">
                <table class="pro-table">
                    <thead><tr><th>Business</th><th>Permit</th><th>Address</th><th>Contact</th><th>Status</th><th>Update</th></tr></thead>
                    <tbody>
                        @forelse ($businesses as $business)
                            <tr>
                                <td><strong>{{ $business->business_name ?: $business->account_no }}</strong><div class="pro-muted">{{ $business->uuid }}</div></td>
                                <td>{{ $business->permit_no ?: 'N/A' }}</td>
                                <td>{{ $business->address_line ?: ($business->address ?: 'No address') }}</td>
                                <td>{{ $business->phone_no ?: $business->email ?: 'N/A' }}</td>
                                <td><span class="pro-pill {{ $statusClass($business->status_of_business ?: 'active') }}">{{ $business->status_of_business ?: 'Active' }}</span></td>
                                <td>
                                    @if ($canUpdateBusiness)
                                        <form method="POST" action="{{ route('dashboard.olives-pro.businesses.update', $business) }}" style="display:grid;gap:.35rem;min-width:220px;">
                                            @csrf
                                            <input class="pro-input" style="padding:.45rem .5rem;" name="phone_no" value="{{ $business->phone_no }}" placeholder="Phone">
                                            <input class="pro-input" style="padding:.45rem .5rem;" type="email" name="email" value="{{ $business->email }}" placeholder="Email">
                                            <button class="pro-btn" type="submit" style="padding:.45rem .55rem;"><i class="bi bi-save"></i> Update</button>
                                        </form>
                                    @else
                                        <span class="pro-muted">Not assigned</span>
                                    @endif
                                </td>
                            </tr>
                        @empty
                            <tr><td colspan="6"><div class="empty-pro">No business records found for this Pro account.</div></td></tr>
                        @endforelse
                    </tbody>
                </table>
            </div>
        </section>
    @endif

    @if ($section === 'bills')
        <section class="pro-panel">
            <div class="pro-head">
                <div>
                    <div class="pro-title"><i class="bi bi-receipt" style="color:var(--accent);"></i> Bills & Arrears</div>
                    <div class="pro-sub">Generate property rate bills, carry forward arrears, and avoid duplicate bills for the selected year.</div>
                </div>
            </div>

            @unless ($isProAgent)
                <form method="POST" action="{{ route('dashboard.olives-pro.bills.property-rates.generate') }}" class="pro-form" style="margin-bottom:1rem;">
                    @csrf
                    <div class="pro-field">
                        <label class="pro-label">Bill Year</label>
                        <input class="pro-input" name="bills_year" inputmode="numeric" maxlength="4" value="{{ old('bills_year', now()->year) }}" required>
                    </div>
                    <div class="pro-field">
                        <label class="pro-label">Calculation</label>
                        <select class="pro-select" name="calculation_mode" required>
                            <option value="rateable" @selected(old('calculation_mode', 'rateable') === 'rateable')>Rate impost x rateable value</option>
                            <option value="flat" @selected(old('calculation_mode') === 'flat')>Flat rate</option>
                        </select>
                    </div>
                    <div class="pro-field">
                        <label class="pro-label">Rate Impost</label>
                        <input class="pro-input" type="number" step="0.0001" min="0" name="rate_impost" value="{{ old('rate_impost', '0.001') }}">
                    </div>
                    <div class="pro-field">
                        <label class="pro-label">Flat Rate</label>
                        <input class="pro-input" type="number" step="0.01" min="0" name="flat_rate" value="{{ old('flat_rate', '0') }}">
                    </div>
                    <div class="pro-field">
                        <label class="pro-label">Minimum Bill</label>
                        <input class="pro-input" type="number" step="0.01" min="0" name="min_amount" value="{{ old('min_amount', '0') }}">
                    </div>
                    <div class="pro-field">
                        <label class="pro-label">Maximum Bill</label>
                        <input class="pro-input" type="number" step="0.01" min="0" name="max_amount" value="{{ old('max_amount', '0') }}">
                    </div>
                    <div class="pro-field">
                        <label class="pro-label">General Discount</label>
                        <input class="pro-input" type="number" step="0.01" min="0" name="discount_amount" value="{{ old('discount_amount', '0') }}">
                    </div>
                    <div class="pro-field" style="align-self:end;">
                        <label class="pro-check"><input type="checkbox" name="only_unbilled" value="1" @checked(old('only_unbilled', '1'))> Only properties without a bill for this year</label>
                    </div>
                    <div class="pro-field wide" style="display:flex;gap:.5rem;flex-wrap:wrap;">
                        <button class="pro-btn" type="submit" name="action" value="preview"><i class="bi bi-search"></i> Preview bills</button>
                        <button class="pro-btn" type="submit" name="action" value="generate" style="background:var(--accent);border-color:var(--accent);color:#fff;"><i class="bi bi-receipt-cutoff"></i> Generate property rate bills</button>
                    </div>
                </form>

                @if ($propertyRatePreview)
                    <div class="pro-grid" style="margin-bottom:1rem;">
                        <div class="pro-card"><div class="pro-card-title">Previewed</div><div class="pro-card-meta">{{ number_format($propertyRatePreview['processed'] ?? 0) }} properties processed.</div></div>
                        <div class="pro-card"><div class="pro-card-title">Billable</div><div class="pro-card-meta">{{ number_format(($propertyRatePreview['processed'] ?? 0) - ($propertyRatePreview['skipped'] ?? 0)) }} properties ready for billing.</div></div>
                        <div class="pro-card"><div class="pro-card-title">Total Bill</div><div class="pro-card-meta">{{ $money($propertyRatePreview['total_amount'] ?? 0) }} excluding carried arrears.</div></div>
                        <div class="pro-card"><div class="pro-card-title">Carried Arrears</div><div class="pro-card-meta">{{ $money($propertyRatePreview['total_arrears'] ?? 0) }} from prior bills.</div></div>
                    </div>
                    <div class="pro-table-wrap" style="margin-bottom:1rem;">
                        <table class="pro-table">
                            <thead><tr><th>Property</th><th>Source</th><th>Rateable Value</th><th>Gross</th><th>Discount</th><th>Arrears</th><th>Total Due</th><th>Status</th></tr></thead>
                            <tbody>
                                @foreach (collect($propertyRatePreview['items'] ?? [])->take(25) as $item)
                                    <tr>
                                        <td><strong>{{ $item['property'] ?? 'N/A' }}</strong></td>
                                        <td>{{ $item['rate_source'] ?? 'N/A' }}</td>
                                        <td>{{ $money($item['rateable_value'] ?? 0) }}</td>
                                        <td>{{ $money($item['gross_amount'] ?? 0) }}</td>
                                        <td>{{ $money($item['discount_amount'] ?? 0) }}</td>
                                        <td>{{ $money($item['arrears'] ?? 0) }}</td>
                                        <td>{{ $money($item['total_due'] ?? 0) }}</td>
                                        <td><span class="pro-pill {{ ($item['billable'] ?? false) ? 'good' : 'warn' }}">{{ ($item['billable'] ?? false) ? 'Ready' : ($item['reason'] ?? 'Skipped') }}</span></td>
                                    </tr>
                                @endforeach
                            </tbody>
                        </table>
                    </div>
                @endif
            @endunless

            <div class="pro-head"><div><div class="pro-title"><i class="bi bi-list-check" style="color:var(--accent);"></i> Issued Bills</div><div class="pro-sub">Issued bills and balances in your jurisdiction.</div></div></div>
            <div class="pro-table-wrap">
                <table class="pro-table">
                    <thead><tr><th>Bill</th><th>Asset</th><th>Year</th><th>Amount</th><th>Arrears</th><th>Paid</th></tr></thead>
                    <tbody>
                        @forelse ($bills as $bill)
                            @php($assetName = optional($bill->property)->account_no ?: optional($bill->business)->business_name ?: 'Linked asset')
                            <tr>
                                <td><strong>{{ $bill->bills_id }}</strong><div class="pro-muted">{{ $bill->uuid }}</div></td>
                                <td>{{ $assetName }}</td>
                                <td>{{ $bill->bills_year ?: 'N/A' }}</td>
                                <td>{{ $money($bill->amount) }}</td>
                                <td>{{ $money($bill->arrears) }}</td>
                                <td>{{ $money($bill->successfulPayments->sum('amount')) }}</td>
                            </tr>
                        @empty
                            <tr><td colspan="6"><div class="empty-pro">No bill records found for this Pro account.</div></td></tr>
                        @endforelse
                    </tbody>
                </table>
            </div>
        </section>
    @endif

    @if ($section === 'payments')
        <section class="pro-panel">
            <div class="pro-head">
                <div>
                    <div class="pro-title"><i class="bi bi-cash-coin" style="color:var(--accent);"></i> Payments</div>
                    <div class="pro-sub">Create electronic payment links, send them by email/SMS, and monitor collections.</div>
                </div>
                @unless ($isProAgent)
                    <div class="pro-actions">
                        <a class="pro-btn" href="{{ route('dashboard.olives-pro.integrations') }}"><i class="bi bi-sliders"></i> Gateway settings</a>
                    </div>
                @endunless
            </div>

            <form method="POST" action="{{ route('dashboard.olives-pro.payment-links.store') }}" class="pro-form" style="margin-bottom:1rem;">
                @csrf
                <div class="pro-field">
                    <label class="pro-label">Payment Provider</label>
                    <select class="pro-select" name="integration_uuid" required>
                        <option value="">Select provider</option>
                        @foreach ($paymentIntegrations as $integration)
                            <option value="{{ $integration->uuid }}" @selected(old('integration_uuid') === $integration->uuid)>
                                {{ $integration->label ?: $integration->providerLabel() }}{{ $integration->is_default ? ' (Default)' : '' }}
                            </option>
                        @endforeach
                    </select>
                </div>
                <div class="pro-field">
                    <label class="pro-label">Purpose</label>
                    <select class="pro-select" name="purpose" required>
                        <option value="bill" @selected(old('purpose') === 'bill')>Bill payment</option>
                        <option value="waste" @selected(old('purpose') === 'waste')>Waste service payment</option>
                        <option value="custom" @selected(old('purpose') === 'custom')>Other service payment</option>
                    </select>
                </div>
                <div class="pro-field">
                    <label class="pro-label">Bill</label>
                    <select class="pro-select" name="bill_uuid">
                        <option value="">No bill selected</option>
                        @foreach ($bills as $bill)
                            @php($assetName = optional($bill->property)->account_no ?: optional($bill->business)->business_name ?: 'Linked asset')
                            <option value="{{ $bill->uuid }}" @selected(old('bill_uuid') === $bill->uuid)>{{ $bill->bills_id }} - {{ $assetName }}</option>
                        @endforeach
                    </select>
                </div>
                <div class="pro-field">
                    <label class="pro-label">Waste Plan</label>
                    <select class="pro-select" name="waste_service_plan_uuid">
                        <option value="">No waste plan selected</option>
                        @foreach ($wasteServicePlans as $plan)
                            <option value="{{ $plan->uuid }}" @selected(old('waste_service_plan_uuid') === $plan->uuid)>
                                {{ $plan->company?->name }} - {{ $plan->name }} ({{ $plan->currency_symbol }} {{ number_format((float) $plan->service_charge, 2) }})
                            </option>
                        @endforeach
                    </select>
                </div>
                <div class="pro-field">
                    <label class="pro-label">Payer Name</label>
                    <input class="pro-input" name="payer_name" value="{{ old('payer_name') }}">
                </div>
                <div class="pro-field">
                    <label class="pro-label">Amount</label>
                    <input class="pro-input" type="number" step="0.01" min="1" name="amount" value="{{ old('amount') }}" required>
                </div>
                <div class="pro-field">
                    <label class="pro-label">Payer Email</label>
                    <input class="pro-input" type="email" name="payer_email" value="{{ old('payer_email') }}">
                </div>
                <div class="pro-field">
                    <label class="pro-label">Payer Phone</label>
                    <input class="pro-input" name="payer_phone" value="{{ old('payer_phone') }}">
                </div>
                <div class="pro-field wide">
                    <label class="pro-label">Description</label>
                    <textarea class="pro-textarea" name="description" placeholder="Example: 2026 property rate balance or weekly waste pickup service">{{ old('description') }}</textarea>
                </div>
                <div class="pro-field wide">
                    <button class="pro-btn" type="submit" style="background:var(--accent);border-color:var(--accent);color:#fff;"><i class="bi bi-send"></i> Create and send payment link</button>
                    @if ($paymentIntegrations->isEmpty())
                        <div class="pro-muted" style="margin-top:.5rem;">Configure at least one active payment provider before sending links.</div>
                    @endif
                </div>
            </form>

            <div class="pro-head" style="margin-top:.8rem;"><div><div class="pro-title"><i class="bi bi-link-45deg" style="color:var(--accent);"></i> Payment Links</div><div class="pro-sub">Links generated from this Olives Pro account.</div></div></div>
            <div class="pro-table-wrap" style="margin-bottom:1rem;">
                <table class="pro-table">
                    <thead><tr><th>Reference</th><th>Payer</th><th>Purpose</th><th>Amount</th><th>Provider</th><th>Status</th><th>Link</th></tr></thead>
                    <tbody>
                        @forelse ($paymentRequests as $requestRow)
                            <tr>
                                <td><strong>{{ $requestRow->payment_reference ?: 'Pending' }}</strong><div class="pro-muted">{{ optional($requestRow->created_at)->format('d M Y H:i') }}</div></td>
                                <td>{{ $requestRow->payer_name ?: 'N/A' }}<div class="pro-muted">{{ $requestRow->payer_email ?: $requestRow->payer_phone }}</div></td>
                                <td>{{ ucfirst($requestRow->purpose) }}<div class="pro-muted">{{ $requestRow->description }}</div></td>
                                <td>{{ strtoupper($requestRow->currency_code) }} {{ number_format((float) $requestRow->amount, 2) }}</td>
                                <td>{{ optional($requestRow->integration)->label ?: ucfirst((string) $requestRow->provider) }}</td>
                                <td><span class="pro-pill {{ $statusClass($requestRow->status) }}">{{ $requestRow->status }}</span></td>
                                <td><a class="pro-btn" href="{{ $requestRow->public_url ?: route('public.pro-payment-requests.show', $requestRow) }}" target="_blank" rel="noopener"><i class="bi bi-box-arrow-up-right"></i> Open</a></td>
                            </tr>
                        @empty
                            <tr><td colspan="7"><div class="empty-pro">No payment links have been created yet.</div></td></tr>
                        @endforelse
                    </tbody>
                </table>
            </div>

            <div class="pro-head"><div><div class="pro-title"><i class="bi bi-list-check" style="color:var(--accent);"></i> Received Payments</div><div class="pro-sub">Payment records collected in your jurisdiction.</div></div></div>
            <div class="pro-table-wrap">
                <table class="pro-table">
                    <thead><tr><th>Transaction</th><th>Bill</th><th>Mode</th><th>Amount</th><th>Status</th><th>Date</th></tr></thead>
                    <tbody>
                        @forelse ($payments as $payment)
                            <tr>
                                <td><strong>{{ $payment->transaction_id ?: $payment->req_transaction_id }}</strong><div class="pro-muted">{{ $payment->uuid }}</div></td>
                                <td>{{ $payment->bills_id }}</td>
                                <td>{{ $payment->payment_mode ?: 'N/A' }}</td>
                                <td>{{ $money($payment->amount) }}</td>
                                <td><span class="pro-pill {{ $statusClass($payment->transaction_status) }}">{{ $payment->transaction_status ?: 'Pending' }}</span></td>
                                <td>{{ optional($payment->created_at)->format('d M Y H:i') ?: 'N/A' }}</td>
                            </tr>
                        @empty
                            <tr><td colspan="6"><div class="empty-pro">No payment records found for this Pro account.</div></td></tr>
                        @endforelse
                    </tbody>
                </table>
            </div>
        </section>
    @endif

    @if ($section === 'laws')
        <section class="pro-panel">
            <div class="pro-head"><div><div class="pro-title"><i class="bi bi-bank" style="color:var(--accent);"></i> Laws & Fees</div><div class="pro-sub">Revenue streams and charge settings scoped to your assembly.</div></div></div>
            <div class="pro-table-wrap">
                <table class="pro-table">
                    <thead><tr><th>Code</th><th>Description</th><th>Rate Impost</th><th>Flat Rate</th><th>Minimum</th><th>Maximum</th></tr></thead>
                    <tbody>
                        @forelse ($revenueStreams as $stream)
                            <tr>
                                <td><strong>{{ $stream->code ?: 'N/A' }}</strong><div class="pro-muted">{{ $stream->uuid }}</div></td>
                                <td>{{ $stream->description ?: 'No description' }}</td>
                                <td>{{ $stream->rate_impost ?: 'N/A' }}</td>
                                <td>{{ $money($stream->flat_rate) }}</td>
                                <td>{{ $money($stream->min_amount) }}</td>
                                <td>{{ $money($stream->max_amount) }}</td>
                            </tr>
                        @empty
                            <tr><td colspan="6"><div class="empty-pro">No laws or fee records found for this Pro account.</div></td></tr>
                        @endforelse
                    </tbody>
                </table>
            </div>
        </section>
    @endif

    @if ($section === 'citizens')
        <section class="pro-panel">
            <div class="pro-head"><div><div class="pro-title"><i class="bi bi-people" style="color:var(--accent);"></i> Citizens</div><div class="pro-sub">Rate payers linked to your jurisdiction assets.</div></div></div>
            <div class="pro-table-wrap">
                <table class="pro-table">
                    <thead><tr><th>Name</th><th>Account</th><th>Phone</th><th>Email</th><th>Status</th></tr></thead>
                    <tbody>
                        @forelse ($citizens as $citizen)
                            <tr>
                                <td><strong>{{ trim(($citizen->first_name ?? '').' '.($citizen->last_name ?? '')) ?: $citizen->business_name }}</strong><div class="pro-muted">{{ $citizen->uuid }}</div></td>
                                <td>{{ $citizen->account_number ?: 'N/A' }}</td>
                                <td>{{ $citizen->telephone_number ?: 'N/A' }}</td>
                                <td>{{ $citizen->email ?: 'N/A' }}</td>
                                <td><span class="pro-pill {{ $statusClass($citizen->status) }}">{{ $citizen->status ?: 'N/A' }}</span></td>
                            </tr>
                        @empty
                            <tr><td colspan="5"><div class="empty-pro">No citizen records found for this Pro account.</div></td></tr>
                        @endforelse
                    </tbody>
                </table>
            </div>
        </section>
    @endif

    @if ($section === 'users')
        <section class="pro-panel">
            <div class="pro-head"><div><div class="pro-title"><i class="bi bi-person-gear" style="color:var(--accent);"></i> Agents & Users</div><div class="pro-sub">Register field agents and keep their capabilities limited to assigned work.</div></div></div>

            <form method="POST" action="{{ route('dashboard.olives-pro.agents.store') }}" class="pro-form" style="margin-bottom:1rem;">
                @csrf
                <div class="pro-field"><label class="pro-label">Agent Name</label><input class="pro-input" name="name" value="{{ old('name') }}" required></div>
                <div class="pro-field"><label class="pro-label">Email</label><input class="pro-input" type="email" name="email" value="{{ old('email') }}" required></div>
                <div class="pro-field"><label class="pro-label">Phone</label><input class="pro-input" name="phone" value="{{ old('phone') }}" required></div>
                <div class="pro-field"><label class="pro-label">Temporary Password</label><input class="pro-input" type="password" name="password" required minlength="8"></div>
                <div class="pro-field wide">
                    <label class="pro-label">Allowed Capabilities</label>
                    <div class="pro-check-grid">
                        @foreach ($agentCapabilityLabels as $capability => $label)
                            <label class="pro-check">
                                <input type="checkbox" name="agent_capabilities[]" value="{{ $capability }}" @checked(in_array($capability, old('agent_capabilities', [])))>
                                <span>{{ $label }}</span>
                            </label>
                        @endforeach
                    </div>
                </div>
                <div class="pro-field wide"><button class="pro-btn" type="submit" style="background:var(--accent);border-color:var(--accent);color:#fff;"><i class="bi bi-person-plus"></i> Register agent</button></div>
            </form>

            <div class="pro-head" style="margin-top:.8rem;"><div><div class="pro-title"><i class="bi bi-person-badge" style="color:var(--accent);"></i> Registered Agents</div><div class="pro-sub">Agents created by this Olives Pro holder.</div></div></div>
            <div class="pro-table-wrap" style="margin-bottom:1rem;">
                <table class="pro-table">
                    <thead><tr><th>Agent</th><th>Phone</th><th>Capabilities</th><th>Status</th></tr></thead>
                    <tbody>
                        @forelse ($agents as $agent)
                            <tr>
                                <td><strong>{{ $agent->name }}</strong><div class="pro-muted">{{ $agent->email ?: $agent->uuid }}</div></td>
                                <td>{{ $agent->phone ?: 'N/A' }}</td>
                                <td>{{ collect($agent->agent_capabilities ?: [])->map(fn ($capability) => $agentCapabilityLabels[$capability] ?? $capability)->implode(', ') ?: 'No capabilities' }}</td>
                                <td><span class="pro-pill {{ $statusClass($agent->status) }}">{{ $agent->status ?: 'N/A' }}</span></td>
                            </tr>
                        @empty
                            <tr><td colspan="4"><div class="empty-pro">No agents registered yet.</div></td></tr>
                        @endforelse
                    </tbody>
                </table>
            </div>

            <div class="pro-head"><div><div class="pro-title"><i class="bi bi-people" style="color:var(--accent);"></i> Assembly Users</div><div class="pro-sub">Users linked to this assembly.</div></div></div>
            <div class="pro-table-wrap">
                <table class="pro-table">
                    <thead><tr><th>User</th><th>Phone</th><th>Access Level</th><th>Roles</th><th>Status</th></tr></thead>
                    <tbody>
                        @forelse ($users as $linkedUser)
                            <tr>
                                <td><strong>{{ $linkedUser->name }}</strong><div class="pro-muted">{{ $linkedUser->email ?: $linkedUser->uuid }}</div></td>
                                <td>{{ $linkedUser->phone ?: 'N/A' }}</td>
                                <td>{{ $linkedUser->access_level ?: 'N/A' }}</td>
                                <td>{{ $linkedUser->roles->pluck('name')->implode(', ') ?: 'No roles' }}</td>
                                <td><span class="pro-pill {{ $statusClass($linkedUser->status) }}">{{ $linkedUser->status ?: 'N/A' }}</span></td>
                            </tr>
                        @empty
                            <tr><td colspan="5"><div class="empty-pro">No user records found for this Pro account.</div></td></tr>
                        @endforelse
                    </tbody>
                </table>
            </div>
        </section>
    @endif

    @if ($section === 'reports')
        <section class="pro-panel">
            <div class="pro-head"><div><div class="pro-title"><i class="bi bi-file-earmark-bar-graph" style="color:var(--accent);"></i> Reports</div><div class="pro-sub">Jurisdiction summary without permission-restricted report screens.</div></div></div>
            <div class="pro-grid">
                <div class="pro-card"><div class="pro-card-title">Property Coverage</div><div class="pro-card-meta">{{ number_format($properties->count()) }} properties and {{ number_format($businesses->count()) }} businesses in scope.</div></div>
                <div class="pro-card"><div class="pro-card-title">Billing</div><div class="pro-card-meta">{{ number_format($bills->count()) }} bills totaling {{ $money($billTotal) }}.</div></div>
                <div class="pro-card"><div class="pro-card-title">Collections</div><div class="pro-card-meta">{{ number_format($successfulPayments->count()) }} successful payments totaling {{ $money($paidTotal) }}.</div></div>
                <div class="pro-card"><div class="pro-card-title">Waste Services</div><div class="pro-card-meta">{{ number_format($wasteCompanies->count()) }} waste companies registered or available.</div></div>
                <div class="pro-card"><div class="pro-card-title">Rate Payers</div><div class="pro-card-meta">{{ number_format($citizens->count()) }} citizen records linked through assets.</div></div>
                <div class="pro-card"><div class="pro-card-title">Laws & Fees</div><div class="pro-card-meta">{{ number_format($revenueStreams->count()) }} revenue stream records configured.</div></div>
            </div>
        </section>
    @endif

    @if ($section === 'integrations')
        <section class="pro-panel">
            <div class="pro-head">
                <div>
                    <div class="pro-title"><i class="bi bi-sliders" style="color:var(--accent);"></i> Payment & SMS Settings</div>
                    <div class="pro-sub">These credentials belong to this Olives Pro account only. Donation continues to use the platform donation gateways.</div>
                </div>
            </div>

            <div class="pro-grid" style="grid-template-columns:repeat(2,minmax(0,1fr));">
                <div class="pro-card" style="min-height:auto;">
                    <div class="pro-card-title">Payment Gateway</div>
                    <div class="pro-card-meta" style="margin-bottom:.75rem;">Paystack, Flutterwave, Stripe, Hubtel, expressPay, and GhIPSS/GhQR provider settings.</div>
                    <form method="POST" action="{{ route('dashboard.olives-pro.integrations.store') }}" class="pro-form" style="grid-template-columns:1fr;">
                        @csrf
                        <input type="hidden" name="category" value="payment">
                        <div class="pro-field">
                            <label class="pro-label">Provider</label>
                            <select class="pro-select" name="provider" required>
                                @foreach ($paymentProviderNames as $provider => $label)
                                    <option value="{{ $provider }}">{{ $label }}</option>
                                @endforeach
                            </select>
                        </div>
                        <div class="pro-field"><label class="pro-label">Display Label</label><input class="pro-input" name="label" placeholder="Example: Assembly Paystack"></div>
                        <div class="pro-field"><label class="pro-label">Public Key</label><input class="pro-input" name="credentials[public_key]"></div>
                        <div class="pro-field"><label class="pro-label">Secret Key / API Key</label><input class="pro-input" name="credentials[secret_key]"></div>
                        <div class="pro-field"><label class="pro-label">Encryption Key</label><input class="pro-input" name="credentials[encryption_key]"></div>
                        <div class="pro-field"><label class="pro-label">Client ID</label><input class="pro-input" name="credentials[client_id]"></div>
                        <div class="pro-field"><label class="pro-label">Client Secret</label><input class="pro-input" name="credentials[client_secret]"></div>
                        <div class="pro-field"><label class="pro-label">Merchant Account / ID</label><input class="pro-input" name="credentials[merchant_account_number]" placeholder="Hubtel account number"></div>
                        <div class="pro-field"><label class="pro-label">Merchant ID</label><input class="pro-input" name="credentials[merchant_id]" placeholder="expressPay/GhIPSS merchant"></div>
                        <div class="pro-field"><label class="pro-label">API Key</label><input class="pro-input" name="credentials[api_key]"></div>
                        <div class="pro-field"><label class="pro-label">Base URL</label><input class="pro-input" name="credentials[base_url]" placeholder="Optional provider base URL"></div>
                        <div class="pro-field"><label class="pro-label">Checkout Path / URL</label><input class="pro-input" name="credentials[checkout_path]" placeholder="Hubtel path, if different"></div>
                        <div class="pro-field"><label class="pro-label">GhIPSS/GhQR Checkout URL</label><input class="pro-input" name="credentials[checkout_url]" placeholder="Provider-issued payment URL"></div>
                        <label class="pro-check"><input type="checkbox" name="is_active" value="1" checked> Active</label>
                        <label class="pro-check"><input type="checkbox" name="is_default" value="1"> Make default payment provider</label>
                        <button class="pro-btn" type="submit" style="background:var(--accent);border-color:var(--accent);color:#fff;"><i class="bi bi-save"></i> Save payment provider</button>
                    </form>
                </div>

                <div class="pro-card" style="min-height:auto;">
                    <div class="pro-card-title">SMS Provider</div>
                    <div class="pro-card-meta" style="margin-bottom:.75rem;">Hubtel SMS, MNotify, or Twilio settings for payment and waste pickup notifications.</div>
                    <form method="POST" action="{{ route('dashboard.olives-pro.integrations.store') }}" class="pro-form" style="grid-template-columns:1fr;">
                        @csrf
                        <input type="hidden" name="category" value="sms">
                        <div class="pro-field">
                            <label class="pro-label">Provider</label>
                            <select class="pro-select" name="provider" required>
                                @foreach ($smsProviderNames as $provider => $label)
                                    <option value="{{ $provider }}">{{ $label }}</option>
                                @endforeach
                            </select>
                        </div>
                        <div class="pro-field"><label class="pro-label">Display Label</label><input class="pro-input" name="label" placeholder="Example: Assembly Twilio"></div>
                        <div class="pro-field"><label class="pro-label">Sender ID</label><input class="pro-input" name="credentials[sender_id]"></div>
                        <div class="pro-field"><label class="pro-label">API Key</label><input class="pro-input" name="credentials[api_key]"></div>
                        <div class="pro-field"><label class="pro-label">Client ID</label><input class="pro-input" name="credentials[client_id]"></div>
                        <div class="pro-field"><label class="pro-label">Client Secret</label><input class="pro-input" name="credentials[client_secret]"></div>
                        <div class="pro-field"><label class="pro-label">Twilio Account SID</label><input class="pro-input" name="credentials[account_sid]"></div>
                        <div class="pro-field"><label class="pro-label">Twilio Auth Token</label><input class="pro-input" name="credentials[auth_token]"></div>
                        <div class="pro-field"><label class="pro-label">From Number</label><input class="pro-input" name="credentials[from_number]"></div>
                        <div class="pro-field"><label class="pro-label">Messaging Service SID</label><input class="pro-input" name="credentials[messaging_service_sid]"></div>
                        <div class="pro-field"><label class="pro-label">Base URL</label><input class="pro-input" name="credentials[base_url]" placeholder="Optional provider base URL"></div>
                        <div class="pro-field"><label class="pro-label">Send Path</label><input class="pro-input" name="credentials[send_path]" placeholder="Optional path"></div>
                        <label class="pro-check"><input type="checkbox" name="is_active" value="1" checked> Active</label>
                        <label class="pro-check"><input type="checkbox" name="is_default" value="1"> Make default SMS provider</label>
                        <button class="pro-btn" type="submit" style="background:var(--accent);border-color:var(--accent);color:#fff;"><i class="bi bi-save"></i> Save SMS provider</button>
                    </form>
                </div>
            </div>

            <div class="pro-table-wrap" style="margin-top:1rem;">
                <table class="pro-table">
                    <thead><tr><th>Label</th><th>Category</th><th>Provider</th><th>Default</th><th>Status</th><th>Updated</th></tr></thead>
                    <tbody>
                        @forelse ($integrations as $integration)
                            <tr>
                                <td><strong>{{ $integration->label ?: $integration->providerLabel() }}</strong><div class="pro-muted">{{ $integration->uuid }}</div></td>
                                <td>{{ ucfirst($integration->category) }}</td>
                                <td>{{ $integration->providerLabel() }}</td>
                                <td><span class="pro-pill {{ $integration->is_default ? 'good' : 'muted' }}">{{ $integration->is_default ? 'Default' : 'Available' }}</span></td>
                                <td><span class="pro-pill {{ $integration->is_active ? 'good' : 'danger' }}">{{ $integration->is_active ? 'Active' : 'Inactive' }}</span></td>
                                <td>{{ optional($integration->updated_at)->format('d M Y H:i') }}</td>
                            </tr>
                        @empty
                            <tr><td colspan="6"><div class="empty-pro">No payment or SMS integrations configured yet.</div></td></tr>
                        @endforelse
                    </tbody>
                </table>
            </div>
        </section>
    @endif

    @if ($section === 'profile')
        <section class="pro-panel">
            <div class="pro-head">
                <div>
                    <div class="pro-title"><i class="bi bi-person-circle" style="color:var(--accent);"></i> My Profile</div>
                    <div class="pro-sub">Update the account holder name shown across the Olives Pro workspace.</div>
                </div>
            </div>
            <form method="POST" action="{{ route('dashboard.olives-pro.profile.update') }}" style="display:grid;grid-template-columns:repeat(2,minmax(0,1fr));gap:.75rem;">
                @csrf
                <div>
                    <label style="display:block;font-size:.67rem;font-weight:900;text-transform:uppercase;letter-spacing:.45px;color:var(--muted);margin-bottom:.3rem;">Name</label>
                    <input name="name" value="{{ old('name', $user->name) }}" required style="width:100%;border:1px solid var(--line);border-radius:10px;padding:.68rem .75rem;color:#17251b;background:#fff;font:inherit;outline:0;">
                </div>
                <div>
                    <label style="display:block;font-size:.67rem;font-weight:900;text-transform:uppercase;letter-spacing:.45px;color:var(--muted);margin-bottom:.3rem;">Email</label>
                    <input value="{{ $user->email ?: 'N/A' }}" readonly style="width:100%;border:1px solid var(--line);border-radius:10px;padding:.68rem .75rem;color:var(--muted);background:#fbfefc;font:inherit;outline:0;">
                </div>
                <div>
                    <label style="display:block;font-size:.67rem;font-weight:900;text-transform:uppercase;letter-spacing:.45px;color:var(--muted);margin-bottom:.3rem;">Phone</label>
                    <input value="{{ $user->phone ?: 'N/A' }}" readonly style="width:100%;border:1px solid var(--line);border-radius:10px;padding:.68rem .75rem;color:var(--muted);background:#fbfefc;font:inherit;outline:0;">
                </div>
                <div>
                    <label style="display:block;font-size:.67rem;font-weight:900;text-transform:uppercase;letter-spacing:.45px;color:var(--muted);margin-bottom:.3rem;">Assembly</label>
                    <input value="{{ $assembly->name ?? ($assemblyCode ?: 'Not assigned') }}" readonly style="width:100%;border:1px solid var(--line);border-radius:10px;padding:.68rem .75rem;color:var(--muted);background:#fbfefc;font:inherit;outline:0;">
                </div>
                <div style="grid-column:1 / -1;">
                    <button class="pro-btn" type="submit" style="background:var(--accent);border-color:var(--accent);color:#fff;"><i class="bi bi-save"></i> Update profile</button>
                </div>
            </form>
        </section>
    @endif
</div>

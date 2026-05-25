@php
    $errors = $errors ?? new \Illuminate\Support\ViewErrorBag;
@endphp

<style>
    .waste-form { display:grid; grid-template-columns:repeat(2,minmax(0,1fr)); gap:.75rem; }
    .waste-form .wide { grid-column:1 / -1; }
    .waste-label { display:block; font-size:.68rem; font-weight:900; text-transform:uppercase; letter-spacing:.45px; color:var(--muted); margin-bottom:.3rem; }
    .waste-input, .waste-select, .waste-textarea { width:100%; border:1px solid var(--line); border-radius:10px; padding:.68rem .75rem; color:#17251b; background:#fff; font:inherit; outline:0; }
    .waste-textarea { min-height:80px; resize:vertical; }
    .waste-btn { display:inline-flex; align-items:center; justify-content:center; gap:.4rem; border-radius:9px; border:1px solid var(--line); background:var(--soft); color:var(--primary); font-size:.78rem; font-weight:900; padding:.55rem .75rem; text-decoration:none; cursor:pointer; }
    .waste-btn.primary { background:var(--accent); border-color:var(--accent); color:#fff; }
    .company-card { border:1px solid var(--line); border-radius:12px; background:#fbfefc; padding:1rem; margin-bottom:.85rem; }
    .company-title { font-family:'Poppins',sans-serif; font-weight:800; color:#17251b; }
    .company-meta { color:var(--muted); font-size:.78rem; line-height:1.45; margin:.25rem 0 .75rem; }
    .plan-list { display:grid; gap:.45rem; margin:.65rem 0; }
    .plan-item { border:1px solid var(--line); border-radius:10px; padding:.65rem; background:#fff; font-size:.82rem; }
    @media (max-width:900px) { .waste-form { grid-template-columns:1fr; } .waste-form .wide { grid-column:auto; } }
</style>

@if ($errors->any())
    <div class="panel" style="border-color:#fecaca;background:#fef2f2;color:#991b1b;margin-bottom:1rem;">
        <strong>Check the form:</strong> {{ $errors->first() }}
    </div>
@endif

<div class="panel" style="margin-bottom:1rem;">
    <div class="panel-head">
        <div>
            <div class="panel-title"><i class="bi bi-building-add" style="color:var(--accent);"></i> Register Waste Company</div>
            <div class="section-sub">Companies published here become visible to Olives Account holders for booking.</div>
        </div>
    </div>
    <form method="POST" action="{{ route('dashboard.olives-pro.waste-companies.store') }}" class="waste-form">
        @csrf
        <div><label class="waste-label">Company Name</label><input class="waste-input" name="name" value="{{ old('name') }}" required></div>
        <div><label class="waste-label">Contact Person</label><input class="waste-input" name="contact_person" value="{{ old('contact_person') }}"></div>
        <div><label class="waste-label">Phone</label><input class="waste-input" name="phone" value="{{ old('phone') }}"></div>
        <div><label class="waste-label">Email</label><input class="waste-input" type="email" name="email" value="{{ old('email') }}"></div>
        <div><label class="waste-label">Service Area</label><input class="waste-input" name="service_area" value="{{ old('service_area') }}" placeholder="Example: Osu, Accra Central"></div>
        <div><label class="waste-label">Status</label><select class="waste-select" name="status"><option value="active">Active</option><option value="suspended">Suspended</option></select></div>
        <div class="wide"><label class="waste-label">Full Address</label><input class="waste-input" name="address_line" value="{{ old('address_line') }}" placeholder="Street, landmark, locality, or postal address"></div>
        <div><label class="waste-label">Continent</label><input class="waste-input" name="continent" value="{{ old('continent', 'Africa') }}"></div>
        <div><label class="waste-label">Country</label><input class="waste-input" name="country" value="{{ old('country', 'Ghana') }}"></div>
        <div><label class="waste-label">Country Code</label><input class="waste-input" name="country_code" value="{{ old('country_code', 'GH') }}" maxlength="3"></div>
        <div><label class="waste-label">State / Region</label><input class="waste-input" name="administrative_area" value="{{ old('administrative_area') }}"></div>
        <div><label class="waste-label">City / Town</label><input class="waste-input" name="city" value="{{ old('city') }}"></div>
        <div><label class="waste-label">District / County</label><input class="waste-input" name="district" value="{{ old('district') }}"></div>
        <div><label class="waste-label">Postal Code</label><input class="waste-input" name="postal_code" value="{{ old('postal_code') }}"></div>
        <div class="wide"><label class="waste-label">Notes</label><textarea class="waste-textarea" name="notes">{{ old('notes') }}</textarea></div>
        <div class="wide"><button class="waste-btn primary" type="submit"><i class="bi bi-save"></i> Register company</button></div>
    </form>
</div>

<div class="panel">
    <div class="panel-head">
        <div class="panel-title"><i class="bi bi-recycle" style="color:var(--accent);"></i> Registered Waste Companies</div>
    </div>

    @forelse ($companies as $company)
        <div class="company-card">
            <div class="company-title">{{ $company->name }} <span style="font-size:.7rem;color:var(--muted);">({{ $company->status }})</span></div>
            <div class="company-meta">{{ $company->service_area ?: 'No service area' }} &middot; {{ $company->phone ?: 'No phone' }} @if($company->email) &middot; {{ $company->email }} @endif</div>
            <div class="plan-list">
                @forelse ($company->plans as $plan)
                    <div class="plan-item">
                        <strong>{{ $plan->name }}</strong> &middot; {{ $plan->waste_stream }} &middot; {{ ucfirst($plan->default_frequency) }} &middot;
                        {{ $plan->currency_symbol }} {{ number_format((float) $plan->service_charge, 2) }} {{ $plan->charge_period }}
                    </div>
                @empty
                    <div class="plan-item">No service charges have been added yet.</div>
                @endforelse
            </div>

            <form method="POST" action="{{ route('dashboard.olives-pro.waste-companies.plans.store', $company) }}" class="waste-form" style="margin-top:.8rem;">
                @csrf
                <div><label class="waste-label">Plan Name</label><input class="waste-input" name="name" placeholder="Standard household pickup" required></div>
                <div><label class="waste-label">Waste Stream</label><input class="waste-input" name="waste_stream" value="General waste" required></div>
                <div><label class="waste-label">Default Frequency</label><select class="waste-select" name="default_frequency"><option value="weekly">Weekly</option><option value="daily">Daily</option><option value="biweekly">Every two weeks</option><option value="monthly">Monthly</option><option value="once">One time</option></select></div>
                <div><label class="waste-label">Service Charge</label><input class="waste-input" type="number" step="0.01" min="0" name="service_charge" required></div>
                <div><label class="waste-label">Currency Code</label><input class="waste-input" name="currency_code" value="{{ optional($company->assembly)->currency_code ?: 'GHS' }}" maxlength="3" required></div>
                <div><label class="waste-label">Currency Symbol</label><input class="waste-input" name="currency_symbol" value="{{ optional($company->assembly)->currency_symbol ?: 'GHS' }}"></div>
                <div><label class="waste-label">Charge Period</label><input class="waste-input" name="charge_period" value="per pickup" required></div>
                <div class="wide"><label class="waste-label">Description</label><textarea class="waste-textarea" name="description"></textarea></div>
                <div class="wide"><button class="waste-btn" type="submit"><i class="bi bi-plus-circle"></i> Add service charge</button></div>
            </form>
        </div>
    @empty
        <div class="company-card">No waste companies registered yet.</div>
    @endforelse
</div>

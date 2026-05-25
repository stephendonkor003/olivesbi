@extends('dashboard.tiers.partials.tier-page-layout')

@section('page-title', 'My Profile')
@section('page-scope', 'My Profile')

@section('page-content')

@php
    $profileUser = auth()->user();
    $accountCard = \App\Support\OlivesCardNumber::format(optional($profileUser)->olives_card_number);
@endphp

{{-- Hero Banner --}}
<div class="tier-hero">
    <div>
        <div class="hero-badge"><i class="bi bi-person-circle"></i> Account</div>
        <div class="hero-title">My Profile</div>
        <div class="hero-sub">Manage your Olives BI account details and sign-in credentials.</div>
    </div>
    <div style="font-size:.78rem; color:rgba(255,255,255,.55); text-align:right; white-space:nowrap;">
        <div style="font-size:.68rem;text-transform:uppercase;letter-spacing:.5px;color:rgba(255,255,255,.4);margin-bottom:.2rem;">Session</div>
        {{ now()->format('H:i') }} · {{ now()->format('l') }}
    </div>
</div>

@if ($errors->any())
    <div style="background:#fce4ec; border:1px solid #e57373; border-radius:10px; padding:.85rem 1rem; margin-bottom:1rem; font-size:.85rem; color:#880e4f;">
        <i class="bi bi-exclamation-triangle-fill me-2"></i>
        Please fix the errors below and try again.
    </div>
@endif

@if (session('status'))
    <div style="background:#e8f5e9; border:1px solid #81c784; border-radius:10px; padding:.85rem 1rem; margin-bottom:1rem; font-size:.85rem; color:#1a5c2a;">
        <i class="bi bi-check-circle-fill me-2"></i> {{ session('status') }}
    </div>
@endif

<div style="display:grid; grid-template-columns: minmax(0,1.15fr) minmax(280px,.85fr); gap:1rem; align-items:start;" class="two-col">

    {{-- LEFT: Profile form --}}
    <div>

        @if (empty($citizen))
        {{-- Simple account profile (no citizen record) --}}
        <div class="panel">
            <div class="panel-head">
                <div class="panel-title"><i class="bi bi-person-fill" style="color:var(--accent);"></i> Account Information</div>
            </div>
            <div style="display:grid; grid-template-columns:1fr 1fr; gap:.85rem;">
                <div>
                    <label class="field-label">Account Name</label>
                    <input class="field-input" type="text" value="{{ optional($profileUser)->name }}" readonly>
                </div>
                <div>
                    <label class="field-label">Email Address</label>
                    <input class="field-input" type="text" value="{{ optional($profileUser)->email }}" readonly>
                </div>
                <div>
                    <label class="field-label">Phone Number</label>
                    <input class="field-input" type="text" value="{{ optional($profileUser)->phone ?? '—' }}" readonly>
                </div>
                <div>
                    <label class="field-label">Access Level</label>
                    <input class="field-input" type="text" value="{{ optional($profileUser)->access_level ?? '—' }}" readonly>
                </div>
                <div>
                    <label class="field-label">Account Status</label>
                    <input class="field-input" type="text" value="{{ optional($profileUser)->status ?? 'Active' }}" readonly>
                </div>
                <div>
                    <label class="field-label">Olives Account Card</label>
                    <input class="field-input" type="text" value="{{ $accountCard }}" readonly style="font-family:'Space Mono',monospace; letter-spacing:.08em; font-weight:700;">
                    <div style="font-size:.72rem; color:var(--muted); margin-top:.3rem;">Use this card number, your email, or phone to sign in.</div>
                </div>
            </div>
        </div>

        @else
        {{-- Full citizen profile (rate payer record) --}}
        <div class="panel">
            <div class="panel-head">
                <div class="panel-title"><i class="bi bi-person-vcard-fill" style="color:var(--accent);"></i> Profile Details</div>
                <div style="font-size:.75rem; color:var(--muted);">Record: {{ $citizen->account_number }}</div>
            </div>
            <form class="needs-validation" action="{{ route('dashboard.updateMyProfile', $citizen) }}" method="POST" novalidate>
                @csrf
                <div style="display:grid; grid-template-columns:1fr 1fr; gap:.85rem;">

                    <div>
                        <label class="field-label" for="first_name">First Name</label>
                        <input class="field-input @error('first_name') is-invalid @enderror" type="text"
                            id="first_name" name="first_name" value="{{ $citizen->first_name }}">
                        @error('first_name')<div class="field-error">{{ $message }}</div>@enderror
                    </div>

                    <div>
                        <label class="field-label" for="last_name">Last Name</label>
                        <input class="field-input @error('last_name') is-invalid @enderror" type="text"
                            id="last_name" name="last_name" value="{{ $citizen->last_name }}">
                        @error('last_name')<div class="field-error">{{ $message }}</div>@enderror
                    </div>

                    <div>
                        <label class="field-label" for="prefix">Prefix</label>
                        <select class="field-select @error('prefix') is-invalid @enderror" id="prefix" name="prefix">
                            <option disabled {{ !$citizen->prefix ? 'selected' : '' }}>Select Prefix</option>
                            @foreach (['Mr','Mrs','Miss','Dr'] as $p)
                                <option value="{{ $p }}" {{ $citizen->prefix == $p ? 'selected' : '' }}>{{ $p }}</option>
                            @endforeach
                        </select>
                        @error('prefix')<div class="field-error">{{ $message }}</div>@enderror
                    </div>

                    <div>
                        <label class="field-label" for="gender">Gender</label>
                        <select class="field-select @error('gender') is-invalid @enderror" id="gender" name="gender">
                            <option value="Male"   {{ $citizen->gender == 'Male'   ? 'selected' : '' }}>Male</option>
                            <option value="Female" {{ $citizen->gender == 'Female' ? 'selected' : '' }}>Female</option>
                        </select>
                        @error('gender')<div class="field-error">{{ $message }}</div>@enderror
                    </div>

                    <div>
                        <label class="field-label" for="date_of_birth">Date of Birth</label>
                        <input class="field-input @error('date_of_birth') is-invalid @enderror" type="date"
                            id="date_of_birth" name="date_of_birth" value="{{ $citizen->date_of_birth }}">
                        @error('date_of_birth')<div class="field-error">{{ $message }}</div>@enderror
                    </div>

                    <div>
                        <label class="field-label" for="marital_status">Marital Status</label>
                        <select class="field-select @error('marital_status') is-invalid @enderror" id="marital_status" name="marital_status">
                            @foreach (['Single','Married','Divorced','Widowed'] as $ms)
                                <option value="{{ $ms }}" {{ $citizen->marital_status == $ms ? 'selected' : '' }}>{{ $ms }}</option>
                            @endforeach
                        </select>
                        @error('marital_status')<div class="field-error">{{ $message }}</div>@enderror
                    </div>

                    <div>
                        <label class="field-label" for="telephone_number">Telephone Number</label>
                        <input class="field-input @error('telephone_number') is-invalid @enderror" type="text"
                            id="telephone_number" name="telephone_number" value="{{ $citizen->telephone_number }}">
                        @error('telephone_number')<div class="field-error">{{ $message }}</div>@enderror
                    </div>

                    <div>
                        <label class="field-label" for="country_of_citizenship">Country of Citizenship</label>
                        <select class="field-select @error('country_of_citizenship') is-invalid @enderror"
                            id="country_of_citizenship" name="country_of_citizenship">
                            <option disabled {{ !$citizen->country_of_citizenship ? 'selected' : '' }}>Select Country</option>
                            @foreach (['Ghana','Nigeria','Togo','South Africa','Burkina Faso','Mali','Côte d\'Ivoire','Senegal','Cameroon'] as $c)
                                <option value="{{ $c }}" {{ $citizen->country_of_citizenship == $c ? 'selected' : '' }}>{{ $c }}</option>
                            @endforeach
                        </select>
                        @error('country_of_citizenship')<div class="field-error">{{ $message }}</div>@enderror
                    </div>

                    <div>
                        <label class="field-label" for="customer_type">Customer Type</label>
                        <select class="field-select @error('customer_type') is-invalid @enderror" id="customer_type" name="customer_type">
                            <option disabled {{ !$citizen->customer_type ? 'selected' : '' }}>Select Type</option>
                            @foreach ($customerTypes as $ct)
                                <option value="{{ $ct->id }}" {{ old('customer_type', $citizen->customer_type) == $ct->id ? 'selected' : '' }}>
                                    {{ $ct->name }}
                                </option>
                            @endforeach
                        </select>
                        @error('customer_type')<div class="field-error">{{ $message }}</div>@enderror
                    </div>

                    <div>
                        <label class="field-label" for="Ghana_card_number">Ghana Card Number</label>
                        <input class="field-input" type="text" id="Ghana_card_number"
                            name="Ghana_card_number" value="{{ $citizen->Ghana_card_number }}">
                    </div>

                    <div>
                        <label class="field-label">Account Number</label>
                        <input class="field-input" type="text" value="{{ $citizen->account_number }}" readonly>
                    </div>

                    <div>
                        <label class="field-label" for="status">Status</label>
                        <select class="field-select @error('status') is-invalid @enderror" id="status" name="status">
                            <option value="Active"   {{ $citizen->status == 'Active'   ? 'selected' : '' }}>Active</option>
                            <option value="InActive" {{ $citizen->status == 'InActive' ? 'selected' : '' }}>In Active</option>
                        </select>
                        @error('status')<div class="field-error">{{ $message }}</div>@enderror
                    </div>

                    <div style="grid-column:1/-1; padding-top:.35rem;">
                        <button type="submit" class="btn-save">
                            <i class="bi bi-check2-circle"></i> Save Changes
                        </button>
                    </div>
                </div>
            </form>
        </div>
        @endif

    </div>

    {{-- RIGHT: Account card + quick stats --}}
    <div>
        <div class="panel">
            <div class="panel-head">
                <div class="panel-title"><i class="bi bi-credit-card-2-front-fill" style="color:var(--accent);"></i> Olives Account Card</div>
            </div>
            {{-- Visa-style card visual --}}
            <div style="perspective:900px;">
                <div style="width:100%; max-width:320px; height:190px; border-radius:18px; position:relative;
                            background:linear-gradient(130deg, #071a0d 0%, #0e3318 40%, var(--accent) 140%);
                            box-shadow:0 20px 50px rgba(26,92,42,.3), 0 6px 18px rgba(0,0,0,.2);">
                    <div style="position:absolute;inset:0;border-radius:18px;background-image:radial-gradient(circle,rgba(255,255,255,.05) 1px,transparent 1px);background-size:20px 20px;pointer-events:none;"></div>
                    <div style="position:absolute;inset:0;border-radius:18px;background:linear-gradient(105deg,transparent 30%,rgba(255,255,255,.06) 50%,transparent 70%);background-size:200% 100%;animation:shimmer 3.5s linear infinite;pointer-events:none;"></div>
                    <div style="position:relative;z-index:2;padding:1.15rem 1.35rem;height:100%;display:flex;flex-direction:column;justify-content:space-between;">
                        <div style="display:flex;justify-content:space-between;align-items:center;">
                            <div style="display:flex;align-items:center;gap:.35rem;">
                                <div style="width:26px;height:26px;border-radius:50% 8px 50% 8px;background:linear-gradient(135deg,rgba(76,199,106,.9),rgba(45,158,75,.8));display:grid;place-items:center;font-size:.7rem;box-shadow:0 0 10px rgba(76,199,106,.4);">🌿</div>
                                <span style="font-family:'Poppins',sans-serif;font-weight:900;font-size:.95rem;color:#fff;">Olives <em style="color:#4cc76a;font-style:normal;">BI</em></span>
                            </div>
                            <span style="font-family:'Poppins',sans-serif;font-weight:800;font-size:.58rem;text-transform:uppercase;letter-spacing:.5px;background:rgba(255,255,255,.12);border:1px solid rgba(255,255,255,.2);border-radius:999px;padding:.16rem .48rem;color:rgba(255,255,255,.88);">{{ ucfirst(\App\Support\OlivesAccountTier::fromUser(auth()->user())) }}</span>
                        </div>
                        <div>
                            <div style="width:34px;height:25px;border-radius:5px;background:linear-gradient(135deg,#d4af70,#f0d080,#c8a04a,#e8c96a);margin-bottom:.55rem;position:relative;overflow:hidden;">
                                <div style="position:absolute;inset:0;background:repeating-linear-gradient(90deg,rgba(0,0,0,.1) 0,rgba(0,0,0,.1) 1px,transparent 1px,transparent 8px),repeating-linear-gradient(0deg,rgba(0,0,0,.1) 0,rgba(0,0,0,.1) 1px,transparent 1px,transparent 8px);"></div>
                            </div>
                            <div style="font-family:'Space Mono',monospace;font-size:.8rem;letter-spacing:.18em;color:rgba(255,255,255,.85);font-weight:700;">{{ $accountCard }}</div>
                        </div>
                        <div style="display:flex;justify-content:space-between;align-items:flex-end;">
                            <div>
                                <div style="font-size:.52rem;text-transform:uppercase;letter-spacing:.5px;color:rgba(255,255,255,.4);margin-bottom:.12rem;">Account Holder</div>
                                <div style="font-size:.78rem;font-weight:700;color:#fff;text-transform:uppercase;letter-spacing:.04em;">{{ strtoupper(substr(optional($profileUser)->name ?? 'User', 0, 22)) }}</div>
                            </div>
                            <div style="text-align:right;">
                                <div style="font-size:.52rem;text-transform:uppercase;letter-spacing:.5px;color:rgba(255,255,255,.4);margin-bottom:.12rem;">Valid Thru</div>
                                <div style="font-family:'Space Mono',monospace;font-size:.72rem;color:rgba(255,255,255,.8);">{{ now()->format('m/y') }} — {{ now()->addYears(3)->format('m/y') }}</div>
                            </div>
                        </div>
                    </div>
                </div>
            </div>
            <p style="margin-top:.85rem; font-size:.75rem; color:var(--muted); line-height:1.55;">
                Use this card number with your password to sign in by card, email, or phone.
            </p>
        </div>

        {{-- Account stats --}}
        <div class="panel">
            <div class="panel-head">
                <div class="panel-title"><i class="bi bi-info-circle-fill" style="color:var(--accent);"></i> Account Summary</div>
            </div>
            <div style="display:flex; flex-direction:column; gap:.65rem;">
                <div class="profile-stat">
                    <div class="profile-stat-icon"><i class="bi bi-envelope-fill"></i></div>
                    <div>
                        <div class="profile-stat-label">Email</div>
                        <div class="profile-stat-val">{{ optional($profileUser)->email ?? '—' }}</div>
                    </div>
                </div>
                <div class="profile-stat">
                    <div class="profile-stat-icon"><i class="bi bi-telephone-fill"></i></div>
                    <div>
                        <div class="profile-stat-label">Phone</div>
                        <div class="profile-stat-val">{{ optional($profileUser)->phone ?? '—' }}</div>
                    </div>
                </div>
                <div class="profile-stat">
                    <div class="profile-stat-icon"><i class="bi bi-shield-fill-check"></i></div>
                    <div>
                        <div class="profile-stat-label">Access Level</div>
                        <div class="profile-stat-val">{{ optional($profileUser)->access_level ?? 'Standard' }}</div>
                    </div>
                </div>
                <div class="profile-stat">
                    <div class="profile-stat-icon"><i class="bi bi-circle-fill" style="font-size:.55rem;"></i></div>
                    <div>
                        <div class="profile-stat-label">Account Status</div>
                        <div class="profile-stat-val" style="color:#1a5c2a;">{{ optional($profileUser)->status ?? 'Active' }}</div>
                    </div>
                </div>
            </div>
        </div>
    </div>
</div>

@endsection

@push('page-scripts')
<style>
@keyframes shimmer {
    0%   { background-position: 200% 0; }
    100% { background-position: -200% 0; }
}
</style>
@endpush

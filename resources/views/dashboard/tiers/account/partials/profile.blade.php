@php
    $citizen = $accountData['citizen'] ?? null;
@endphp

<style>
    .info-display-grid { display: grid; grid-template-columns: repeat(2, minmax(0, 1fr)); gap: .65rem; }
    .info-display-card {
        background: var(--soft); border: 1px solid var(--line); border-radius: 12px;
        padding: 1rem; color: #17251b;
    }
    .info-display-icon { font-size: 1.4rem; color: var(--accent); margin-bottom: .5rem; }
    .info-display-title { font-family: 'Poppins', sans-serif; font-weight: 800; font-size: .85rem; margin-bottom: .25rem; }
    .info-display-desc { font-size: .77rem; color: var(--muted); line-height: 1.45; overflow-wrap: anywhere; }
    @media (max-width: 560px) { .info-display-grid { grid-template-columns: 1fr; } }
</style>

<div class="panel">
    <div class="panel-head">
        <div class="panel-title"><i class="bi bi-person-circle" style="color:var(--accent);"></i> My Profile</div>
        <a class="panel-action" href="{{ route('auth.change') }}">Change password</a>
    </div>

    @if (session('status'))
        <div style="background:#f0fdf4;border:1px solid #bbf7d0;color:#166534;border-radius:10px;padding:.75rem .9rem;font-size:.84rem;margin-bottom:1rem;">
            <i class="bi bi-check-circle-fill"></i> {{ session('status') }}
        </div>
    @endif

    <form method="POST" action="{{ route('dashboard.olives-account.profile.update') }}" style="background:linear-gradient(180deg,#f7fcf8,#fff);border:1px solid var(--line);border-radius:14px;padding:1rem;margin-bottom:1rem;">
        @csrf
        <div style="display:flex;justify-content:space-between;gap:1rem;align-items:flex-start;flex-wrap:wrap;margin-bottom:.8rem;">
            <div>
                <div style="font-size:.66rem;font-weight:800;text-transform:uppercase;letter-spacing:.5px;color:var(--accent);margin-bottom:.2rem;">Account holder</div>
                <div style="font-family:'Poppins',sans-serif;font-weight:800;color:var(--primary);">Update display name</div>
            </div>
            <span style="display:inline-flex;align-items:center;gap:.35rem;border-radius:999px;background:var(--soft);border:1px solid var(--line);padding:.26rem .6rem;font-size:.7rem;font-weight:800;color:var(--primary);">
                <i class="bi bi-shield-check"></i> Secure profile
            </span>
        </div>
        <label for="account_name" style="display:block;font-size:.7rem;font-weight:800;text-transform:uppercase;letter-spacing:.45px;color:var(--primary);margin-bottom:.35rem;">Account Name</label>
        <div style="display:grid;grid-template-columns:minmax(0,1fr) auto;gap:.65rem;align-items:start;">
            <div>
                <input id="account_name" name="name" type="text" value="{{ old('name', optional($user)->name) }}" style="width:100%;border:1px solid var(--line);border-radius:9px;padding:.74rem .82rem;font:inherit;color:#17251b;background:#fff;">
                @error('name')
                    <div style="color:#b91c1c;font-size:.76rem;margin-top:.35rem;">{{ $message }}</div>
                @enderror
            </div>
            <button type="submit" style="border:0;background:var(--accent);color:#fff;border-radius:9px;padding:.76rem 1rem;font-weight:800;cursor:pointer;white-space:nowrap;">
                <i class="bi bi-save"></i> Save name
            </button>
        </div>
    </form>

    <div class="info-display-grid">
        <div class="info-display-card">
            <div class="info-display-icon"><i class="bi bi-person-badge"></i></div>
            <div class="info-display-title">Account Name</div>
            <div class="info-display-desc">{{ optional($user)->name ?: '—' }}</div>
        </div>
        <div class="info-display-card">
            <div class="info-display-icon"><i class="bi bi-credit-card-2-front"></i></div>
            <div class="info-display-title">Olives Card Login</div>
            <div class="info-display-desc">{{ \App\Support\OlivesCardNumber::format(optional($user)->olives_card_number) }}</div>
        </div>
        <div class="info-display-card">
            <div class="info-display-icon"><i class="bi bi-envelope"></i></div>
            <div class="info-display-title">Email</div>
            <div class="info-display-desc">{{ optional($user)->email ?: 'No email on account' }}</div>
        </div>
        <div class="info-display-card">
            <div class="info-display-icon"><i class="bi bi-phone"></i></div>
            <div class="info-display-title">Phone</div>
            <div class="info-display-desc">{{ optional($user)->phone ?: 'No phone on account' }}</div>
        </div>
    </div>
</div>

<div class="panel">
    <div class="panel-head">
        <div class="panel-title"><i class="bi bi-file-person" style="color:var(--accent);"></i> Rate Payer Profile</div>
    </div>

    @if ($citizen)
        <div class="info-display-grid">
            <div class="info-display-card">
                <div class="info-display-title">Name</div>
                <div class="info-display-desc">{{ trim(($citizen->first_name ?? '').' '.($citizen->last_name ?? '')) ?: 'N/A' }}</div>
            </div>
            <div class="info-display-card">
                <div class="info-display-title">Account Number</div>
                <div class="info-display-desc">{{ $citizen->account_number ?? 'N/A' }}</div>
            </div>
            <div class="info-display-card">
                <div class="info-display-title">Nationality</div>
                <div class="info-display-desc">{{ $citizen->country_of_citizenship ?? 'N/A' }}</div>
            </div>
            <div class="info-display-card">
                <div class="info-display-title">Status</div>
                <div class="info-display-desc">{{ $citizen->status ?? 'N/A' }}</div>
            </div>
        </div>
    @else
        <div style="text-align:center;padding:2.4rem 1rem;color:var(--muted);">
            <i class="bi bi-person-lines-fill" style="font-size:2rem;display:block;margin-bottom:.6rem;opacity:.45;"></i>
            No rate-payer profile is linked to this account yet.
        </div>
    @endif
</div>

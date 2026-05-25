<div class="panel">
    <div class="panel-head">
        <div class="panel-title"><i class="bi bi-person-circle" style="color:var(--accent);"></i> Subscriber Profile</div>
        <a class="panel-action" href="{{ route('auth.change') }}">Change password</a>
    </div>

    @if (session('status'))
        <div style="background:#f0fdf4;border:1px solid #bbf7d0;color:#166534;border-radius:10px;padding:.75rem .9rem;font-size:.84rem;margin-bottom:1rem;">
            <i class="bi bi-check-circle-fill"></i> {{ session('status') }}
        </div>
    @endif

    <form method="POST" action="{{ route('dashboard.subscriber.profile.update') }}" style="background:var(--soft);border:1px solid var(--line);border-radius:14px;padding:1rem;margin-bottom:1rem;">
        @csrf
        <label for="subscriber_name" style="display:block;font-size:.7rem;font-weight:800;text-transform:uppercase;letter-spacing:.45px;color:var(--primary);margin-bottom:.35rem;">Account Name</label>
        <div style="display:flex;gap:.65rem;align-items:flex-start;flex-wrap:wrap;">
            <div style="flex:1;min-width:220px;">
                <input id="subscriber_name" name="name" type="text" value="{{ old('name', optional($user)->name) }}" style="width:100%;border:1px solid var(--line);border-radius:9px;padding:.7rem .8rem;font:inherit;color:#17251b;">
                @error('name')
                    <div style="color:#b91c1c;font-size:.76rem;margin-top:.35rem;">{{ $message }}</div>
                @enderror
            </div>
            <button type="submit" style="border:0;background:var(--accent);color:#fff;border-radius:9px;padding:.72rem 1rem;font-weight:800;cursor:pointer;">
                <i class="bi bi-save"></i> Save name
            </button>
        </div>
    </form>

    <div class="tool-grid">
        <div class="tool-card">
            <div class="tool-card-icon"><i class="bi bi-person-badge"></i></div>
            <div class="tool-card-title">Account Name</div>
            <div class="tool-card-desc">{{ optional($user)->name }}</div>
        </div>
        <div class="tool-card">
            <div class="tool-card-icon"><i class="bi bi-envelope"></i></div>
            <div class="tool-card-title">Email Login</div>
            <div class="tool-card-desc">{{ optional($user)->email ?: 'No email on account' }}</div>
        </div>
        <div class="tool-card">
            <div class="tool-card-icon"><i class="bi bi-phone"></i></div>
            <div class="tool-card-title">Phone Login</div>
            <div class="tool-card-desc">{{ optional($user)->phone ?: 'No phone on account' }}</div>
        </div>
        <div class="tool-card">
            <div class="tool-card-icon"><i class="bi bi-credit-card-2-front"></i></div>
            <div class="tool-card-title">Olives Card Login</div>
            <div class="tool-card-desc">{{ \App\Support\OlivesCardNumber::format(optional($user)->olives_card_number) }}</div>
        </div>
    </div>
</div>

<div class="panel">
    <div class="panel-head">
        <div class="panel-title"><i class="bi bi-shield-check" style="color:var(--accent);"></i> Access Summary</div>
    </div>
    <ul class="focus-list">
        <li class="focus-item"><i class="bi bi-check-circle-fill"></i><span>Status: {{ optional($user)->status }}</span></li>
        <li class="focus-item"><i class="bi bi-check-circle-fill"></i><span>Tier: Subscriber</span></li>
        <li class="focus-item"><i class="bi bi-check-circle-fill"></i><span>Login options: email, phone number, or Olives account card.</span></li>
        <li class="focus-item"><i class="bi bi-check-circle-fill"></i><span>Subscriber access covers environmental news, alerts, newsletter topics, and locality checks.</span></li>
    </ul>
</div>

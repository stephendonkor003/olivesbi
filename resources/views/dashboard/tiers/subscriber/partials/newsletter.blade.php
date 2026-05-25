@php
    $selectedTopics = collect(optional($subscription)->topics ?? [])->all();
@endphp

<div class="panel">
    <div class="panel-head">
        <div class="panel-title"><i class="bi bi-envelope-paper-fill" style="color:var(--accent);"></i> Newsletter Preferences</div>
    </div>

    @if (session('status'))
        <div style="background:#f0fdf4;border:1px solid #bbf7d0;color:#166534;border-radius:10px;padding:.75rem .9rem;font-size:.84rem;margin-bottom:1rem;">
            <i class="bi bi-check-circle-fill"></i> {{ session('status') }}
        </div>
    @endif

    @if ($errors->any())
        <div style="background:#fef2f2;border:1px solid #fecaca;color:#991b1b;border-radius:10px;padding:.75rem .9rem;font-size:.84rem;margin-bottom:1rem;">
            {{ $errors->first() }}
        </div>
    @endif

    <form method="POST" action="{{ route('dashboard.subscriber.newsletter.update') }}">
        @csrf
        <div class="tool-grid">
            @foreach ($topics ?? [] as $topic)
                <label class="tool-card" style="cursor:pointer;">
                    <div style="display:flex;gap:.65rem;align-items:flex-start;">
                        <input type="checkbox" name="topics[]" value="{{ $topic }}" @checked(in_array($topic, $selectedTopics, true)) style="margin-top:.2rem;accent-color:var(--accent);">
                        <div>
                            <div class="tool-card-title">{{ $topic }}</div>
                            <div class="tool-card-desc">Send updates when this topic appears in public environmental intelligence.</div>
                        </div>
                    </div>
                </label>
            @endforeach
        </div>

        <button type="submit" style="margin-top:1rem;border:0;background:var(--accent);color:#fff;border-radius:9px;padding:.7rem 1rem;font-weight:800;cursor:pointer;">
            <i class="bi bi-save"></i> Save newsletter preferences
        </button>
    </form>
</div>

<div class="panel">
    <div class="panel-head">
        <div class="panel-title"><i class="bi bi-person-lines-fill" style="color:var(--accent);"></i> Delivery Account</div>
    </div>
    <div class="info-list">
        <div class="info-item">
            <div class="info-dot"></div>
            <div>
                <div class="info-text"><strong>Name:</strong> {{ optional($user)->name }}</div>
                <div class="info-time">Subscriber profile</div>
            </div>
        </div>
        <div class="info-item" style="border:0;">
            <div class="info-dot"></div>
            <div>
                <div class="info-text"><strong>Email:</strong> {{ optional($user)->email ?: 'No email on account' }}</div>
                <div class="info-time">Used for newsletter delivery</div>
            </div>
        </div>
    </div>
</div>

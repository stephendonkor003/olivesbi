<div class="panel">
    <div class="panel-head">
        <div class="panel-title"><i class="bi bi-newspaper" style="color:var(--accent);"></i> Environment News Desk</div>
        <a class="panel-action" href="{{ route('dashboard.subscriber.locality') }}">Research locality</a>
    </div>

    <div class="info-list">
        @foreach ($newsItems ?? [] as $item)
            <div class="info-item">
                <div class="info-dot"></div>
                <div>
                    <div style="display:flex;gap:.45rem;align-items:center;flex-wrap:wrap;margin-bottom:.2rem;">
                        <span style="font-size:.66rem;font-weight:800;text-transform:uppercase;letter-spacing:.45px;color:var(--accent);">{{ $item['label'] }}</span>
                        <span class="info-time">{{ $item['time'] }}</span>
                    </div>
                    <div style="font-family:'Poppins',sans-serif;font-size:.92rem;font-weight:800;color:#17251b;margin-bottom:.2rem;">{{ $item['title'] }}</div>
                    <div class="info-text">{{ $item['text'] }}</div>
                </div>
            </div>
        @endforeach
    </div>
</div>

<div class="panel">
    <div class="panel-head">
        <div class="panel-title"><i class="bi bi-compass" style="color:var(--accent);"></i> Subscriber Reading Flow</div>
    </div>
    <ul class="focus-list">
        <li class="focus-item"><i class="bi bi-check-circle-fill"></i><span>Read environmental summaries that connect news, public data, and locality risk.</span></li>
        <li class="focus-item"><i class="bi bi-check-circle-fill"></i><span>Move from any topic into the locality checker for coordinates, area search, and map context.</span></li>
        <li class="focus-item"><i class="bi bi-check-circle-fill"></i><span>Use newsletter topics to decide which briefs should reach your email inbox.</span></li>
    </ul>
</div>

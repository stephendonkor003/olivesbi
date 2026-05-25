@php
    $properties = $accountData['properties'] ?? collect();
    $propertyRequests = $accountData['propertyRequests'] ?? collect();
    $propertyCurrencies = $accountData['propertyCurrencies'] ?? collect();
    $propertyCurrencyTotals = collect($accountData['propertyCurrencyTotals'] ?? []);
    $totals = $accountData['totals'] ?? [];
    $money = fn ($currency, $amount) => trim(($currency['symbol'] ?? $currency['code'] ?? 'GHS').' '.number_format((float) $amount, 2));
@endphp

<div class="panel">
    <div class="panel-head">
        <div class="panel-title"><i class="bi bi-houses-fill" style="color:var(--accent);"></i> My Properties</div>
        <div style="display:flex;gap:.75rem;flex-wrap:wrap;">
            <a class="panel-action" href="{{ route('dashboard.olives-account.properties.map') }}"><i class="bi bi-map"></i> Add from map</a>
            <a class="panel-action" href="{{ route('dashboard.olives-account.locality') }}">Check locality</a>
        </div>
    </div>

    <div class="tool-grid">
        <div class="tool-card">
            <div class="tool-card-icon"><i class="bi bi-building-check"></i></div>
            <div class="tool-card-title">{{ number_format($properties->count()) }} linked properties</div>
            <div class="tool-card-desc">Only records connected to your account are shown here.</div>
        </div>
        <div class="tool-card">
            <div class="tool-card-icon"><i class="bi bi-map"></i></div>
            <div class="tool-card-title">{{ number_format($propertyRequests->where('status', 'pending')->count()) }} pending map requests</div>
            <div class="tool-card-desc">Map selections waiting for assembly or municipality confirmation.</div>
        </div>
        @forelse ($propertyCurrencyTotals as $currencyTotal)
            <div class="tool-card">
                <div class="tool-card-icon"><i class="bi bi-cash-stack"></i></div>
                <div class="tool-card-title">{{ $money($currencyTotal, $currencyTotal['property_value'] ?? 0) }}</div>
                <div class="tool-card-desc">{{ $currencyTotal['code'] ?? 'GHS' }} rateable value from {{ $currencyTotal['source'] ?? 'the property assembly' }}.</div>
            </div>
        @empty
        @endforelse
    </div>
</div>

@if ($propertyRequests->isNotEmpty())
<div class="panel">
    <div class="panel-head">
        <div class="panel-title"><i class="bi bi-map" style="color:var(--accent);"></i> Map Property Requests</div>
        <a class="panel-action" href="{{ route('dashboard.olives-account.properties.map') }}">New request</a>
    </div>

    <div class="info-list">
        @foreach ($propertyRequests as $requestItem)
            @php
                $statusColor = match ($requestItem->status) {
                    'approved' => '#1a5c2a',
                    'rejected' => '#b91c1c',
                    default => '#92400e',
                };
            @endphp
            <div class="info-item">
                <div class="info-dot" style="background:{{ $statusColor }};"></div>
                <div style="width:100%;">
                    <div style="display:flex;justify-content:space-between;gap:1rem;flex-wrap:wrap;">
                        <div>
                            <div style="font-family:'Poppins',sans-serif;font-weight:800;color:#17251b;">{{ $requestItem->tracking_code }}</div>
                            <div class="info-text">{{ $requestItem->parcel_name ?: $requestItem->address_line ?: $requestItem->display_name }}</div>
                            <div class="info-time">{{ \App\Support\GlobalLocation::regionFor($requestItem) }}</div>
                        </div>
                        <div style="text-align:right;">
                            <div style="font-weight:800;color:{{ $statusColor }};">{{ ucfirst($requestItem->status) }}</div>
                            <div class="info-time">{{ $requestItem->assembly->name ?? 'Assembly not selected' }}</div>
                        </div>
                    </div>
                    <div style="margin-top:.55rem;display:flex;gap:.75rem;flex-wrap:wrap;">
                        <span class="info-time">Lat: {{ $requestItem->latitude ?? 'N/A' }}</span>
                        <span class="info-time">Lng: {{ $requestItem->longitude ?? 'N/A' }}</span>
                        <span class="info-time">Submitted: {{ optional($requestItem->created_at)->format('d M Y') }}</span>
                    </div>
                </div>
            </div>
        @endforeach
    </div>
</div>
@endif

<div class="panel">
    <div class="panel-head">
        <div class="panel-title"><i class="bi bi-list-ul" style="color:var(--accent);"></i> Property Records</div>
    </div>

    <div class="info-list">
        @forelse ($properties as $property)
            @php
                $currency = $propertyCurrencies[$property->uuid] ?? ['code' => 'GHS', 'symbol' => 'GHS'];
            @endphp
            <div class="info-item">
                <div class="info-dot"></div>
                <div style="width:100%;">
                    <div style="display:flex;justify-content:space-between;gap:1rem;flex-wrap:wrap;">
                        <div>
                            <div style="font-family:'Poppins',sans-serif;font-weight:800;color:#17251b;">{{ $property->account_no ?? $property->property_number ?? 'Property '.$property->uuid }}</div>
                            <div class="info-text">{{ \App\Support\GlobalLocation::addressFor($property) }}</div>
                            <div class="info-time">{{ \App\Support\GlobalLocation::regionFor($property) }}</div>
                        </div>
                        <div style="text-align:right;">
                            <div style="font-weight:800;color:var(--primary);">{{ $money($currency, $property->ratable_value) }}</div>
                            <div class="info-time">{{ $property->assembly->name ?? 'Assembly not recorded' }}</div>
                        </div>
                    </div>
                    <div style="margin-top:.55rem;">
                        <a class="panel-action" href="{{ route('dashboard.olives-account.properties.show', $property) }}"><i class="bi bi-eye"></i> View property</a>
                    </div>
                </div>
            </div>
        @empty
            <div style="text-align:center;padding:2.4rem 1rem;color:var(--muted);">
                <i class="bi bi-houses" style="font-size:2rem;display:block;margin-bottom:.6rem;opacity:.45;"></i>
                No property records are linked to your account yet.
            </div>
        @endforelse
    </div>
</div>

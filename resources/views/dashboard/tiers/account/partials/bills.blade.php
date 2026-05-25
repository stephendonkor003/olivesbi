@php
    $bills = $accountData['bills'] ?? collect();
    $billCurrencies = $accountData['billCurrencies'] ?? collect();
    $billCurrencyTotals = collect($accountData['billCurrencyTotals'] ?? []);
    $totals = $accountData['totals'] ?? [];
    $money = fn ($currency, $amount) => trim(($currency['symbol'] ?? $currency['code'] ?? 'GHS').' '.number_format((float) $amount, 2));
@endphp

<div class="panel">
    <div class="panel-head">
        <div class="panel-title"><i class="bi bi-receipt-cutoff" style="color:var(--accent);"></i> Bills & Levies</div>
        <a class="panel-action" href="{{ route('dashboard.olives-account.payments') }}">Payment history</a>
    </div>

    <div class="tool-grid">
        <div class="tool-card">
            <div class="tool-card-icon"><i class="bi bi-receipt"></i></div>
            <div class="tool-card-title">{{ number_format($bills->count()) }} bill records</div>
            <div class="tool-card-desc">All visible bills are linked to your own properties or businesses.</div>
        </div>
        @forelse ($billCurrencyTotals as $currencyTotal)
            <div class="tool-card">
                <div class="tool-card-icon"><i class="bi bi-wallet2"></i></div>
                <div class="tool-card-title">{{ $money($currencyTotal, $currencyTotal['due'] ?? 0) }}</div>
                <div class="tool-card-desc">Total due in {{ $currencyTotal['code'] ?? 'GHS' }} from {{ $currencyTotal['source'] ?? 'the issuing assembly' }}.</div>
            </div>
            <div class="tool-card">
                <div class="tool-card-icon"><i class="bi bi-exclamation-triangle"></i></div>
                <div class="tool-card-title">{{ $money($currencyTotal, $currencyTotal['arrears'] ?? 0) }}</div>
                <div class="tool-card-desc">Arrears in {{ $currencyTotal['code'] ?? 'GHS' }} for {{ number_format($currencyTotal['count'] ?? 0) }} bill records.</div>
            </div>
        @empty
            <div class="tool-card">
                <div class="tool-card-icon"><i class="bi bi-wallet2"></i></div>
                <div class="tool-card-title">No bill currency yet</div>
                <div class="tool-card-desc">Currency appears once a municipality or district issues a bill.</div>
            </div>
        @endforelse
    </div>
</div>

<div class="panel">
    <div class="panel-head">
        <div class="panel-title"><i class="bi bi-list-ul" style="color:var(--accent);"></i> Bill Records</div>
    </div>

    <div class="info-list">
        @forelse ($bills as $bill)
            @php
                $assetName = optional($bill->property)->account_no
                    ?: optional($bill->business)->business_name
                    ?: 'Linked asset';
                $type = $bill->property_id ? 'Property' : 'Business';
                $totalDue = (float) $bill->amount + (float) $bill->arrears;
                $currency = $billCurrencies[$bill->uuid] ?? ['code' => 'GHS', 'symbol' => 'GHS', 'source' => 'Default currency'];
            @endphp
            <div class="info-item">
                <div class="info-dot"></div>
                <div style="width:100%;">
                    <div style="display:flex;justify-content:space-between;gap:1rem;flex-wrap:wrap;">
                        <div>
                            <div style="font-family:'Poppins',sans-serif;font-weight:800;color:#17251b;">{{ $bill->bills_id }}</div>
                            <div class="info-text">{{ $type }} - {{ $assetName }}</div>
                        </div>
                        <div style="text-align:right;">
                            <div style="font-weight:800;color:#b91c1c;">{{ $money($currency, $totalDue) }}</div>
                            <div class="info-time">{{ $currency['code'] ?? 'GHS' }} from {{ $currency['source'] ?? 'issuing assembly' }}</div>
                        </div>
                    </div>
                    <div style="margin-top:.55rem;display:flex;gap:.75rem;flex-wrap:wrap;">
                        <span class="info-time">Year {{ $bill->bills_year }}</span>
                        <span class="info-time">Amount: {{ $money($currency, $bill->amount) }}</span>
                        <span class="info-time">Arrears: {{ $money($currency, $bill->arrears) }}</span>
                        <a class="panel-action" href="{{ route('dashboard.olives-account.bills.show', $bill) }}"><i class="bi bi-eye"></i> View bill</a>
                    </div>
                </div>
            </div>
        @empty
            <div style="text-align:center;padding:2.4rem 1rem;color:var(--muted);">
                <i class="bi bi-receipt" style="font-size:2rem;display:block;margin-bottom:.6rem;opacity:.45;"></i>
                No bills are linked to your account yet.
            </div>
        @endforelse
    </div>
</div>

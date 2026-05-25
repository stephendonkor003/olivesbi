@php
    $payments = $accountData['payments'] ?? collect();
    $paymentCurrencies = $accountData['paymentCurrencies'] ?? collect();
    $paymentCurrencyTotals = collect($accountData['paymentCurrencyTotals'] ?? []);
    $totals = $accountData['totals'] ?? [];
    $money = fn ($currency, $amount) => trim(($currency['symbol'] ?? $currency['code'] ?? 'GHS').' '.number_format((float) $amount, 2));
@endphp

<div class="panel">
    <div class="panel-head">
        <div class="panel-title"><i class="bi bi-credit-card-2-front-fill" style="color:var(--accent);"></i> Payment History</div>
        <a class="panel-action" href="{{ route('dashboard.olives-account.bills') }}">View bills</a>
    </div>

    <div class="tool-grid">
        <div class="tool-card">
            <div class="tool-card-icon"><i class="bi bi-cash-coin"></i></div>
            <div class="tool-card-title">{{ number_format($payments->count()) }} payment records</div>
            <div class="tool-card-desc">Payments attached to bills for your own properties and businesses.</div>
        </div>
        @forelse ($paymentCurrencyTotals as $currencyTotal)
            <div class="tool-card">
                <div class="tool-card-icon"><i class="bi bi-check-circle"></i></div>
                <div class="tool-card-title">{{ $money($currencyTotal, $currencyTotal['paid'] ?? 0) }}</div>
                <div class="tool-card-desc">Successful payments in {{ $currencyTotal['code'] ?? 'GHS' }} from {{ $currencyTotal['source'] ?? 'the issuing assembly' }}.</div>
            </div>
        @empty
            <div class="tool-card">
                <div class="tool-card-icon"><i class="bi bi-check-circle"></i></div>
                <div class="tool-card-title">No payments yet</div>
                <div class="tool-card-desc">Payment currency appears from the bill's issuing municipality or district.</div>
            </div>
        @endforelse
    </div>
</div>

<div class="panel">
    <div class="panel-head">
        <div class="panel-title"><i class="bi bi-clock-history" style="color:var(--accent);"></i> Payment Records</div>
    </div>

    <div class="info-list">
        @forelse ($payments as $payment)
            @php
                $status = strtolower((string) $payment->transaction_status);
                $statusColor = $status === 'success' ? '#1a5c2a' : ($status === 'failed' ? '#b91c1c' : '#92400e');
                $currency = $paymentCurrencies[$payment->uuid] ?? ['code' => 'GHS', 'symbol' => 'GHS', 'source' => 'Default currency'];
            @endphp
            <div class="info-item">
                <div class="info-dot" style="background:{{ $statusColor }};"></div>
                <div style="width:100%;">
                    <div style="display:flex;justify-content:space-between;gap:1rem;flex-wrap:wrap;">
                        <div>
                            <div style="font-family:'Poppins',sans-serif;font-weight:800;color:#17251b;">{{ $payment->transaction_id ?? $payment->req_transaction_id ?? $payment->bills_id }}</div>
                            <div class="info-text">Bill reference: {{ $payment->bills_id }}</div>
                        </div>
                        <div style="text-align:right;">
                            <div style="font-weight:800;color:var(--primary);">{{ $money($currency, $payment->amount) }}</div>
                            <div class="info-time">{{ $payment->transaction_status ?? 'Pending' }} - {{ $currency['code'] ?? 'GHS' }}</div>
                        </div>
                    </div>
                    <div style="margin-top:.55rem;display:flex;gap:.75rem;flex-wrap:wrap;">
                        <span class="info-time">Mode: {{ $payment->payment_mode ?? 'N/A' }}</span>
                        <span class="info-time">Date: {{ optional($payment->created_at)->format('d M Y') ?: 'N/A' }}</span>
                        <span class="info-time">Currency source: {{ $currency['source'] ?? 'issuing assembly' }}</span>
                        <a class="panel-action" href="{{ route('dashboard.olives-account.payments.show', $payment) }}"><i class="bi bi-eye"></i> View payment</a>
                    </div>
                </div>
            </div>
        @empty
            <div style="text-align:center;padding:2.4rem 1rem;color:var(--muted);">
                <i class="bi bi-credit-card" style="font-size:2rem;display:block;margin-bottom:.6rem;opacity:.45;"></i>
                No payments are linked to your account yet.
            </div>
        @endforelse
    </div>
</div>

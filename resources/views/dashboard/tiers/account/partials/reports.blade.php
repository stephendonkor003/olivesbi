@php
    $reportPage = $reportPage ?? 'index';
    $bills = $accountData['bills'] ?? collect();
    $payments = $accountData['payments'] ?? collect();
    $wasteBookings = $accountData['wasteBookings'] ?? collect();
    $auditLogs = $auditLogs ?? collect();
    $billCurrencies = $accountData['billCurrencies'] ?? collect();
    $paymentCurrencies = $accountData['paymentCurrencies'] ?? collect();

    $wastePickups = $wasteBookings
        ->flatMap(function ($booking) {
            return $booking->pickups->map(function ($pickup) use ($booking) {
                $pickup->setRelation('booking', $booking);
                return $pickup;
            });
        })
        ->sortByDesc('scheduled_for')
        ->values();

    $money = fn ($currency, $amount) => trim(($currency['symbol'] ?? $currency['code'] ?? 'GHS').' '.number_format((float) $amount, 2));
    $billTotal = $bills->sum(fn ($bill) => (float) $bill->amount + (float) $bill->arrears - (float) $bill->discount_amount);
    $paidTotal = $payments->filter(fn ($payment) => strtolower((string) $payment->transaction_status) === 'success')->sum(fn ($payment) => (float) $payment->amount);
    $successfulPayments = $payments->filter(fn ($payment) => strtolower((string) $payment->transaction_status) === 'success')->count();
    $wasteKg = $wastePickups->where('status', 'completed')->sum(fn ($pickup) => (float) $pickup->waste_amount_kg);
    $missedPickups = $wastePickups->where('status', 'missed')->count();

    $statusClass = fn ($status) => match (strtolower((string) $status)) {
        'success', 'completed', 'active', 'paid' => 'good',
        'failed', 'missed' => 'danger',
        'pending', 'due', 'scheduled' => 'warn',
        default => 'muted',
    };

    $reportLinks = [
        [
            'key' => 'bills',
            'icon' => 'bi-receipt-cutoff',
            'title' => 'Bill Report',
            'subtitle' => number_format($bills->count()).' linked bill records and arrears summary.',
            'url' => route('dashboard.olives-account.reports.bills'),
        ],
        [
            'key' => 'payments',
            'icon' => 'bi-credit-card-2-front',
            'title' => 'Payment Report',
            'subtitle' => number_format($payments->count()).' payment records and receipt links.',
            'url' => route('dashboard.olives-account.reports.payments'),
        ],
        [
            'key' => 'waste',
            'icon' => 'bi-recycle',
            'title' => 'Waste Report',
            'subtitle' => number_format($wastePickups->count()).' pickup records and waste generated.',
            'url' => route('dashboard.olives-account.reports.waste'),
        ],
        [
            'key' => 'audit',
            'icon' => 'bi-shield-check',
            'title' => 'Audit Trail',
            'subtitle' => number_format($auditLogs->count()).' recent account activity records.',
            'url' => route('dashboard.olives-account.reports.audit'),
        ],
    ];
@endphp

<style>
    .reports-page { display:flex; flex-direction:column; gap:1rem; }
    .reports-menu { display:grid; grid-template-columns:repeat(4,minmax(0,1fr)); gap:.75rem; }
    .report-link {
        display:flex; align-items:flex-start; gap:.75rem; background:#fff; border:1px solid var(--line);
        border-radius:14px; padding:1rem; text-decoration:none; color:#17251b;
        box-shadow:0 1px 8px rgba(26,92,42,.04); min-height:104px;
        transition:border-color .18s, box-shadow .18s, transform .14s;
    }
    .report-link:hover { border-color:rgba(45,158,75,.38); box-shadow:0 6px 20px rgba(26,92,42,.09); transform:translateY(-1px); }
    .report-link.active { background:#fbfefc; border-color:rgba(45,158,75,.38); box-shadow:0 6px 20px rgba(26,92,42,.09); }
    .report-icon { width:40px; height:40px; border-radius:11px; display:grid; place-items:center; background:var(--soft); color:var(--primary); font-size:1.1rem; flex:0 0 auto; }
    .report-link-title { font-family:'Poppins',sans-serif; font-weight:800; color:var(--primary); font-size:.9rem; }
    .report-link-sub { color:var(--muted); font-size:.76rem; line-height:1.45; margin-top:.2rem; }
    .report-panel { background:#fff; border:1px solid var(--line); border-radius:14px; padding:1rem; box-shadow:0 1px 8px rgba(26,92,42,.04); }
    .report-head { display:flex; justify-content:space-between; align-items:flex-start; gap:1rem; margin-bottom:1rem; }
    .report-title { font-family:'Poppins',sans-serif; font-weight:800; color:var(--primary); display:flex; align-items:center; gap:.45rem; }
    .report-sub { color:var(--muted); font-size:.77rem; margin-top:.2rem; line-height:1.45; }
    .report-actions { display:flex; gap:.45rem; flex-wrap:wrap; justify-content:flex-end; }
    .report-btn {
        display:inline-flex; align-items:center; justify-content:center; gap:.35rem; border:1px solid var(--line);
        border-radius:9px; padding:.5rem .7rem; color:var(--primary); background:var(--soft);
        text-decoration:none; font-weight:900; font-size:.75rem; cursor:pointer;
        transition:background .18s, box-shadow .18s, transform .14s;
    }
    .report-btn:hover { background:#fff; box-shadow:0 2px 8px rgba(26,92,42,.1); transform:translateY(-1px); color:var(--primary); }
    .report-summary { display:grid; grid-template-columns:repeat(4,minmax(0,1fr)); gap:.65rem; margin-bottom:1rem; }
    .report-stat { border:1px solid var(--line); border-radius:12px; background:#fbfefc; padding:.8rem; }
    .report-stat-label { color:var(--muted); font-size:.64rem; font-weight:900; text-transform:uppercase; letter-spacing:.45px; margin-bottom:.25rem; }
    .report-stat-value { font-family:'Poppins',sans-serif; font-weight:800; color:#17251b; font-size:1rem; line-height:1.25; }
    .report-table-wrap { overflow:auto; }
    .report-table { width:100%; border-collapse:collapse; min-width:760px; }
    .report-table th, .report-table td { border-bottom:1px solid var(--line); padding:.72rem .55rem; text-align:left; vertical-align:top; font-size:.8rem; }
    .report-table th { color:var(--primary); font-size:.66rem; font-weight:900; text-transform:uppercase; letter-spacing:.45px; background:#fbfefc; }
    .report-table strong { color:#17251b; }
    .report-muted { color:var(--muted); font-size:.74rem; line-height:1.4; }
    .report-pill { display:inline-flex; border-radius:999px; padding:.2rem .55rem; font-size:.66rem; font-weight:900; text-transform:uppercase; white-space:nowrap; }
    .report-pill.good { background:#dcfce7; color:#166534; }
    .report-pill.warn { background:#fff7ed; color:#92400e; }
    .report-pill.danger { background:#fee2e2; color:#991b1b; }
    .report-pill.muted { background:#f3f4f6; color:#4b5563; }
    .audit-list { display:grid; gap:.55rem; }
    .audit-item { display:grid; grid-template-columns:auto minmax(0,1fr) auto; gap:.65rem; align-items:flex-start; border:1px solid var(--line); border-radius:12px; padding:.75rem; background:#fbfefc; }
    .audit-dot { width:10px; height:10px; border-radius:50%; background:var(--accent); margin-top:.35rem; }
    .empty-report { border:1px dashed rgba(26,92,42,.22); border-radius:12px; padding:1.25rem; text-align:center; color:var(--muted); background:#fbfefc; }
    @media (max-width:1100px) {
        .reports-menu, .report-summary { grid-template-columns:repeat(2,minmax(0,1fr)); }
    }
    @media (max-width:640px) {
        .reports-menu, .report-summary, .audit-item { grid-template-columns:1fr; }
        .report-head { flex-direction:column; }
        .report-actions, .report-btn { width:100%; }
    }
</style>

<div class="reports-page">
    <div class="reports-menu">
        @foreach ($reportLinks as $link)
            <a class="report-link {{ $reportPage === $link['key'] ? 'active' : '' }}" href="{{ $link['url'] }}">
                <span class="report-icon"><i class="bi {{ $link['icon'] }}"></i></span>
                <span>
                    <span class="report-link-title">{{ $link['title'] }}</span>
                    <span class="report-link-sub">{{ $link['subtitle'] }}</span>
                </span>
            </a>
        @endforeach
    </div>

    @if ($reportPage === 'index')
        <section class="report-panel">
            <div class="report-head">
                <div>
                    <div class="report-title"><i class="bi bi-file-earmark-bar-graph" style="color:var(--accent);"></i> Account Reports</div>
                    <div class="report-sub">Select one report above to view it on its own page.</div>
                </div>
            </div>
            <div class="report-summary">
                <div class="report-stat"><div class="report-stat-label">Bills</div><div class="report-stat-value">{{ number_format($bills->count()) }}</div></div>
                <div class="report-stat"><div class="report-stat-label">Payments</div><div class="report-stat-value">{{ number_format($payments->count()) }}</div></div>
                <div class="report-stat"><div class="report-stat-label">Waste Pickups</div><div class="report-stat-value">{{ number_format($wastePickups->count()) }}</div></div>
                <div class="report-stat"><div class="report-stat-label">Audit Logs</div><div class="report-stat-value">{{ number_format($auditLogs->count()) }}</div></div>
            </div>
        </section>
    @endif

    @if ($reportPage === 'bills')
        <section class="report-panel">
            <div class="report-head">
                <div>
                    <div class="report-title"><i class="bi bi-receipt-cutoff" style="color:var(--accent);"></i> Bill Report</div>
                    <div class="report-sub">Bills shown here are linked only to your properties and businesses.</div>
                </div>
                <div class="report-actions">
                    <a class="report-btn" href="{{ route('dashboard.olives-account.bills') }}"><i class="bi bi-eye"></i> Open bills</a>
                </div>
            </div>
            <div class="report-summary">
                <div class="report-stat"><div class="report-stat-label">Bills</div><div class="report-stat-value">{{ number_format($bills->count()) }}</div></div>
                <div class="report-stat"><div class="report-stat-label">Total Due</div><div class="report-stat-value">{{ number_format($billTotal, 2) }}</div></div>
                <div class="report-stat"><div class="report-stat-label">Arrears</div><div class="report-stat-value">{{ number_format($bills->sum(fn ($bill) => (float) $bill->arrears), 2) }}</div></div>
                <div class="report-stat"><div class="report-stat-label">Bill Years</div><div class="report-stat-value">{{ $bills->pluck('bills_year')->filter()->unique()->count() }}</div></div>
            </div>
            <div class="report-table-wrap">
                <table class="report-table">
                    <thead><tr><th>Bill</th><th>Asset</th><th>Year</th><th>Amount</th><th>Arrears</th><th>Actions</th></tr></thead>
                    <tbody>
                        @forelse ($bills->take(25) as $bill)
                            @php
                                $currency = $billCurrencies[$bill->uuid] ?? ['code' => 'GHS', 'symbol' => 'GHS'];
                                $assetName = optional($bill->property)->account_no ?: optional($bill->business)->business_name ?: 'Linked asset';
                            @endphp
                            <tr>
                                <td><strong>{{ $bill->bills_id }}</strong><div class="report-muted">{{ $currency['code'] ?? 'GHS' }}</div></td>
                                <td>{{ $assetName }}</td>
                                <td>{{ $bill->bills_year ?: 'N/A' }}</td>
                                <td>{{ $money($currency, $bill->amount) }}</td>
                                <td>{{ $money($currency, $bill->arrears) }}</td>
                                <td>
                                    <a class="report-btn" href="{{ route('dashboard.olives-account.bills.show', $bill) }}"><i class="bi bi-eye"></i> View</a>
                                    <a class="report-btn" href="{{ route('dashboard.olives-account.bills.invoice.download', $bill) }}"><i class="bi bi-download"></i> PDF</a>
                                </td>
                            </tr>
                        @empty
                            <tr><td colspan="6"><div class="empty-report">No bills are linked to your account yet.</div></td></tr>
                        @endforelse
                    </tbody>
                </table>
            </div>
        </section>
    @endif

    @if ($reportPage === 'payments')
        <section class="report-panel">
            <div class="report-head">
                <div>
                    <div class="report-title"><i class="bi bi-credit-card-2-front" style="color:var(--accent);"></i> Payment Report</div>
                    <div class="report-sub">Successful, pending, and failed payment records attached to your bills.</div>
                </div>
                <div class="report-actions">
                    <a class="report-btn" href="{{ route('dashboard.olives-account.payments') }}"><i class="bi bi-eye"></i> Open payments</a>
                </div>
            </div>
            <div class="report-summary">
                <div class="report-stat"><div class="report-stat-label">Payments</div><div class="report-stat-value">{{ number_format($payments->count()) }}</div></div>
                <div class="report-stat"><div class="report-stat-label">Successful Paid</div><div class="report-stat-value">{{ number_format($paidTotal, 2) }}</div></div>
                <div class="report-stat"><div class="report-stat-label">Successful Count</div><div class="report-stat-value">{{ number_format($successfulPayments) }}</div></div>
                <div class="report-stat"><div class="report-stat-label">Pending or Failed</div><div class="report-stat-value">{{ number_format($payments->count() - $successfulPayments) }}</div></div>
            </div>
            <div class="report-table-wrap">
                <table class="report-table">
                    <thead><tr><th>Transaction</th><th>Bill</th><th>Mode</th><th>Amount</th><th>Status</th><th>Actions</th></tr></thead>
                    <tbody>
                        @forelse ($payments->take(25) as $payment)
                            @php
                                $currency = $paymentCurrencies[$payment->uuid] ?? ['code' => 'GHS', 'symbol' => 'GHS'];
                                $status = $payment->transaction_status ?: 'Pending';
                            @endphp
                            <tr>
                                <td><strong>{{ $payment->transaction_id ?? $payment->req_transaction_id ?? $payment->bills_id }}</strong><div class="report-muted">{{ optional($payment->created_at)->format('d M Y H:i') ?: 'N/A' }}</div></td>
                                <td>{{ $payment->bills_id }}</td>
                                <td>{{ $payment->payment_mode ?: 'N/A' }}</td>
                                <td>{{ $money($currency, $payment->amount) }}</td>
                                <td><span class="report-pill {{ $statusClass($status) }}">{{ $status }}</span></td>
                                <td>
                                    <a class="report-btn" href="{{ route('dashboard.olives-account.payments.show', $payment) }}"><i class="bi bi-eye"></i> View</a>
                                    <a class="report-btn" href="{{ route('dashboard.olives-account.payments.receipt.download', $payment) }}"><i class="bi bi-download"></i> PDF</a>
                                </td>
                            </tr>
                        @empty
                            <tr><td colspan="6"><div class="empty-report">No payments are linked to your account yet.</div></td></tr>
                        @endforelse
                    </tbody>
                </table>
            </div>
        </section>
    @endif

    @if ($reportPage === 'waste')
        <section class="report-panel">
            <div class="report-head">
                <div>
                    <div class="report-title"><i class="bi bi-recycle" style="color:var(--accent);"></i> Waste Pickup Report</div>
                    <div class="report-sub">Pickup history, provider activity, missed collections, and waste generated.</div>
                </div>
                <div class="report-actions">
                    <a class="report-btn" href="{{ route('dashboard.olives-account.waste') }}"><i class="bi bi-truck"></i> Manage waste</a>
                </div>
            </div>
            <div class="report-summary">
                <div class="report-stat"><div class="report-stat-label">Bookings</div><div class="report-stat-value">{{ number_format($wasteBookings->count()) }}</div></div>
                <div class="report-stat"><div class="report-stat-label">Pickups</div><div class="report-stat-value">{{ number_format($wastePickups->count()) }}</div></div>
                <div class="report-stat"><div class="report-stat-label">Waste KG</div><div class="report-stat-value">{{ number_format($wasteKg, 2) }}</div></div>
                <div class="report-stat"><div class="report-stat-label">Missed</div><div class="report-stat-value">{{ number_format($missedPickups) }}</div></div>
            </div>
            <div class="report-table-wrap">
                <table class="report-table">
                    <thead><tr><th>Schedule</th><th>Asset</th><th>Provider</th><th>Frequency</th><th>Waste KG</th><th>Status</th></tr></thead>
                    <tbody>
                        @forelse ($wastePickups->take(25) as $pickup)
                            @php
                                $booking = $pickup->booking;
                                $asset = $booking?->property ?: $booking?->business;
                                $assetName = $asset?->account_no ?: ($asset?->business_name ?: ($asset?->erms_id ?: 'Linked asset'));
                            @endphp
                            <tr>
                                <td><strong>{{ $pickup->scheduled_for?->format('d M Y H:i') ?: 'N/A' }}</strong></td>
                                <td>{{ $assetName }}</td>
                                <td>{{ $pickup->company->name ?? $booking?->company?->name ?? 'N/A' }}</td>
                                <td>{{ ucfirst($booking?->frequency ?: 'N/A') }}</td>
                                <td>{{ $pickup->waste_amount_kg ? number_format((float) $pickup->waste_amount_kg, 2).' kg' : 'Pending' }}</td>
                                <td><span class="report-pill {{ $statusClass($pickup->status) }}">{{ $pickup->status }}</span></td>
                            </tr>
                        @empty
                            <tr><td colspan="6"><div class="empty-report">No waste pickup records are linked to your account yet.</div></td></tr>
                        @endforelse
                    </tbody>
                </table>
            </div>
        </section>
    @endif

    @if ($reportPage === 'audit')
        <section class="report-panel">
            <div class="report-head">
                <div>
                    <div class="report-title"><i class="bi bi-shield-check" style="color:var(--accent);"></i> Audit Trail</div>
                    <div class="report-sub">Recent account activity recorded for your signed-in user profile.</div>
                </div>
            </div>
            <div class="audit-list">
                @forelse ($auditLogs as $audit)
                    <div class="audit-item">
                        <span class="audit-dot"></span>
                        <div>
                            <strong>{{ $audit->action_performed ?: 'Activity recorded' }}</strong>
                            <div class="report-muted">{{ $audit->remarks ?: 'No remarks recorded' }}</div>
                        </div>
                        <div class="report-muted" style="text-align:right;">
                            {{ $audit->action_date ?: optional($audit->created_at)->format('d M Y H:i') }}<br>
                            {{ $audit->ip_address ?: 'No IP' }} {{ $audit->device_used ? '- '.$audit->device_used : '' }}
                        </div>
                    </div>
                @empty
                    <div class="empty-report">No audit activity has been recorded for your account yet.</div>
                @endforelse
            </div>
        </section>
    @endif
</div>

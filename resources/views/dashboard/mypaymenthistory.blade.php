@extends('dashboard.tiers.partials.tier-page-layout')

@section('page-title', 'Payment History')
@section('page-scope', 'Payment History')

@php
    $customerData = array_merge([
        'payments' => collect(),
        'paymentTotal' => '0.00',
    ], $customerData ?? []);
@endphp

@section('page-head-extra')
<style>
    .data-table { width:100%; border-collapse:collapse; }
    .data-table thead th {
        font-size:.67rem; font-weight:800; text-transform:uppercase; letter-spacing:.5px;
        color:var(--primary); background:#f8fdf9; padding:.65rem .9rem;
        border-bottom:2px solid var(--line); white-space:nowrap;
    }
    .data-table tbody td {
        padding:.65rem .9rem; font-size:.84rem; color:#374151;
        border-bottom:1px solid var(--line); vertical-align:middle;
    }
    .data-table tbody tr:hover { background:#f8fdf9; }
    .data-table tbody tr:last-child td { border-bottom:0; }
    .data-table tfoot th {
        font-size:.75rem; font-weight:700; color:var(--primary);
        background:#f8fdf9; padding:.65rem .9rem; border-top:2px solid var(--line);
    }
    .tbl-search {
        border:1.5px solid rgba(26,92,42,.18); border-radius:9px;
        height:38px; padding:0 .85rem; font-size:.84rem; color:#1a2e1e;
        outline:none; width:260px; transition:border .2s;
    }
    .tbl-search:focus { border-color:var(--accent); box-shadow:0 0 0 3px rgba(76,199,106,.1); }
    .tbl-badge {
        display:inline-flex; align-items:center; gap:.3rem;
        font-size:.7rem; font-weight:700; padding:.18rem .55rem; border-radius:6px;
        background:var(--soft); color:var(--primary);
    }
    .tbl-link {
        color:var(--primary); font-weight:700; font-size:.8rem;
        text-decoration:none; display:inline-flex; align-items:center; gap:.25rem;
        padding:.22rem .6rem; border-radius:6px; background:var(--soft);
        transition:background .15s;
    }
    .tbl-link:hover { background:#d4edda; color:var(--primary); }
    .summary-grid { display:grid; grid-template-columns:repeat(3,1fr); gap:.85rem; margin-bottom:1.25rem; }
    .sum-card {
        background:#fff; border:1px solid var(--line); border-radius:14px;
        padding:1rem 1.1rem; box-shadow:0 1px 8px rgba(26,92,42,.04);
    }
    .sum-label { font-size:.67rem; font-weight:800; text-transform:uppercase; letter-spacing:.5px; color:var(--muted); margin-bottom:.35rem; }
    .sum-value { font-family:'Poppins',sans-serif; font-weight:800; font-size:1.2rem; color:#17251b; line-height:1; }
    .sum-sub   { font-size:.72rem; color:var(--muted); margin-top:.25rem; }
    .status-pill {
        display:inline-block; font-size:.68rem; font-weight:700; padding:.18rem .55rem;
        border-radius:6px; text-transform:capitalize;
    }
    .status-pill.completed, .status-pill.success, .status-pill.paid {
        background:#e8f5e9; color:#1a5c2a;
    }
    .status-pill.pending { background:#fff8e1; color:#7d4e00; }
    .status-pill.failed  { background:#fce4ec; color:#880e4f; }
    .empty-state { text-align:center; padding:3rem 1rem; color:var(--muted); }
    .empty-state i { font-size:2.5rem; margin-bottom:.75rem; display:block; opacity:.35; }
    .table-wrap { overflow-x:auto; }
    @media (max-width:820px) { .summary-grid { grid-template-columns:repeat(2,1fr); } }
</style>
@endsection

@section('page-content')

{{-- Hero --}}
<div class="tier-hero">
    <div>
        <div class="hero-badge"><i class="bi bi-clock-history"></i> My Account</div>
        <div class="hero-title">Payment History</div>
        <div class="hero-sub">All payments made on your Olives BI account across properties and businesses.</div>
    </div>
    <div style="text-align:right; white-space:nowrap;">
        <div style="font-size:.68rem;text-transform:uppercase;letter-spacing:.5px;color:rgba(255,255,255,.4);margin-bottom:.2rem;">Total Paid</div>
        <div style="font-size:1.1rem; font-weight:800; color:#fff;">GHS {{ $customerData['paymentTotal'] ?? '0.00' }}</div>
    </div>
</div>

@if (auth()->check())

{{-- Summary cards --}}
<div class="summary-grid">
    <div class="sum-card">
        <div class="sum-label">Total Payments</div>
        <div class="sum-value">{{ count($customerData['payments']) }}</div>
        <div class="sum-sub">Payment records</div>
    </div>
    <div class="sum-card">
        <div class="sum-label">Total Paid</div>
        <div class="sum-value" style="color:#1a5c2a;">GHS {{ $customerData['paymentTotal'] }}</div>
        <div class="sum-sub">Confirmed payments</div>
    </div>
    <div class="sum-card">
        <div class="sum-label">View Bills</div>
        <div class="sum-value" style="font-size:.88rem;">
            <a href="{{ route('dashboard.mybills') }}" style="color:var(--accent); text-decoration:none;">Outstanding Bills →</a>
        </div>
        <div class="sum-sub">Check what's due</div>
    </div>
</div>

{{-- Table panel --}}
<div class="panel" style="padding:0; overflow:hidden;">
    <div style="display:flex; align-items:center; justify-content:space-between; padding:1rem 1.25rem; border-bottom:1px solid var(--line);">
        <div class="panel-title" style="margin:0;"><i class="bi bi-cash-coin" style="color:var(--accent);"></i> Payment Records</div>
        <input class="tbl-search" type="text" id="pay-search" placeholder="Search payments…">
    </div>
    <div class="table-wrap">
        <table class="data-table" id="pay-table">
            <thead>
                <tr>
                    <th>#</th>
                    <th>Bill Ref.</th>
                    <th>Name</th>
                    <th>Amount (GHS)</th>
                    <th>Mode</th>
                    <th>Status</th>
                    <th>Assembly</th>
                    <th>Recorded By</th>
                    <th>Date</th>
                    <th>Action</th>
                </tr>
            </thead>
            <tbody>
            @forelse ($customerData['payments'] as $index => $payment)
                @php
                    $name = '';
                    if ($payment->bill->property && $payment->bill->property->customer) {
                        $name = trim(($payment->bill->property->customer->first_name ?? '') . ' ' . ($payment->bill->property->customer->last_name ?? ''));
                    } elseif ($payment->bill->business && $payment->bill->business->customer) {
                        $name = trim(($payment->bill->business->customer->first_name ?? '') . ' ' . ($payment->bill->business->customer->last_name ?? ''));
                    }
                    $statusClass = strtolower(str_replace(' ', '-', $payment->transaction_status ?? 'pending'));
                @endphp
                <tr>
                    <td>{{ $index + 1 }}</td>
                    <td><span class="tbl-badge"><i class="bi bi-hash"></i> {{ $payment->bills_id }}</span></td>
                    <td>{{ $name ?: 'N/A' }}</td>
                    <td style="font-weight:700; color:#1a5c2a;">{{ number_format($payment->amount, 2) }}</td>
                    <td>{{ $payment->payment_mode }}</td>
                    <td><span class="status-pill {{ $statusClass }}">{{ $payment->transaction_status }}</span></td>
                    <td>{{ $payment->assembly->name ?? 'N/A' }}</td>
                    <td style="color:var(--muted);">{{ $payment->createdBy->name ?? 'N/A' }}</td>
                    <td style="color:var(--muted); font-size:.78rem;">{{ optional($payment->created_at)->format('d M Y') }}</td>
                    <td>
                        <a class="tbl-link" href="{{ route('citizens.viewPayment', $payment) }}">
                            <i class="bi bi-eye"></i> View
                        </a>
                    </td>
                </tr>
            @empty
                <tr><td colspan="10"><div class="empty-state"><i class="bi bi-clock-history"></i><p>No payment records found on your account.</p></div></td></tr>
            @endforelse
            </tbody>
            <tfoot>
                <tr>
                    <th colspan="3" style="text-align:right; color:var(--muted);">Total Paid (GHS)</th>
                    <th>{{ $customerData['paymentTotal'] }}</th>
                    <th colspan="6"></th>
                </tr>
            </tfoot>
        </table>
    </div>
</div>

@elseif (false)
    <div class="panel" style="text-align:center; padding:3rem;">
        <i class="bi bi-lock-fill" style="font-size:2rem; color:var(--muted); display:block; margin-bottom:.75rem;"></i>
        <p style="color:var(--muted); font-size:.88rem;">You do not have permission to view this section.</p>
        <a href="{{ route('dashboard.entry') }}" style="color:var(--accent); font-weight:700; font-size:.85rem;">← Back to Dashboard</a>
    </div>
@endif

@endsection

@push('page-scripts')
<script>
    document.getElementById('pay-search').addEventListener('input', function () {
        const q = this.value.toLowerCase();
        document.querySelectorAll('#pay-table tbody tr').forEach(row => {
            row.style.display = row.textContent.toLowerCase().includes(q) ? '' : 'none';
        });
    });
</script>
@endpush

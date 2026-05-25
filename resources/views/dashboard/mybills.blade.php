@extends('dashboard.tiers.partials.tier-page-layout')

@section('page-title', 'My Bills')
@section('page-scope', 'My Bills & Arrears')

@php
    $customerData = array_merge([
        'bills' => collect(),
        'totalArrears' => '0.00',
        'totalAmount' => '0.00',
        'totalDue' => '0.00',
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
    .summary-grid { display:grid; grid-template-columns:repeat(4,1fr); gap:.85rem; margin-bottom:1.25rem; }
    .sum-card {
        background:#fff; border:1px solid var(--line); border-radius:14px;
        padding:1rem 1.1rem; box-shadow:0 1px 8px rgba(26,92,42,.04);
    }
    .sum-label { font-size:.67rem; font-weight:800; text-transform:uppercase; letter-spacing:.5px; color:var(--muted); margin-bottom:.35rem; }
    .sum-value { font-family:'Poppins',sans-serif; font-weight:800; font-size:1.2rem; color:#17251b; line-height:1; }
    .sum-sub   { font-size:.72rem; color:var(--muted); margin-top:.25rem; }
    .amount-due { color:#c0392b; font-weight:700; }
    .amount-arrears { color:#b97c05; font-weight:700; }
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
        <div class="hero-badge"><i class="bi bi-receipt"></i> My Account</div>
        <div class="hero-title">My Bills & Arrears</div>
        <div class="hero-sub">All outstanding bills, arrears, and due amounts on your account.</div>
    </div>
    <div style="text-align:right; white-space:nowrap;">
        <div style="font-size:.68rem;text-transform:uppercase;letter-spacing:.5px;color:rgba(255,255,255,.4);margin-bottom:.2rem;">Total Due</div>
        <div style="font-size:1.1rem; font-weight:800; color:#fff;">GHS {{ $customerData['totalDue'] ?? '0.00' }}</div>
    </div>
</div>

@if (auth()->check())

{{-- Summary cards --}}
<div class="summary-grid">
    <div class="sum-card">
        <div class="sum-label">Total Bills</div>
        <div class="sum-value">{{ count($customerData['bills']) }}</div>
        <div class="sum-sub">Bill records</div>
    </div>
    <div class="sum-card">
        <div class="sum-label">Total Arrears</div>
        <div class="sum-value amount-arrears">GHS {{ $customerData['totalArrears'] }}</div>
        <div class="sum-sub">Overdue balance</div>
    </div>
    <div class="sum-card">
        <div class="sum-label">Current Amount</div>
        <div class="sum-value">GHS {{ $customerData['totalAmount'] }}</div>
        <div class="sum-sub">Current charges</div>
    </div>
    <div class="sum-card">
        <div class="sum-label">Total Due</div>
        <div class="sum-value amount-due">GHS {{ $customerData['totalDue'] }}</div>
        <div class="sum-sub">Arrears + Current</div>
    </div>
</div>

{{-- Table panel --}}
<div class="panel" style="padding:0; overflow:hidden;">
    <div style="display:flex; align-items:center; justify-content:space-between; padding:1rem 1.25rem; border-bottom:1px solid var(--line);">
        <div class="panel-title" style="margin:0;"><i class="bi bi-receipt-cutoff" style="color:var(--accent);"></i> Bill Records</div>
        <input class="tbl-search" type="text" id="bill-search" placeholder="Search bills…">
    </div>
    <div class="table-wrap">
        <table class="data-table" id="bill-table">
            <thead>
                <tr>
                    <th>#</th>
                    <th>Bill No.</th>
                    <th>Name</th>
                    <th>Type</th>
                    <th>Arrears (GHS)</th>
                    <th>Amount (GHS)</th>
                    <th>Total Due (GHS)</th>
                    <th>Date</th>
                    <th>Action</th>
                </tr>
            </thead>
            <tbody>
            @forelse ($customerData['bills'] as $index => $bill)
                @php
                    if ($bill->property_id !== null) {
                        $fname    = $bill->property->customer->first_name ?? '';
                        $lname    = $bill->property->customer->last_name  ?? '';
                        $billType = 'Property';
                    } else {
                        $fname    = $bill->business->customer->first_name ?? '';
                        $lname    = $bill->business->customer->last_name  ?? '';
                        $billType = 'Business';
                    }
                    $name = trim($fname . ' ' . $lname) ?: 'N/A';
                @endphp
                <tr>
                    <td>{{ $index + 1 }}</td>
                    <td><span class="tbl-badge"><i class="bi bi-hash"></i> {{ $bill->bills_id }}</span></td>
                    <td>{{ $name }}</td>
                    <td><span style="font-size:.7rem; font-weight:700; padding:.15rem .45rem; border-radius:5px;
                        background:{{ $billType === 'Property' ? '#e8f5e9' : '#e3f2fd' }};
                        color:{{ $billType === 'Property' ? '#1a5c2a' : '#0d47a1' }};">{{ $billType }}</span></td>
                    <td class="amount-arrears">{{ number_format($bill->arrears, 2) }}</td>
                    <td>{{ number_format($bill->amount, 2) }}</td>
                    <td class="amount-due">{{ number_format($bill->amount + $bill->arrears, 2) }}</td>
                    <td style="color:var(--muted); font-size:.78rem;">{{ optional($bill->created_at)->format('d M Y') }}</td>
                    <td>
                        <a class="tbl-link" href="{{ route('citizens.viewBill', $bill) }}">
                            <i class="bi bi-eye"></i> View
                        </a>
                    </td>
                </tr>
            @empty
                <tr><td colspan="9"><div class="empty-state"><i class="bi bi-receipt"></i><p>No bills found on your account.</p></div></td></tr>
            @endforelse
            </tbody>
            <tfoot>
                <tr>
                    <th colspan="4" style="text-align:right; color:var(--muted);">Totals (GHS)</th>
                    <th>{{ $customerData['totalArrears'] }}</th>
                    <th>{{ $customerData['totalAmount'] }}</th>
                    <th>{{ $customerData['totalDue'] }}</th>
                    <th colspan="2"></th>
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
    document.getElementById('bill-search').addEventListener('input', function () {
        const q = this.value.toLowerCase();
        document.querySelectorAll('#bill-table tbody tr').forEach(row => {
            row.style.display = row.textContent.toLowerCase().includes(q) ? '' : 'none';
        });
    });
</script>
@endpush

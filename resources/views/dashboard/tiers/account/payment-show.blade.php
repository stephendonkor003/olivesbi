@extends('dashboard.tiers.partials.tier-page-layout')

@php
    $bill = $payment->bill;
    $asset = $bill?->property ?: $bill?->business;
    $assetType = $bill?->property_id ? 'Property Payment' : 'Business Payment';
    $assembly = $payment->assembly ?: $bill?->assembly ?: $bill?->property?->assembly ?: $bill?->business?->assembly;
    $currencyCode = strtoupper(trim((string) ($assembly?->currency_code ?: 'GHS')));
    $currencySymbol = trim((string) ($assembly?->currency_symbol ?: $currencyCode));
    $money = fn ($amount) => trim(($currencySymbol ?: $currencyCode).' '.number_format((float) $amount, 2));
    $assetRef = $bill?->property
        ? ($bill->property->account_no ?: ($bill->property->erms_id ?: 'Property '.$bill->property->uuid))
        : ($bill?->business?->account_no ?: ($bill?->business?->business_name ?: 'Linked business'));
    $owner = $asset?->customer;
    $ownerName = trim(($owner->fname ?? '').' '.($owner->lname ?? ''))
        ?: ($owner->organization_name ?? 'Linked owner');
    $assetAddress = $asset ? \App\Support\GlobalLocation::addressFor($asset) : 'No address recorded';
    $assetGlobalRegion = $asset ? \App\Support\GlobalLocation::regionFor($asset) : 'Global location not recorded';
    $transactionRef = $payment->transaction_id ?: ($payment->req_transaction_id ?: 'Payment '.$payment->uuid);
    $status = $payment->transaction_status ?: 'Pending';
    $statusKey = strtolower((string) $status);
    $statusColor = $statusKey === 'success' ? 'var(--primary)' : ($statusKey === 'failed' ? '#b91c1c' : '#92400e');
    $billTotal = $bill ? max(0, (float) $bill->amount + (float) $bill->arrears - (float) $bill->discount_amount) : 0;
@endphp

@section('page-title', 'View Payment')
@section('page-scope', 'Payment Details')

@section('page-head-extra')
<style>
    .payment-hero {
        border-radius: 18px;
        padding: 1.35rem 1.5rem;
        background: linear-gradient(135deg, var(--primary) 0%, var(--accent) 100%);
        color: #fff;
        margin-bottom: 1.25rem;
        display: flex;
        justify-content: space-between;
        align-items: flex-end;
        gap: 1rem;
    }
    .hero-badge-lite {
        display: inline-flex;
        align-items: center;
        gap: .4rem;
        border-radius: 999px;
        border: 1px solid rgba(255,255,255,.25);
        background: rgba(255,255,255,.14);
        padding: .28rem .75rem;
        font-size: .72rem;
        font-weight: 800;
        text-transform: uppercase;
        letter-spacing: .4px;
        margin-bottom: .65rem;
    }
    .payment-hero-title {
        font-family: 'Poppins', sans-serif;
        font-weight: 800;
        font-size: clamp(1.3rem, 2.3vw, 1.8rem);
        line-height: 1.2;
        margin-bottom: .35rem;
    }
    .payment-hero-sub {
        color: rgba(255,255,255,.82);
        font-size: .88rem;
        line-height: 1.6;
    }
    .payment-detail-grid {
        display: grid;
        grid-template-columns: minmax(0, 1.25fr) minmax(300px, .75fr);
        gap: 1rem;
        align-items: start;
    }
    .detail-card {
        background: #fff;
        border: 1px solid var(--line);
        border-radius: 14px;
        box-shadow: 0 1px 8px rgba(26,92,42,.04);
        overflow: hidden;
    }
    .detail-card-head {
        display: flex;
        align-items: center;
        justify-content: space-between;
        gap: 1rem;
        padding: 1rem 1.1rem;
        border-bottom: 1px solid var(--line);
    }
    .detail-title {
        font-family: 'Poppins', sans-serif;
        font-weight: 800;
        color: var(--primary);
        font-size: .95rem;
        margin: 0;
    }
    .detail-body { padding: 1rem 1.1rem; }
    .detail-fields {
        display: grid;
        grid-template-columns: repeat(2, minmax(0, 1fr));
        gap: .75rem;
    }
    .detail-field {
        border: 1px solid var(--line);
        border-radius: 10px;
        background: #fbfefc;
        padding: .8rem .85rem;
        min-height: 78px;
    }
    .detail-label {
        font-size: .66rem;
        font-weight: 800;
        text-transform: uppercase;
        letter-spacing: .45px;
        color: var(--muted);
        margin-bottom: .28rem;
    }
    .detail-value {
        color: #17251b;
        font-weight: 600;
        line-height: 1.45;
        overflow-wrap: anywhere;
    }
    .metric-stack {
        display: grid;
        gap: .7rem;
    }
    .metric-card {
        border: 1px solid var(--line);
        border-radius: 12px;
        background: #fff;
        padding: .9rem;
    }
    .metric-label {
        color: var(--muted);
        font-size: .68rem;
        font-weight: 800;
        letter-spacing: .45px;
        text-transform: uppercase;
        margin-bottom: .35rem;
    }
    .metric-value {
        font-family: 'Poppins', sans-serif;
        color: #17251b;
        font-weight: 800;
        font-size: 1.2rem;
        line-height: 1.1;
    }
    /* action-link base styles inherited from tier-page-layout */
    .detail-card { transition: box-shadow .2s; }
    .detail-card:hover { box-shadow: 0 4px 18px rgba(26,92,42,.08); }
    .status-pill {
        display: inline-flex;
        align-items: center;
        gap: .35rem;
        border-radius: 999px;
        border: 1px solid var(--line);
        background: var(--soft);
        font-size: .72rem;
        font-weight: 900;
        padding: .28rem .65rem;
    }
    .account-doc-dialog {
        width: min(860px, calc(100vw - 2rem));
        max-height: 88vh;
        border: 0;
        border-radius: 14px;
        padding: 0;
        box-shadow: 0 24px 70px rgba(7,26,13,.32);
    }
    .account-doc-dialog::backdrop {
        background: rgba(7,26,13,.5);
    }
    .account-doc-modal-head {
        display: flex;
        justify-content: space-between;
        align-items: center;
        gap: 1rem;
        padding: .8rem 1rem;
        border-bottom: 1px solid var(--line);
        background: #fff;
        position: sticky;
        top: 0;
        z-index: 2;
    }
    .account-doc-modal-body {
        padding: 1rem;
        overflow: auto;
        max-height: calc(88vh - 62px);
        background: #f1f7f3;
    }
    .account-doc-close {
        width: 36px;
        height: 36px;
        border-radius: 9px;
        border: 1px solid var(--line);
        background: #fff;
        color: var(--primary);
        cursor: pointer;
    }
    @media (max-width: 1050px) {
        .payment-detail-grid,
        .detail-fields {
            grid-template-columns: 1fr;
        }
    }
    @media (max-width: 620px) {
        .payment-hero {
            flex-direction: column;
            align-items: flex-start;
        }
    }
</style>
@endsection

@section('page-content')
<div class="payment-hero">
    <div>
        <div class="hero-badge-lite"><i class="bi bi-credit-card-2-front"></i> {{ $assetType }}</div>
        <div class="payment-hero-title">{{ $transactionRef }}</div>
        <div class="payment-hero-sub">Bill {{ $payment->bills_id }} - {{ $assetRef }}<br>{{ $assetAddress }}<br>{{ $assetGlobalRegion }}</div>
    </div>
    <div style="text-align:right;white-space:nowrap;">
        <div style="font-size:.68rem;text-transform:uppercase;letter-spacing:.5px;color:rgba(255,255,255,.55);margin-bottom:.2rem;">Amount Paid</div>
        <div style="font-family:'Poppins',sans-serif;font-weight:800;font-size:1.2rem;">{{ $money($payment->amount) }}</div>
    </div>
</div>

<div style="display:flex;gap:.6rem;flex-wrap:wrap;margin-bottom:1rem;">
    <a class="action-link" href="{{ route('dashboard.olives-account.payments') }}"><i class="bi bi-arrow-left"></i> Back to payments</a>
    <button class="action-link primary" type="button" data-account-doc-open="paymentReceiptModal"><i class="bi bi-file-earmark-text"></i> View receipt</button>
    <a class="action-link" href="{{ route('dashboard.olives-account.payments.receipt.download', $payment) }}"><i class="bi bi-download"></i> Download PDF</a>
    @if ($bill)
        <a class="action-link" href="{{ route('dashboard.olives-account.bills.show', $bill) }}"><i class="bi bi-receipt"></i> View bill</a>
    @endif
    @if ($bill?->property)
        <a class="action-link" href="{{ route('dashboard.olives-account.properties.show', $bill->property) }}"><i class="bi bi-house"></i> View property</a>
    @endif
    @if ($bill?->business)
        <a class="action-link" href="{{ route('dashboard.olives-account.businesses.show', $bill->business) }}"><i class="bi bi-shop"></i> View business</a>
    @endif
</div>

<dialog class="account-doc-dialog" id="paymentReceiptModal">
    <div class="account-doc-modal-head">
        <div class="detail-title"><i class="bi bi-file-earmark-text" style="color:var(--accent);"></i> Receipt Preview</div>
        <button class="account-doc-close" type="button" data-account-doc-close="paymentReceiptModal" aria-label="Close receipt preview"><i class="bi bi-x-lg"></i></button>
    </div>
    <div class="account-doc-modal-body">
        @include('dashboard.tiers.account.documents.payment-receipt', ['payment' => $payment])
    </div>
</dialog>

<div class="payment-detail-grid">
    <div style="display:flex;flex-direction:column;gap:1rem;">
        <div class="detail-card">
            <div class="detail-card-head">
                <h2 class="detail-title"><i class="bi bi-info-circle-fill" style="color:var(--accent);"></i> Payment Information</h2>
                <span class="status-pill" style="color:{{ $statusColor }};"><i class="bi bi-circle-fill"></i> {{ $status }}</span>
            </div>
            <div class="detail-body">
                <div class="detail-fields">
                    <div class="detail-field">
                        <div class="detail-label">Transaction ID</div>
                        <div class="detail-value">{{ $payment->transaction_id ?: 'N/A' }}</div>
                    </div>
                    <div class="detail-field">
                        <div class="detail-label">Request Reference</div>
                        <div class="detail-value">{{ $payment->req_transaction_id ?: 'N/A' }}</div>
                    </div>
                    <div class="detail-field">
                        <div class="detail-label">Bill Number</div>
                        <div class="detail-value">{{ $payment->bills_id ?: 'N/A' }}</div>
                    </div>
                    <div class="detail-field">
                        <div class="detail-label">Payment Date</div>
                        <div class="detail-value">{{ optional($payment->created_at)->format('d M Y, H:i') ?: 'N/A' }}</div>
                    </div>
                    <div class="detail-field">
                        <div class="detail-label">Payment Mode</div>
                        <div class="detail-value">{{ $payment->payment_mode ?: 'N/A' }}</div>
                    </div>
                    <div class="detail-field">
                        <div class="detail-label">Payment Source</div>
                        <div class="detail-value">{{ $payment->payment_source ?: 'N/A' }}</div>
                    </div>
                    <div class="detail-field">
                        <div class="detail-label">Phone</div>
                        <div class="detail-value">{{ $payment->phone ?: 'N/A' }}</div>
                    </div>
                    <div class="detail-field">
                        <div class="detail-label">Network</div>
                        <div class="detail-value">{{ $payment->network ?: 'N/A' }}</div>
                    </div>
                    <div class="detail-field">
                        <div class="detail-label">Receipt</div>
                        <div class="detail-value">{{ $payment->receipt ?: 'N/A' }}</div>
                    </div>
                    <div class="detail-field">
                        <div class="detail-label">Assembly</div>
                        <div class="detail-value">{{ $assembly->name ?? 'N/A' }}</div>
                    </div>
                </div>
            </div>
        </div>

        <div class="detail-card">
            <div class="detail-card-head">
                <h2 class="detail-title"><i class="bi bi-link-45deg" style="color:var(--accent);"></i> Linked Record</h2>
            </div>
            <div class="detail-body">
                <div class="detail-fields">
                    <div class="detail-field">
                        <div class="detail-label">Payment Type</div>
                        <div class="detail-value">{{ $assetType }}</div>
                    </div>
                    <div class="detail-field">
                        <div class="detail-label">Linked Asset</div>
                        <div class="detail-value">{{ $assetRef }}</div>
                    </div>
                    <div class="detail-field">
                        <div class="detail-label">Owner</div>
                        <div class="detail-value">{{ $ownerName }}</div>
                    </div>
                    <div class="detail-field">
                        <div class="detail-label">Asset Address</div>
                        <div class="detail-value">{{ $assetAddress }}<br>{{ $assetGlobalRegion }}</div>
                    </div>
                </div>
            </div>
        </div>
    </div>

    <div style="display:flex;flex-direction:column;gap:1rem;">
        <div class="detail-card">
            <div class="detail-card-head">
                <h2 class="detail-title"><i class="bi bi-speedometer2" style="color:var(--accent);"></i> Summary</h2>
            </div>
            <div class="detail-body">
                <div class="metric-stack">
                    <div class="metric-card">
                        <div class="metric-label">Payment Amount</div>
                        <div class="metric-value" style="color:var(--primary);">{{ $money($payment->amount) }}</div>
                    </div>
                    <div class="metric-card">
                        <div class="metric-label">Bill Total</div>
                        <div class="metric-value">{{ $money($billTotal) }}</div>
                    </div>
                    <div class="metric-card">
                        <div class="metric-label">Status</div>
                        <div class="metric-value" style="font-size:1rem;color:{{ $statusColor }};">{{ $status }}</div>
                    </div>
                    <div class="metric-card">
                        <div class="metric-label">Currency Source</div>
                        <div class="metric-value" style="font-size:1rem;">{{ $currencyCode }} - {{ $assembly->name ?? 'Default currency' }}</div>
                    </div>
                </div>
            </div>
        </div>

        <div class="detail-card">
            <div class="detail-card-head">
                <h2 class="detail-title"><i class="bi bi-chat-square-text" style="color:var(--accent);"></i> Prompt / Notes</h2>
            </div>
            <div class="detail-body">
                <div style="border:1px solid var(--line);border-radius:12px;background:#fbfefc;padding:.9rem;color:#374151;line-height:1.55;">
                    {{ $payment->prompt ?: 'No payment prompt or note was recorded.' }}
                </div>
            </div>
        </div>
    </div>
</div>
@endsection

@push('page-scripts')
<script>
    document.querySelectorAll('[data-account-doc-open]').forEach((button) => {
        button.addEventListener('click', () => {
            const dialog = document.getElementById(button.dataset.accountDocOpen);
            if (dialog && typeof dialog.showModal === 'function') {
                dialog.showModal();
            }
        });
    });

    document.querySelectorAll('[data-account-doc-close]').forEach((button) => {
        button.addEventListener('click', () => {
            document.getElementById(button.dataset.accountDocClose)?.close();
        });
    });
</script>
@endpush

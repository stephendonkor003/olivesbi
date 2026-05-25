@extends('dashboard.tiers.partials.tier-page-layout')

@php
    $asset = $bill->property ?: $bill->business;
    $assetType = $bill->property_id ? 'Property Bill' : 'Business Bill';
    $assembly = $bill->assembly ?: $bill->property?->assembly ?: $bill->business?->assembly;
    $currencyCode = strtoupper(trim((string) ($assembly?->currency_code ?: 'GHS')));
    $currencySymbol = trim((string) ($assembly?->currency_symbol ?: $currencyCode));
    $money = fn ($amount) => trim(($currencySymbol ?: $currencyCode).' '.number_format((float) $amount, 2));
    $currentAmount = (float) $bill->amount;
    $arrears = (float) $bill->arrears;
    $discount = (float) $bill->discount_amount;
    $totalDue = max(0, $currentAmount + $arrears - $discount);
    $paid = $bill->successfulPayments->sum('amount');
    $balance = max(0, $totalDue - $paid);
    $assetRef = $bill->property
        ? ($bill->property->account_no ?: ($bill->property->erms_id ?: 'Property '.$bill->property->uuid))
        : ($bill->business?->account_no ?: ($bill->business?->business_name ?: 'Linked business'));
    $owner = $asset?->customer;
    $ownerName = trim(($owner->fname ?? '').' '.($owner->lname ?? ''))
        ?: ($owner->organization_name ?? 'Linked owner');
    $assetAddress = $asset ? \App\Support\GlobalLocation::addressFor($asset) : 'No address recorded';
    $assetGlobalRegion = $asset ? \App\Support\GlobalLocation::regionFor($asset) : 'Global location not recorded';
    $status = $balance <= 0 ? 'Settled' : ($paid > 0 ? 'Part paid' : 'Outstanding');
    $statusColor = $balance <= 0 ? 'var(--primary)' : ($paid > 0 ? '#92400e' : '#b91c1c');
    $paymentPageUrl = route('admin.public.bills.show', $bill);
    $paymentAmount = number_format($balance, 2, '.', '');
@endphp

@section('page-title', 'View Bill')
@section('page-scope', 'Bill Details')

@section('page-head-extra')
<style>
    .bill-hero {
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
    .bill-hero-title {
        font-family: 'Poppins', sans-serif;
        font-weight: 800;
        font-size: clamp(1.3rem, 2.3vw, 1.8rem);
        line-height: 1.2;
        margin-bottom: .35rem;
    }
    .bill-hero-sub {
        color: rgba(255,255,255,.82);
        font-size: .88rem;
        line-height: 1.6;
    }
    .bill-detail-grid {
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
    .payment-row {
        display: flex;
        justify-content: space-between;
        gap: 1rem;
        padding: .75rem 0;
        border-bottom: 1px solid var(--line);
    }
    .payment-row:last-child { border-bottom: 0; }
    .account-doc-dialog {
        width: min(920px, calc(100vw - 2rem));
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
    .gateway-form {
        display: grid;
        gap: .85rem;
    }
    .gateway-field label {
        display: block;
        font-size: .68rem;
        font-weight: 900;
        text-transform: uppercase;
        letter-spacing: .45px;
        color: var(--muted);
        margin-bottom: .35rem;
    }
    .gateway-field input {
        width: 100%;
        border: 1px solid var(--line);
        border-radius: 10px;
        padding: .75rem .85rem;
        font: inherit;
        font-weight: 800;
        color: #17251b;
        outline: 0;
    }
    .gateway-field input:focus {
        border-color: var(--accent);
        box-shadow: 0 0 0 3px rgba(45,158,75,.14);
    }
    .gateway-note {
        border: 1px solid var(--line);
        border-radius: 12px;
        background: #f7fcf8;
        color: var(--muted);
        font-size: .82rem;
        line-height: 1.5;
        padding: .8rem .85rem;
    }
    .gateway-error {
        display: none;
        color: #b91c1c;
        font-size: .78rem;
        font-weight: 800;
    }
    .gateway-actions {
        display: flex;
        flex-wrap: wrap;
        gap: .55rem;
        align-items: center;
    }
    .action-link:disabled, .action-link[disabled] {
        opacity: .6;
        cursor: not-allowed;
        transform: none;
        pointer-events: none;
    }
    @media (max-width: 1050px) {
        .bill-detail-grid,
        .detail-fields {
            grid-template-columns: 1fr;
        }
    }
    @media (max-width: 620px) {
        .bill-hero {
            flex-direction: column;
            align-items: flex-start;
        }
    }
</style>
@endsection

@section('page-content')
<div class="bill-hero">
    <div>
        <div class="hero-badge-lite"><i class="bi bi-receipt-cutoff"></i> {{ $assetType }}</div>
        <div class="bill-hero-title">{{ $bill->bills_id }}</div>
        <div class="bill-hero-sub">{{ $assetRef }} - {{ $assetAddress }}<br>{{ $assetGlobalRegion }}</div>
    </div>
    <div style="text-align:right;white-space:nowrap;">
        <div style="font-size:.68rem;text-transform:uppercase;letter-spacing:.5px;color:rgba(255,255,255,.55);margin-bottom:.2rem;">Balance Due</div>
        <div style="font-family:'Poppins',sans-serif;font-weight:800;font-size:1.2rem;">{{ $money($balance) }}</div>
    </div>
</div>

<div style="display:flex;gap:.6rem;flex-wrap:wrap;margin-bottom:1rem;">
    <a class="action-link" href="{{ route('dashboard.olives-account.bills') }}"><i class="bi bi-arrow-left"></i> Back to bills</a>
    @if ($balance > 0)
        <button class="action-link primary" type="button" data-account-doc-open="billPaymentModal"><i class="bi bi-credit-card"></i> Make payment</button>
    @endif
    <button class="action-link" type="button" data-account-doc-open="billInvoiceModal"><i class="bi bi-file-earmark-text"></i> View invoice</button>
    <a class="action-link" href="{{ route('dashboard.olives-account.bills.invoice.download', $bill) }}"><i class="bi bi-download"></i> Download PDF</a>
    <a class="action-link" href="{{ $paymentPageUrl }}" target="_blank" rel="noopener"><i class="bi bi-box-arrow-up-right"></i> Public payment page</a>
    @if ($bill->property)
        <a class="action-link" href="{{ route('dashboard.olives-account.properties.show', $bill->property) }}"><i class="bi bi-house"></i> View property</a>
    @endif
    @if ($bill->business)
        <a class="action-link" href="{{ route('dashboard.olives-account.businesses.show', $bill->business) }}"><i class="bi bi-shop"></i> View business</a>
    @endif
    <a class="action-link" href="{{ route('dashboard.olives-account.payments') }}"><i class="bi bi-credit-card"></i> Payment history</a>
</div>

<dialog class="account-doc-dialog" id="billInvoiceModal">
    <div class="account-doc-modal-head">
        <div class="detail-title"><i class="bi bi-file-earmark-text" style="color:var(--accent);"></i> Invoice Preview</div>
        <button class="account-doc-close" type="button" data-account-doc-close="billInvoiceModal" aria-label="Close invoice preview"><i class="bi bi-x-lg"></i></button>
    </div>
    <div class="account-doc-modal-body">
        @include('dashboard.tiers.account.documents.bill-invoice', ['bill' => $bill])
    </div>
</dialog>

@if ($balance > 0)
<dialog class="account-doc-dialog" id="billPaymentModal">
    <div class="account-doc-modal-head">
        <div class="detail-title"><i class="bi bi-credit-card" style="color:var(--accent);"></i> Gateway Payment</div>
        <button class="account-doc-close" type="button" data-account-doc-close="billPaymentModal" aria-label="Close payment form"><i class="bi bi-x-lg"></i></button>
    </div>
    <div class="account-doc-modal-body">
        <div class="detail-card" style="box-shadow:none;">
            <div class="detail-body">
                <form class="gateway-form" id="billGatewayPaymentForm" data-payment-url="{{ route('public.bills.payment.initialize', $bill) }}">
                    <div class="gateway-note">
                        Payment is processed through the configured payment gateway. The gateway page will open after initialization.
                    </div>
                    <div class="gateway-field">
                        <label for="gatewayAmount">Amount to Pay</label>
                        <input id="gatewayAmount" name="amount" type="number" min="1" max="{{ $paymentAmount }}" step="0.01" value="{{ $paymentAmount }}" required>
                    </div>
                    <div class="gateway-error" id="gatewayPaymentError"></div>
                    <div class="gateway-actions">
                        <button class="action-link primary" type="submit" id="gatewaySubmitButton"><i class="bi bi-shield-check"></i> Continue to gateway</button>
                        <a class="action-link" href="{{ $paymentPageUrl }}" target="_blank" rel="noopener"><i class="bi bi-box-arrow-up-right"></i> Open payment page</a>
                    </div>
                </form>
            </div>
        </div>
    </div>
</dialog>
@endif

<div class="bill-detail-grid">
    <div style="display:flex;flex-direction:column;gap:1rem;">
        <div class="detail-card">
            <div class="detail-card-head">
                <h2 class="detail-title"><i class="bi bi-info-circle-fill" style="color:var(--accent);"></i> Bill Information</h2>
                <span class="status-pill" style="color:{{ $statusColor }};"><i class="bi bi-circle-fill"></i> {{ $status }}</span>
            </div>
            <div class="detail-body">
                <div class="detail-fields">
                    <div class="detail-field">
                        <div class="detail-label">Bill Number</div>
                        <div class="detail-value">{{ $bill->bills_id }}</div>
                    </div>
                    <div class="detail-field">
                        <div class="detail-label">Bill Type</div>
                        <div class="detail-value">{{ $assetType }}</div>
                    </div>
                    <div class="detail-field">
                        <div class="detail-label">Bill Year</div>
                        <div class="detail-value">{{ $bill->bills_year ?: 'N/A' }}</div>
                    </div>
                    <div class="detail-field">
                        <div class="detail-label">Billing Date</div>
                        <div class="detail-value">{{ $bill->billing_date ? \Illuminate\Support\Carbon::parse($bill->billing_date)->format('d M Y') : 'N/A' }}</div>
                    </div>
                    <div class="detail-field">
                        <div class="detail-label">Assembly</div>
                        <div class="detail-value">{{ $assembly->name ?? 'N/A' }}</div>
                    </div>
                    <div class="detail-field">
                        <div class="detail-label">Owner</div>
                        <div class="detail-value">{{ $ownerName }}</div>
                    </div>
                    <div class="detail-field">
                        <div class="detail-label">Linked Asset</div>
                        <div class="detail-value">{{ $assetRef }}</div>
                    </div>
                    <div class="detail-field">
                        <div class="detail-label">Address</div>
                        <div class="detail-value">{{ $assetAddress }}<br>{{ $assetGlobalRegion }}</div>
                    </div>
                </div>
            </div>
        </div>

        <div class="detail-card">
            <div class="detail-card-head">
                <h2 class="detail-title"><i class="bi bi-calculator-fill" style="color:var(--accent);"></i> Amount Breakdown</h2>
            </div>
            <div class="detail-body">
                <div class="detail-fields">
                    <div class="detail-field">
                        <div class="detail-label">Current Amount</div>
                        <div class="detail-value">{{ $money($currentAmount) }}</div>
                    </div>
                    <div class="detail-field">
                        <div class="detail-label">Arrears</div>
                        <div class="detail-value">{{ $money($arrears) }}</div>
                    </div>
                    <div class="detail-field">
                        <div class="detail-label">Discount</div>
                        <div class="detail-value">{{ $money($discount) }}</div>
                    </div>
                    <div class="detail-field">
                        <div class="detail-label">Total Due</div>
                        <div class="detail-value">{{ $money($totalDue) }}</div>
                    </div>
                </div>
            </div>
        </div>
    </div>

    <div style="display:flex;flex-direction:column;gap:1rem;">
        <div class="detail-card">
            <div class="detail-card-head">
                <h2 class="detail-title"><i class="bi bi-speedometer2" style="color:var(--accent);"></i> Payment Summary</h2>
            </div>
            <div class="detail-body">
                <div class="metric-stack">
                    <div class="metric-card">
                        <div class="metric-label">Total Due</div>
                        <div class="metric-value">{{ $money($totalDue) }}</div>
                    </div>
                    <div class="metric-card">
                        <div class="metric-label">Successful Payments</div>
                        <div class="metric-value" style="color:var(--primary);">{{ $money($paid) }}</div>
                    </div>
                    <div class="metric-card">
                        <div class="metric-label">Balance</div>
                        <div class="metric-value" style="color:{{ $statusColor }};">{{ $money($balance) }}</div>
                    </div>
                    <div class="metric-card">
                        <div class="metric-label">Status</div>
                        <div class="metric-value" style="font-size:1rem;color:{{ $statusColor }};">{{ $status }}</div>
                    </div>
                </div>
            </div>
        </div>

        <div class="detail-card">
            <div class="detail-card-head">
                <h2 class="detail-title"><i class="bi bi-clock-history" style="color:var(--accent);"></i> Payments On This Bill</h2>
            </div>
            <div class="detail-body">
                @forelse ($bill->payments as $payment)
                    @php
                        $paymentStatus = strtolower((string) $payment->transaction_status);
                        $paymentColor = $paymentStatus === 'success' ? 'var(--primary)' : ($paymentStatus === 'failed' ? '#b91c1c' : '#92400e');
                    @endphp
                    <div class="payment-row">
                        <div>
                            <div style="font-family:'Poppins',sans-serif;font-weight:800;color:#17251b;">{{ $payment->transaction_id ?? $payment->req_transaction_id ?? 'Payment '.$payment->uuid }}</div>
                            <div style="font-size:.76rem;color:var(--muted);">{{ $payment->payment_mode ?? 'Payment' }} - {{ optional($payment->created_at)->format('d M Y') ?: 'No date' }}</div>
                        </div>
                        <div style="text-align:right;">
                            <div style="font-weight:800;color:var(--primary);">{{ $money($payment->amount) }}</div>
                            <div style="font-size:.74rem;font-weight:800;color:{{ $paymentColor }};">{{ $payment->transaction_status ?? 'Pending' }}</div>
                        </div>
                    </div>
                @empty
                    <div style="text-align:center;padding:1.8rem 1rem;color:var(--muted);">
                        <i class="bi bi-credit-card" style="font-size:1.7rem;display:block;margin-bottom:.45rem;opacity:.45;"></i>
                        No payments are linked to this bill yet.
                    </div>
                @endforelse
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

    const gatewayForm = document.getElementById('billGatewayPaymentForm');
    if (gatewayForm) {
        gatewayForm.addEventListener('submit', async (event) => {
            event.preventDefault();

            const submitButton = document.getElementById('gatewaySubmitButton');
            const errorBox = document.getElementById('gatewayPaymentError');
            const amountInput = document.getElementById('gatewayAmount');
            const maxAmount = Number(amountInput.getAttribute('max'));
            const amount = Number(amountInput.value);

            errorBox.style.display = 'none';
            errorBox.textContent = '';

            if (!amount || amount <= 0) {
                errorBox.textContent = 'Enter a valid payment amount.';
                errorBox.style.display = 'block';
                return;
            }

            if (amount > maxAmount) {
                errorBox.textContent = `Amount cannot exceed {{ $currencySymbol }} ${maxAmount.toFixed(2)}.`;
                errorBox.style.display = 'block';
                return;
            }

            submitButton.disabled = true;
            submitButton.innerHTML = '<i class="bi bi-arrow-repeat"></i> Connecting...';

            try {
                const response = await fetch(gatewayForm.dataset.paymentUrl, {
                    method: 'POST',
                    headers: {
                        'Content-Type': 'application/json',
                        'Accept': 'application/json',
                        'X-CSRF-TOKEN': '{{ csrf_token() }}',
                    },
                    body: JSON.stringify({ amount: amountInput.value }),
                });

                const data = await response.json().catch(() => ({}));

                if (!response.ok || !data.success || !data.authorization_url) {
                    throw new Error(data.message || 'Payment initialization failed. Please try again.');
                }

                window.location.href = data.authorization_url;
            } catch (error) {
                errorBox.textContent = error.message || 'Payment initialization failed. Please try again.';
                errorBox.style.display = 'block';
                submitButton.disabled = false;
                submitButton.innerHTML = '<i class="bi bi-shield-check"></i> Continue to gateway';
            }
        });
    }
</script>
@endpush

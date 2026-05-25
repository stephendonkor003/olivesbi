@php
    $bill = $payment->bill;
    $asset = $bill?->property ?: $bill?->business;
    $assetType = $bill?->property_id ? 'Property Payment' : 'Business Payment';
    $assembly = $payment->assembly ?: $bill?->assembly ?: $bill?->property?->assembly ?: $bill?->business?->assembly;
    $currencyCode = strtoupper(trim((string) ($assembly?->currency_code ?: 'GHS')));
    $currencySymbol = trim((string) ($assembly?->currency_symbol ?: $currencyCode));
    $money = fn ($amount) => trim(($currencySymbol ?: $currencyCode).' '.number_format((float) $amount, 2));
    $owner = $asset?->customer;
    $ownerName = trim(($owner->fname ?? '').' '.($owner->lname ?? '')) ?: ($owner->organization_name ?? 'Linked owner');
    $assetRef = $bill?->property
        ? ($bill->property->account_no ?: ($bill->property->erms_id ?: 'Property '.$bill->property->uuid))
        : ($bill?->business?->account_no ?: ($bill?->business?->business_name ?: 'Linked business'));
    $assetAddress = $asset ? \App\Support\GlobalLocation::addressFor($asset) : 'No address recorded';
    $assetGlobalRegion = $asset ? \App\Support\GlobalLocation::regionFor($asset) : 'Global location not recorded';
    $transactionRef = $payment->transaction_id ?: ($payment->req_transaction_id ?: $payment->uuid);
@endphp

<style>
    .account-doc {
        font-family: Arial, sans-serif;
        color: #17251b;
        background: #fff;
        border: 1px solid #dceade;
        border-radius: 14px;
        overflow: hidden;
        max-width: 760px;
        margin: 0 auto;
    }
    .account-doc * { box-sizing: border-box; }
    .account-doc-header {
        background: #1a5c2a;
        color: #fff;
        padding: 22px 24px;
    }
    .account-doc-kicker {
        font-size: 11px;
        font-weight: 800;
        text-transform: uppercase;
        letter-spacing: .8px;
        color: #a8f0b8;
        margin-bottom: 7px;
    }
    .account-doc-title {
        font-size: 26px;
        font-weight: 800;
        margin: 0 0 5px;
    }
    .account-doc-sub {
        font-size: 13px;
        color: #d9f5df;
        margin: 0;
    }
    .account-doc-body { padding: 22px 24px 24px; }
    .account-doc-row {
        width: 100%;
        border-collapse: collapse;
        margin-bottom: 16px;
    }
    .account-doc-row td {
        vertical-align: top;
        width: 50%;
        padding: 0 12px 0 0;
    }
    .account-doc-label {
        font-size: 10px;
        font-weight: 800;
        letter-spacing: .6px;
        text-transform: uppercase;
        color: #6b7280;
        margin-bottom: 4px;
    }
    .account-doc-value {
        font-size: 14px;
        font-weight: 700;
        color: #17251b;
        line-height: 1.45;
    }
    .account-doc-paid {
        border: 1px solid #bbf7d0;
        background: #f0fdf4;
        border-radius: 12px;
        padding: 16px;
        margin: 16px 0;
        text-align: center;
    }
    .account-doc-paid .amount {
        font-size: 28px;
        color: #1a5c2a;
        font-weight: 800;
        margin-top: 5px;
    }
    .account-doc-box {
        border: 1px solid #dceade;
        background: #f7fcf8;
        border-radius: 10px;
        padding: 12px;
        margin-bottom: 14px;
    }
    .account-doc-footer {
        margin-top: 18px;
        padding-top: 12px;
        border-top: 1px solid #dceade;
        font-size: 11px;
        color: #6b7280;
        line-height: 1.5;
    }
</style>

<div class="account-doc">
    <div class="account-doc-header">
        <div class="account-doc-kicker">Olives BI Payment Receipt</div>
        <h1 class="account-doc-title">{{ $transactionRef }}</h1>
        <p class="account-doc-sub">{{ $assetType }} - {{ $payment->transaction_status ?: 'Pending' }}</p>
    </div>
    <div class="account-doc-body">
        <div class="account-doc-paid">
            <div class="account-doc-label">Amount Paid</div>
            <div class="amount">{{ $money($payment->amount) }}</div>
        </div>

        <table class="account-doc-row">
            <tr>
                <td>
                    <div class="account-doc-label">Paid By</div>
                    <div class="account-doc-value">{{ $ownerName }}<br>{{ $payment->phone ?: 'No phone recorded' }}</div>
                </td>
                <td>
                    <div class="account-doc-label">Payment Details</div>
                    <div class="account-doc-value">
                        Bill: {{ $payment->bills_id ?: 'N/A' }}<br>
                        Date: {{ optional($payment->created_at)->format('d M Y, H:i') ?: 'N/A' }}<br>
                        Mode: {{ $payment->payment_mode ?: 'N/A' }}
                    </div>
                </td>
            </tr>
        </table>

        <div class="account-doc-box">
            <div class="account-doc-label">Linked Asset</div>
            <div class="account-doc-value">{{ $assetRef }} - {{ $assembly->name ?? 'Issuing assembly' }}<br>{{ $assetAddress }}<br>{{ $assetGlobalRegion }}</div>
        </div>

        <div class="account-doc-box">
            <div class="account-doc-label">Receipt UUID</div>
            <div class="account-doc-value">{{ $payment->uuid }}</div>
        </div>

        <div class="account-doc-footer">
            This receipt is system generated from the Olives BI account portal and does not require a signature.
        </div>
    </div>
</div>

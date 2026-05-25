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
    $owner = $asset?->customer;
    $ownerName = trim(($owner->fname ?? '').' '.($owner->lname ?? '')) ?: ($owner->organization_name ?? 'Linked owner');
    $assetRef = $bill->property
        ? ($bill->property->account_no ?: ($bill->property->erms_id ?: 'Property '.$bill->property->uuid))
        : ($bill->business?->account_no ?: ($bill->business?->business_name ?: 'Linked business'));
    $asset = $bill->property ?: $bill->business;
    $assetAddress = $asset ? \App\Support\GlobalLocation::addressFor($asset) : 'No address recorded';
    $assetGlobalRegion = $asset ? \App\Support\GlobalLocation::regionFor($asset) : 'Global location not recorded';
    $paid = (float) $bill->successfulPayments->sum('amount');
    $balance = max(0, $totalDue - $paid);
    $paymentUrl = route('admin.public.bills.show', $bill);
    $paymentQr = base64_encode((string) \SimpleSoftwareIO\QrCode\Facades\QrCode::format('svg')->size(118)->margin(1)->generate($paymentUrl));
@endphp

<style>
    .account-doc {
        font-family: Arial, sans-serif;
        color: #17251b;
        background: #fff;
        border: 1px solid #dceade;
        border-radius: 14px;
        overflow: hidden;
        max-width: 820px;
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
    .account-doc-box {
        border: 1px solid #dceade;
        background: #f7fcf8;
        border-radius: 10px;
        padding: 12px;
        margin-bottom: 14px;
    }
    .account-doc-table {
        width: 100%;
        border-collapse: collapse;
        margin-top: 10px;
    }
    .account-doc-table th,
    .account-doc-table td {
        border-bottom: 1px solid #dceade;
        padding: 11px 8px;
        text-align: left;
        font-size: 13px;
    }
    .account-doc-table th {
        color: #1a5c2a;
        font-size: 11px;
        text-transform: uppercase;
        letter-spacing: .5px;
        background: #eef8f1;
    }
    .account-doc-table td:last-child,
    .account-doc-table th:last-child {
        text-align: right;
    }
    .account-doc-total td {
        font-weight: 800;
        color: #1a5c2a;
        font-size: 15px;
        border-bottom: 0;
    }
    .account-doc-payment {
        width: 100%;
        border-collapse: collapse;
        margin-top: 16px;
        border: 1px solid #dceade;
        background: #f7fcf8;
        border-radius: 10px;
    }
    .account-doc-payment td {
        padding: 12px;
        vertical-align: middle;
    }
    .account-doc-payment-copy {
        width: 72%;
    }
    .account-doc-payment-url {
        font-size: 10px;
        color: #1a5c2a;
        line-height: 1.35;
        word-break: break-all;
        margin-top: 7px;
    }
    .account-doc-qr-cell {
        width: 28%;
        text-align: right;
    }
    .account-doc-qr {
        width: 118px;
        height: 118px;
        border: 1px solid #dceade;
        background: #fff;
        padding: 6px;
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
        <div class="account-doc-kicker">Olives BI Invoice</div>
        <h1 class="account-doc-title">{{ $bill->bills_id }}</h1>
        <p class="account-doc-sub">{{ $assetType }} - {{ $assembly->name ?? 'Issuing assembly' }}</p>
    </div>
    <div class="account-doc-body">
        <table class="account-doc-row">
            <tr>
                <td>
                    <div class="account-doc-label">Billed To</div>
                    <div class="account-doc-value">{{ $ownerName }}<br>{{ $assetAddress }}<br>{{ $assetGlobalRegion }}</div>
                </td>
                <td>
                    <div class="account-doc-label">Invoice Details</div>
                    <div class="account-doc-value">
                        Year: {{ $bill->bills_year ?: 'N/A' }}<br>
                        Date: {{ $bill->billing_date ? \Illuminate\Support\Carbon::parse($bill->billing_date)->format('d M Y') : optional($bill->created_at)->format('d M Y') }}<br>
                        Asset: {{ $assetRef }}
                    </div>
                </td>
            </tr>
        </table>

        <div class="account-doc-box">
            <div class="account-doc-label">Reference</div>
            <div class="account-doc-value">{{ $bill->uuid }}</div>
        </div>

        <table class="account-doc-table">
            <thead>
                <tr>
                    <th>Description</th>
                    <th>Amount</th>
                </tr>
            </thead>
            <tbody>
                <tr>
                    <td>Current bill amount</td>
                    <td>{{ $money($currentAmount) }}</td>
                </tr>
                <tr>
                    <td>Arrears</td>
                    <td>{{ $money($arrears) }}</td>
                </tr>
                <tr>
                    <td>Discount</td>
                    <td>{{ $money($discount) }}</td>
                </tr>
                <tr class="account-doc-total">
                    <td>Total due</td>
                    <td>{{ $money($totalDue) }}</td>
                </tr>
                <tr>
                    <td>Successful payments</td>
                    <td>{{ $money($paid) }}</td>
                </tr>
                <tr class="account-doc-total">
                    <td>Balance payable</td>
                    <td>{{ $money($balance) }}</td>
                </tr>
            </tbody>
        </table>

        <table class="account-doc-payment">
            <tr>
                <td class="account-doc-payment-copy">
                    <div class="account-doc-label">Gateway Payment</div>
                    <div class="account-doc-value">Scan the QR code to open the public bill payment page and continue through the configured payment gateway.</div>
                    <div class="account-doc-payment-url">{{ $paymentUrl }}</div>
                </td>
                <td class="account-doc-qr-cell">
                    <img class="account-doc-qr" src="data:image/svg+xml;base64,{{ $paymentQr }}" alt="Payment QR code">
                </td>
            </tr>
        </table>

        <div class="account-doc-footer">
            This invoice is generated from the Olives BI account portal. Contact the issuing assembly or municipality for validation and billing enquiries.
        </div>
    </div>
</div>

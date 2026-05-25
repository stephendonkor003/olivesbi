@extends('dashboard.tiers.partials.tier-page-layout')

@php
    $assembly = $property->assembly;
    $currencyCode = strtoupper(trim((string) ($assembly?->currency_code ?: 'GHS')));
    $currencySymbol = trim((string) ($assembly?->currency_symbol ?: $currencyCode));
    $money = fn ($amount) => trim(($currencySymbol ?: $currencyCode).' '.number_format((float) $amount, 2));
    $propertyRef = $property->account_no ?: ($property->erms_id ?: 'Property '.$property->uuid);
    $address = \App\Support\GlobalLocation::addressFor($property);
    $globalRegion = \App\Support\GlobalLocation::regionFor($property);
    $ownerName = trim(($property->customer->fname ?? '').' '.($property->customer->lname ?? '')) ?: ($property->customer->organization_name ?? 'Linked owner');
    $propertyBills = $propertyBills ?? collect();
    $totalBills = $propertyBills->sum(fn ($bill) => (float) $bill->amount + (float) $bill->arrears);
    $totalPaid = $propertyBills->sum(fn ($bill) => $bill->successfulPayments->sum('amount'));
@endphp

@section('page-title', 'View Property')
@section('page-scope', 'Property Details')

@section('page-head-extra')
<style>
    .property-detail-grid {
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
    .property-hero {
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
    .property-hero-title {
        font-family: 'Poppins', sans-serif;
        font-weight: 800;
        font-size: clamp(1.3rem, 2.3vw, 1.8rem);
        line-height: 1.2;
        margin-bottom: .35rem;
    }
    .property-hero-sub {
        color: rgba(255,255,255,.82);
        font-size: .88rem;
        line-height: 1.6;
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
    .bill-row {
        display: flex;
        justify-content: space-between;
        gap: 1rem;
        padding: .75rem 0;
        border-bottom: 1px solid var(--line);
    }
    .bill-row:last-child { border-bottom: 0; }
    .account-doc-dialog {
        width: min(920px, calc(100vw - 2rem));
        max-height: 88vh;
        border: 0;
        border-radius: 14px;
        padding: 0;
        box-shadow: 0 24px 70px rgba(7,26,13,.32);
    }
    .account-doc-dialog::backdrop { background: rgba(7,26,13,.5); }
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
        .property-detail-grid,
        .detail-fields {
            grid-template-columns: 1fr;
        }
    }
    @media (max-width: 620px) {
        .property-hero {
            flex-direction: column;
            align-items: flex-start;
        }
    }
</style>
@endsection

@section('page-content')
<div class="property-hero">
    <div>
        <div class="hero-badge-lite"><i class="bi bi-house-check-fill"></i> Olives Account Property</div>
        <div class="property-hero-title">{{ $propertyRef }}</div>
        <div class="property-hero-sub">{{ $address }}<br>{{ $globalRegion }}</div>
    </div>
    <div style="text-align:right;white-space:nowrap;">
        <div style="font-size:.68rem;text-transform:uppercase;letter-spacing:.5px;color:rgba(255,255,255,.55);margin-bottom:.2rem;">Ratable Value</div>
        <div style="font-family:'Poppins',sans-serif;font-weight:800;font-size:1.2rem;">{{ $money($property->ratable_value) }}</div>
    </div>
</div>

<div style="display:flex;gap:.6rem;flex-wrap:wrap;margin-bottom:1rem;">
    <a class="action-link" href="{{ route('dashboard.olives-account.properties') }}"><i class="bi bi-arrow-left"></i> Back to properties</a>
    <a class="action-link primary" href="{{ route('dashboard.olives-account.bills') }}"><i class="bi bi-receipt"></i> View bills</a>
    <a class="action-link" href="{{ route('dashboard.olives-account.locality') }}"><i class="bi bi-geo-alt"></i> Check locality</a>
</div>

<div class="property-detail-grid">
    <div style="display:flex;flex-direction:column;gap:1rem;">
        <div class="detail-card">
            <div class="detail-card-head">
                <h2 class="detail-title"><i class="bi bi-info-circle-fill" style="color:var(--accent);"></i> Property Information</h2>
            </div>
            <div class="detail-body">
                <div class="detail-fields">
                    <div class="detail-field">
                        <div class="detail-label">Property Reference</div>
                        <div class="detail-value">{{ $propertyRef }}</div>
                    </div>
                    <div class="detail-field">
                        <div class="detail-label">ERMS ID</div>
                        <div class="detail-value">{{ $property->erms_id ?: 'N/A' }}</div>
                    </div>
                    <div class="detail-field">
                        <div class="detail-label">Digital Address</div>
                        <div class="detail-value">{{ $property->digitaladdress ?: 'N/A' }}</div>
                    </div>
                    <div class="detail-field">
                        <div class="detail-label">Address Line</div>
                        <div class="detail-value">{{ $property->address_line ?: 'N/A' }}</div>
                    </div>
                    <div class="detail-field">
                        <div class="detail-label">Location</div>
                        <div class="detail-value">{{ $property->location ?: 'N/A' }}</div>
                    </div>
                    <div class="detail-field">
                        <div class="detail-label">Street Name</div>
                        <div class="detail-value">{{ $property->streetname ?: 'N/A' }}</div>
                    </div>
                    <div class="detail-field">
                        <div class="detail-label">House Number</div>
                        <div class="detail-value">{{ $property->hseno ?: 'N/A' }}</div>
                    </div>
                    <div class="detail-field">
                        <div class="detail-label">Assembly</div>
                        <div class="detail-value">{{ $assembly->name ?? 'N/A' }}</div>
                    </div>
                    <div class="detail-field">
                        <div class="detail-label">Country</div>
                        <div class="detail-value">{{ $property->country ?: 'N/A' }}{{ $property->country_code ? ' ('.$property->country_code.')' : '' }}</div>
                    </div>
                    <div class="detail-field">
                        <div class="detail-label">Continent</div>
                        <div class="detail-value">{{ $property->continent ?: 'N/A' }}</div>
                    </div>
                    <div class="detail-field">
                        <div class="detail-label">City / District</div>
                        <div class="detail-value">{{ collect([$property->city, $property->district])->filter()->implode(', ') ?: 'N/A' }}</div>
                    </div>
                    <div class="detail-field">
                        <div class="detail-label">State / Region</div>
                        <div class="detail-value">{{ $property->administrative_area ?: 'N/A' }}</div>
                    </div>
                    <div class="detail-field">
                        <div class="detail-label">Postal Code</div>
                        <div class="detail-value">{{ $property->postal_code ?: 'N/A' }}</div>
                    </div>
                    <div class="detail-field">
                        <div class="detail-label">Owner</div>
                        <div class="detail-value">{{ $ownerName }}</div>
                    </div>
                </div>
            </div>
        </div>

        <div class="detail-card">
            <div class="detail-card-head">
                <h2 class="detail-title"><i class="bi bi-diagram-3-fill" style="color:var(--accent);"></i> Classification</h2>
            </div>
            <div class="detail-body">
                <div class="detail-fields">
                    <div class="detail-field">
                        <div class="detail-label">Entity Type</div>
                        <div class="detail-value">{{ $property->entityType->name ?? 'N/A' }}</div>
                    </div>
                    <div class="detail-field">
                        <div class="detail-label">Revenue Stream</div>
                        <div class="detail-value">{{ $property->revenueStream->name ?? 'N/A' }}</div>
                    </div>
                    <div class="detail-field">
                        <div class="detail-label">Division</div>
                        <div class="detail-value">{{ $property->division->division_name ?? $property->division->name ?? 'N/A' }}</div>
                    </div>
                    <div class="detail-field">
                        <div class="detail-label">Block</div>
                        <div class="detail-value">{{ $property->block->block_name ?? $property->block->name ?? 'N/A' }}</div>
                    </div>
                    <div class="detail-field">
                        <div class="detail-label">Zone</div>
                        <div class="detail-value">{{ $property->zone->name ?? 'N/A' }}</div>
                    </div>
                    <div class="detail-field">
                        <div class="detail-label">Property Use</div>
                        <div class="detail-value">{{ $property->propertyUse->name ?? 'N/A' }}</div>
                    </div>
                </div>
            </div>
        </div>
    </div>

    <div style="display:flex;flex-direction:column;gap:1rem;">
        <div class="detail-card">
            <div class="detail-card-head">
                <h2 class="detail-title"><i class="bi bi-speedometer2" style="color:var(--accent);"></i> Account Summary</h2>
            </div>
            <div class="detail-body">
                <div class="metric-stack">
                    <div class="metric-card">
                        <div class="metric-label">Ratable Value</div>
                        <div class="metric-value">{{ $money($property->ratable_value) }}</div>
                    </div>
                    <div class="metric-card">
                        <div class="metric-label">Total Bills</div>
                        <div class="metric-value">{{ $money($totalBills) }}</div>
                    </div>
                    <div class="metric-card">
                        <div class="metric-label">Successful Payments</div>
                        <div class="metric-value">{{ $money($totalPaid) }}</div>
                    </div>
                    <div class="metric-card">
                        <div class="metric-label">Rating Status</div>
                        <div class="metric-value" style="font-size:1rem;color:{{ $property->is_rated ? 'var(--primary)' : '#92400e' }};">{{ $property->is_rated ? 'Rated' : 'Not rated' }}</div>
                    </div>
                </div>
            </div>
        </div>

        <div class="detail-card">
            <div class="detail-card-head">
                <h2 class="detail-title"><i class="bi bi-receipt-cutoff" style="color:var(--accent);"></i> Related Bills</h2>
            </div>
            <div class="detail-body">
                @forelse ($propertyBills as $bill)
                    <div class="bill-row">
                        <div>
                            <div style="font-family:'Poppins',sans-serif;font-weight:800;color:#17251b;">{{ $bill->bills_id }}</div>
                            <div style="font-size:.76rem;color:var(--muted);">Year {{ $bill->bills_year }} - {{ $bill->billing_date ? \Illuminate\Support\Carbon::parse($bill->billing_date)->format('d M Y') : 'No billing date' }}</div>
                        </div>
                        <div style="text-align:right;">
                            <div style="font-weight:800;color:#b91c1c;">{{ $money((float) $bill->amount + (float) $bill->arrears) }}</div>
                            <div style="display:flex;gap:.35rem;flex-wrap:wrap;justify-content:flex-end;margin-top:.35rem;">
                                <a class="action-link" style="padding:.35rem .55rem;" href="{{ route('dashboard.olives-account.bills.show', $bill) }}"><i class="bi bi-eye"></i> Open</a>
                                <button class="action-link" style="padding:.35rem .55rem;" type="button" data-account-doc-open="invoice-{{ $bill->uuid }}"><i class="bi bi-file-earmark-text"></i> View</button>
                                <a class="action-link" style="padding:.35rem .55rem;" href="{{ route('dashboard.olives-account.bills.invoice.download', $bill) }}"><i class="bi bi-download"></i> PDF</a>
                            </div>
                            <dialog class="account-doc-dialog" id="invoice-{{ $bill->uuid }}">
                                <div class="account-doc-modal-head">
                                    <div class="detail-title"><i class="bi bi-file-earmark-text" style="color:var(--accent);"></i> Invoice Preview</div>
                                    <button class="account-doc-close" type="button" data-account-doc-close="invoice-{{ $bill->uuid }}" aria-label="Close invoice preview"><i class="bi bi-x-lg"></i></button>
                                </div>
                                <div class="account-doc-modal-body">
                                    @include('dashboard.tiers.account.documents.bill-invoice', ['bill' => $bill])
                                </div>
                            </dialog>
                        </div>
                    </div>
                @empty
                    <div style="text-align:center;padding:1.8rem 1rem;color:var(--muted);">
                        <i class="bi bi-receipt" style="font-size:1.7rem;display:block;margin-bottom:.45rem;opacity:.45;"></i>
                        No bills are linked to this property yet.
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
</script>
@endpush

@extends('dashboard.tiers.partials.tier-page-layout')

@section('page-title', 'Payment Receipt')
@section('page-scope', 'Payment Receipt')

@section('page-head-extra')
<style>
    .document-page-actions {
        display: flex;
        gap: .6rem;
        flex-wrap: wrap;
        margin-bottom: 1rem;
    }
    .document-page-action {
        display: inline-flex;
        align-items: center;
        gap: .4rem;
        border-radius: 9px;
        background: var(--soft);
        color: var(--primary);
        border: 1px solid var(--line);
        text-decoration: none;
        font-size: .8rem;
        font-weight: 800;
        padding: .55rem .78rem;
    }
    .document-page-action.primary {
        background: var(--accent);
        color: #fff;
        border-color: var(--accent);
    }
</style>
@endsection

@section('page-content')
<div class="document-page-actions">
    <a class="document-page-action" href="{{ route('dashboard.olives-account.payments.show', $payment) }}"><i class="bi bi-arrow-left"></i> Back to payment</a>
    <a class="document-page-action primary" href="{{ route('dashboard.olives-account.payments.receipt.download', $payment) }}"><i class="bi bi-download"></i> Download PDF</a>
</div>

@include('dashboard.tiers.account.documents.payment-receipt', ['payment' => $payment])
@endsection

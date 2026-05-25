@extends('dashboard.tiers.partials.tier-page-layout')

@section('page-title', 'Bill Invoice')
@section('page-scope', 'Bill Invoice')

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
    <a class="document-page-action" href="{{ route('dashboard.olives-account.bills.show', $bill) }}"><i class="bi bi-arrow-left"></i> Back to bill</a>
    <a class="document-page-action primary" href="{{ route('dashboard.olives-account.bills.invoice.download', $bill) }}"><i class="bi bi-download"></i> Download PDF</a>
    <a class="document-page-action" href="{{ route('admin.public.bills.show', $bill) }}" target="_blank" rel="noopener"><i class="bi bi-credit-card"></i> Pay online</a>
</div>

@include('dashboard.tiers.account.documents.bill-invoice', ['bill' => $bill])
@endsection

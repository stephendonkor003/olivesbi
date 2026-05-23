@extends('layout.base')

@section('page-styles')
@endsection

@section('page-content')
    <div class="card">
        <!-- HEADER SECTION -->
        <div class="card-body border-bottom pb-3">
            <div class="d-flex justify-content-between align-items-center mb-2">
                <div>
                    <h4 class="fw-bold text-primary mb-1">
                        <i class="ri-file-chart-line me-2"></i> Regional Analysis Overview
                    </h4>

                    <p class="mb-0 text-muted fs-14">
                        Explore insights from all 16 regions of Ghana. Select your preferred region to view its
                        corresponding data.
                    </p>
                </div>
            </div>
        </div>

    </div>
    <div class="row">
        @foreach ($total['regions'] as $index => $region)
            <div class="col-xxl-12 mb-3">
                <!-- Toggle Button -->
                <button class="btn btn-outline-primary w-100 text-start mb-2" type="button" data-bs-toggle="collapse"
                    data-bs-target="#region-table-{{ $region->id }}" aria-expanded="false"
                    aria-controls="region-table-{{ $region->id }}">
                    {{ $region->name }}
                </button>

                <!-- Collapsible Table Card -->
                <div class="collapse" id="region-table-{{ $region->id }}">
                    <div class="card custom-card">
                        <div class="card-body">
                            @if ($region->assemblies->count() > 0)
                                <div class="table-responsive">
                                    <table class="table table-bordered text-nowrap w-100"
                                        id="file-export-{{ $region->id }}">
                                        <thead>
                                            <tr>
                                                <th>S/N</th>
                                                <th>Assembly Name</th>
                                                <th>Total Properties</th>
                                                <th>Total Businesses</th>
                                                <th>Total Bills (GHS)</th>
                                                <th>Total Payments (GHS)</th>
                                                <th>Total Receivables (GHS)</th>
                                            </tr>
                                        </thead>
                                        <tbody>
                                            @php
                                                $regionTotalProperties = 0;
                                                $regionTotalBusinesses = 0;
                                                $regionTotalBills = 0;
                                                $regionTotalPayments = 0;
                                                $regionTotalReceivables = 0;
                                            @endphp

                                            @foreach ($region->assemblies as $key => $assembly)
                                                @php
                                                    $totalPropertiesCount = $assembly->properties->count();
                                                    $totalBusinessesCount = $assembly->businesses->count();
                                                    $totalBills = $assembly->bills->sum('amount');
                                                    $totalPayments = $assembly->payments
                                                        ->filter(function ($payment) {
                                                            return $payment->payment_mode !== 'momo' ||
                                                                $payment->transaction_status == 'Success';
                                                        })
                                                        ->sum('amount');
                                                    $totalReceivables = $totalBills - $totalPayments;

                                                    $regionTotalProperties += $totalPropertiesCount;
                                                    $regionTotalBusinesses += $totalBusinessesCount;
                                                    $regionTotalBills += $totalBills;
                                                    $regionTotalPayments += $totalPayments;
                                                    $regionTotalReceivables += $totalReceivables;
                                                @endphp

                                                <tr>
                                                    <td>{{ $key + 1 }}</td>
                                                    <td>{{ $assembly->name }}</td>
                                                    <td>{{ $totalPropertiesCount }}</td>
                                                    <td>{{ $totalBusinessesCount }}</td>
                                                    <td>{{ number_format($totalBills, 2) }}</td>
                                                    <td>{{ number_format($totalPayments, 2) }}</td>
                                                    <td>{{ number_format($totalReceivables, 2) }}</td>
                                                </tr>
                                            @endforeach
                                        </tbody>
                                        <tfoot>
                                            <tr>
                                                <th colspan="2" class="text-end">Totals:</th>
                                                <th>{{ $regionTotalProperties }}</th>
                                                <th>{{ $regionTotalBusinesses }}</th>
                                                <th>{{ number_format($regionTotalBills, 2) }}</th>
                                                <th>{{ number_format($regionTotalPayments, 2) }}</th>
                                                <th>{{ number_format($regionTotalReceivables, 2) }}</th>
                                            </tr>
                                        </tfoot>
                                    </table>
                                </div>
                            @else
                                <p>No assemblies available for this region.</p>
                            @endif
                        </div>
                    </div>
                </div>
            </div>
        @endforeach
    </div>
@endsection

@section('page-scripts')
    <script>
        $(document).ready(function() {
            $('table[id^="file-export-"]').each(function() {
                $(this).DataTable({
                    dom: 'Bfrtip',
                    buttons: ['copy', 'csv', 'excel', 'pdf', 'print'],
                    language: {
                        searchPlaceholder: 'Search...',
                        sSearch: '',
                    },
                    responsive: true,
                    pageLength: 10,
                    lengthMenu: [5, 10, 25, 50, 100],
                });
            });
        });
    </script>
@endsection

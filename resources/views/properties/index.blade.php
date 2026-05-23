@extends('layout.base')

@section('page-styles')
    <!-- Leaflet css -->
    <link href="{{ asset('assets/app/map/leaflet/leaflet.css') }}" rel="stylesheet">
    <link href="{{ asset('assets/app/map/leaflet/OverPassLayer.css') }}" rel="stylesheet">
    <link href="{{ asset('assets/app/map/leaflet/leaflet-geoman.css') }}" rel="stylesheet">
    <link href="{{ asset('assets/app/map/leaflet/L.Control.SlideMenu.css') }}" rel="stylesheet">
@endsection

@section('page-content')
    <div class="container-fluid">
        <div class="card">
            <!-- HEADER SECTION -->
            <div class="card-body border-bottom pb-3">
                <div class="d-flex justify-content-between align-items-center mb-2">
                    <div>
                        <h4 class="fw-bold text-primary mb-1">
                            <i class="ri-building-line me-2"></i> Property Management
                        </h4>


                        <p class="mb-0 text-muted fs-14">
                            You are Viewing all Properties Record from your
                            central database repository.
                        </p>

                    </div>
                    @can('properties.create')
                        <div class="d-flex gap-2">
                            <a href="{{ route('properties.create') }}" class="btn btn-sm btn-primary">
                                <i class="fa fa-plus me-1"></i> Add Property
                            </a>
                            <a href="{{ route('properties.import') }}" class="btn btn-sm btn-success">
                                <i class="fa fa-upload me-1"></i> Import Properties
                            </a>
                        </div>
                    @endcan
                </div>
            </div>
        </div>

        <div class="row">
            <div class="col-md-12">
                <div class="card">
                    <div class="card-body">
                        <div class="col-xxl-12">
                            <form method="GET" action="{{ route('properties.index') }}" class="mb-3">
                                <div class="row align-items-end g-3">
                                    <!-- Entity Type -->
                                    <div class="col-md-4">
                                        <label for="entity_type" class="form-label">Entity Type</label>
                                        <select name="entity_type" id="entity_type" class="form-select">
                                            <option value="">All</option>
                                            @foreach ($data['entityTypes'] as $type)
                                                <option value="{{ $type->id }}"
                                                    {{ request('entity_type') == $type->id ? 'selected' : '' }}>
                                                    {{ $type->name }}
                                                </option>
                                            @endforeach
                                        </select>
                                    </div>

                                    <!-- Assembly -->
                                    <div class="col-md-4">
                                        <label for="assembly" class="form-label">Assembly</label>
                                        <select name="assembly" id="assembly" class="form-select">
                                            <option value="">All</option>
                                            @foreach ($data['assemblies'] as $assembly)
                                                <option value="{{ $assembly->id }}"
                                                    {{ request('assembly') == $assembly->id ? 'selected' : '' }}>
                                                    {{ $assembly->name }}
                                                </option>
                                            @endforeach
                                        </select>
                                    </div>

                                    <!-- Apply Filters Button -->
                                    <div class="col-md-4">
                                        <button type="submit" class="btn btn-primary w-100">Apply Filters</button>
                                    </div>
                                </div>
                            </form>
                        </div>
                    </div>
                </div>
            </div>
        </div>

        <div class="col-md-12 mb-5 ml-3">
            <label for="map" class="mb-3 d-block" style="font-size: 16px;">
                Rate Payer Properties On Map (
                <span style="color: blue; font-weight: bold;">Blue:</span> Full Payment,
                <span style="color: rgb(53, 53, 10); font-weight: bold;">Yellow:</span> Partial Payment,
                <span style="color: red; font-weight: bold;">Red:</span> No Payment)
            </label>
        </div>
        <div class="card">
            <div id="map" class="shadow rounded" style="height: 600px;"></div>
        </div>


        <div class="container-fluid mh-auto">
            <div class="row">

                @if (session()->has('status'))
                    <div class="alert alert-success alert-dismissible fade show">
                        <svg viewBox="0 0 24 24" width="24" height="24" stroke="currentColor" stroke-width="2"
                            fill="none" stroke-linecap="round" stroke-linejoin="round" class="me-2">
                            <polyline points="9 11 12 14 22 4"></polyline>
                            <path d="M21 12v7a2 2 0 0 1-2 2H5a2 2 0 0 1-2-2V5a2 2 0 0 1 2-2h11"></path>
                        </svg>
                        <strong>{{ session('status') }}</strong>
                        <button type="button" class="btn-close" data-bs-dismiss="alert" aria-label="btn-close"><span><i
                                    class="fa-solid fa-xmark"></i></span>
                        </button>
                    </div>
                @endif

                <div class="card">
                    <div class="table-responsive active-projects user-tbl  dt-filter">
                        <table id="file-export" class="table table-bordered text-nowrap w-100">
                            <thead>
                                <tr>
                                    <th>S/N</th>
                                    <th>Entity Type</th>
                                    <th>Category</th>
                                    {{-- <th>Digital Address</th> --}}
                                    {{-- <th>Location</th> --}}
                                    {{-- <th>Street Name</th> --}}
                                    <th>Rated?</th>

                                    <th>Property Number</th>
                                    {{-- <th>Validated?</th> --}}
                                    {{-- <th>Owner Account</th> --}}
                                    {{-- <th>Owner Name</th> --}}
                                    {{-- <th>Ratable Value</th> --}}
                                    {{-- <th>Assembly</th> --}}
                                    {{-- <th>Division</th>
                                        <th>Block</th>
                                        <th>Zone</th> --}}
                                    <th>Property Use</th>
                                    {{-- <th>Date Created</th> --}}
                                    <th>Actions</th>
                                </tr>
                            </thead>
                            <tbody>
                                @foreach ($data['properties'] as $index => $property)
                                    <tr>
                                        <td>{{ $index + 1 }}</td>
                                        <td>{{ $property->entityType->name ?? 'N/A' }}</td>
                                        <td>{{ $property->entityType->category ?? 'N/A' }}</td>
                                        {{-- <td>{{ $property->digital_address }}</td>
                                            <td>{{ $property->location }}</td> --}}
                                        {{-- <td>{{ $property->street_name }}</td> --}}
                                        {{-- <td>{{ $property->validated }}</td> --}}
                                        {{-- <td>
                                                @if ($property->validated === 'Yes')
                                                    <span class="badge bg-success">Yes</span>
                                                @else
                                                    <span class="badge bg-danger">No</span>
                                                @endif
                                            </td> --}}
                                        <td>
                                            @if ($property->rated === 'Yes')
                                                <span class="badge bg-success">Yes</span>
                                            @else
                                                <span class="badge bg-danger">No</span>
                                            @endif
                                        </td>
                                        <td>{{ $property->property_number }}</td>
                                        {{-- <td>{{ $property->customer->account_number ?? 'N/A' }}</td> --}}
                                        {{-- <td>{{ $property->customer->first_name ?? '' }}
                                                {{ $property->customer->last_name ?? 'N/A' }}
                                            </td> --}}
                                        {{-- <td>GHS {{ number_format($property->ratable_value, 2) }}</td> --}}
                                        {{-- <td>{{ $property->assembly->name ?? 'N/A' }}</td> --}}
                                        {{-- <td>{{ $property->division->division_name ?? 'N/A' }}</td>
                                            <td>{{ $property->block->block_name ?? 'N/A' }}</td>
                                            <td>{{ $property->zone->name ?? 'N/A' }}</td> --}}
                                        <td>{{ $property->propertyUse->name ?? 'N/A' }}</td>
                                        {{-- <td>{{ $property->created_at }}</td> --}}
                                        <td>
                                            <div class="dropdown">
                                                <div class="btn-link" data-bs-toggle="dropdown" aria-expanded="false">
                                                    <svg width="24" height="24" viewBox="0 0 24 24" fill="none"
                                                        xmlns="http://www.w3.org/2000/svg">
                                                        <path
                                                            d="M11 12C11 12.5523 11.4477 13 12 13C12.5523 13 13 12.5523 13 12C13 11.4477 12.5523 11 12 11C11.4477 11 11 11.4477 11 12Z"
                                                            stroke="#737B8B" stroke-width="2" stroke-linecap="round"
                                                            stroke-linejoin="round">
                                                        </path>
                                                        <path
                                                            d="M18 12C18 12.5523 18.4477 13 19 13C19.5523 13 20 12.5523 20 12C20 11.4477 19.5523 11 19 11C18.4477 11 18 11.4477 18 12Z"
                                                            stroke="#737B8B" stroke-width="2" stroke-linecap="round"
                                                            stroke-linejoin="round">
                                                        </path>
                                                        <path
                                                            d="M4 12C4 12.5523 4.44772 13 5 13C5.55228 13 6 12.5523 6 12C6 11.4477 5.55228 11 5 11C4.44772 11 4 11.4477 4 12Z"
                                                            stroke="#737B8B" stroke-width="2" stroke-linecap="round"
                                                            stroke-linejoin="round">
                                                        </path>
                                                    </svg>
                                                </div>
                                                <div class="dropdown-menu dropdown-menu-end">
                                                    <div class="py-2">
                                                        <a class="dropdown-item"
                                                            href=" {{ route('properties.show', $property) }}">View
                                                        </a>
                                                        <a class="dropdown-item"
                                                            href=" {{ route('properties.edit', $property) }}">Edit
                                                        </a>
                                                    </div>
                                                </div>
                                            </div>
                                        </td>
                                    </tr>
                                @endforeach
                            </tbody>
                            {{-- <tfoot>
                                    <th colspan="10"></th>
                                    <th>Total (GHS)</th>
                                    <th>{{ $total }}</th>
                                    <th colspan="7"></th>
                                </tfoot> --}}
                        </table>
                    </div>
                    <hr />

                </div>
            </div>
        </div>
    </div>
@endsection

@section('page-scripts')
    <!-- leaflet js-->
    <script src="{{ asset('assets/app/map/geojsonhint.js') }}"></script>
    <script src="{{ asset('assets/app/map/geojsonUtil.js') }}"></script>
    <script src="{{ asset('assets/app/map/leaflet/leaflet.js') }}"></script>
    <script src="{{ asset('assets/app/map/leaflet/OverPassLayer.bundle.js') }}"></script>
    <script src="{{ asset('assets/app/map/leaflet/Leaflet.Control.Custom.js') }}"></script>
    <script src="{{ asset('assets/app/map/leaflet/leaflet-geoman.min.js') }}"></script>
    <script src="{{ asset('assets/app/map/leaflet/L.Control.SlideMenu.js') }}"></script>
    <script src="{{ asset('assets/app/map/leaflet/easy-button.js') }}"></script>
    <script src="{{ asset('assets/app/map/leaflet/osmtogeojson.js') }}"></script>
    <script src="{{ asset('assets/app/map/StyleFactory.js') }}"></script>
    <script src="{{ asset('assets/app/map/MapTileProvider.js') }}"></script>
    <script src="{{ asset('assets/app/map/MapController.js?v=' . \Illuminate\Support\Str::random(5)) }}"></script>

    <script>
        var map = L.map('map').setView([0, 0], 2);

        L.tileLayer('https://{s}.tile.openstreetmap.org/{z}/{x}/{y}.png', {
            maxZoom: 19,
            attribution: '© OpenStreetMap contributors'
        }).addTo(map);

        var properties = @json($data['properties']);

        properties.forEach(function(property) {
            if (property.latitude && property.longitude) {
                var totalBill = property.total_bills || 0;
                var totalPaid = property.total_payments || 0;
                var markerColor;

                if (totalPaid >= totalBill && totalBill > 0) {
                    markerColor = 'blue';
                } else if (totalPaid > 0 && totalPaid < totalBill) {
                    markerColor = 'yellow';
                } else {
                    markerColor = 'red';
                }

                var customIcon = L.icon({
                    iconUrl: `https://raw.githubusercontent.com/pointhi/leaflet-color-markers/master/img/marker-icon-${markerColor}.png`,
                    shadowUrl: 'https://cdnjs.cloudflare.com/ajax/libs/leaflet/1.7.1/images/marker-shadow.png',
                    iconSize: [25, 41],
                    iconAnchor: [12, 41],
                    popupAnchor: [1, -34],
                    shadowSize: [41, 41]
                });

                var marker = L.marker([property.latitude, property.longitude], {
                    icon: customIcon
                }).addTo(map);

                var popup = L.popup().setContent(`
                    <b>Property No.:</b> ${property.property_no}<br>
                    <b>Owner:</b> ${property.owner}<br>
                    <th>Location:</b> ${property.location}<br>
                    <b>Status:</b> ${markerColor === 'blue' ? 'Fully Paid' : markerColor === 'yellow' ? 'Partial Payment' : 'No Payment'}
                `);

                marker.on('mouseover', function(e) {
                    popup.setLatLng(e.latlng).openOn(map);
                });

                marker.on('mouseout', function() {
                    map.closePopup(popup);
                });
            }
        });

        var bounds = L.latLngBounds(properties.map(function(property) {
            return [property.latitude, property.longitude];
        }));

        map.fitBounds(bounds);
    </script>

    <script src="https://cdn.jsdelivr.net/npm/chart.js"></script>
    <script src="https://cdn.jsdelivr.net/npm/apexcharts"></script>
@endsection

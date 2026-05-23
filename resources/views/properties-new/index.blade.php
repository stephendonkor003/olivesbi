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
                            <a href="{{ route('new-properties.create') }}" class="btn btn-sm btn-primary">
                                <i class="fa fa-plus me-1"></i> Add Property
                            </a>
                            {{-- <a href="{{ route('properties.import') }}" class="btn btn-sm btn-success">
                                <i class="fa fa-upload me-1"></i> Import Properties
                            </a> --}}
                            {{-- <a href="{{ route('properties.map') }}" class="btn btn-sm btn-warning">
                                <i class="fa fa-plus me-1"></i> View Property On Map
                            </a> --}}
                        </div>
                    @endcan
                </div>
            </div>
        </div>

        @if(!auth()->user()->assembly_code)
        <div class="row">
            <div class="col-md-12">
                <div class="card">
                    <div class="card-body">
                        <div class="col-xxl-12">
                            <form method="GET" action="{{ route('new-properties.index') }}" class="mb-3">
                                <div class="row align-items-end g-3">
                                    <!-- Entity Type -->
{{--                                    <div class="col-md-4">--}}
{{--                                        <label for="entity_type" class="form-label">Entity Type</label>--}}
{{--                                        <select name="entity_type" id="entity_type" class="form-select">--}}
{{--                                            <option value="">All</option>--}}
{{--                                            @foreach ($data['entityTypes'] as $type)--}}
{{--                                                <option value="{{ $type->id }}"--}}
{{--                                                    {{ request('entity_type') == $type->id ? 'selected' : '' }}>--}}
{{--                                                    {{ $type->name }}--}}
{{--                                                </option>--}}
{{--                                            @endforeach--}}
{{--                                        </select>--}}
{{--                                    </div>--}}

                                    <!-- Assembly -->
                                    <div class="col-md-4">
                                        <label for="assembly" class="form-label">Assembly</label>
                                        <select name="assembly" id="assembly" class="form-select">
                                            <option value="">All</option>
                                            @foreach ($data['assemblies'] as $assembly)
                                                <option value="{{ $assembly->assembly_code }}"
                                                    {{ request('assembly') == $assembly->assembly_code ? 'selected' : '' }}>
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
        @endif

        <div class="col-md-12 mb-5 ml-3">
            <label for="map" class="mb-3 d-block" style="font-size: 16px;">
                Rate Payer Properties On Map (
                <span style="color: blue; font-weight: bold;">Blue:</span> Full Payment,
                <span style="color: rgb(195, 195, 5); font-weight: bold;">Yellow:</span> Partial Payment,
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
                    <div class="card-body px-0">
                        <div class="table-responsive active-projects user-tbl  dt-filter">
                            <table id="file-export" class="table table-bordered text-nowrap w-100"
                                data-ajax="{{ route('new-properties.index', ['assembly' => request('assembly')]) }}" data-server-side="true"
                                data-processing="true">
                                <thead>
                                    <tr>
                                        <th data-orderable="false" data-searchable="false" data-name="DT_RowIndex"
                                            data-data="DT_RowIndex">S/N</th>
                                        <th data-name="erms_id" data-data="erms_id">ERMS ID</th>
                                        <th data-name="account_no" data-data="account_no">Account No</th>
                                        <th data-name="hseno" data-data="hseno">House No.</th>
                                        <th data-name="digitaladdress" data-data="digitaladdress">Digital Address</th>
                                        <th data-name="location" data-data="location">Location</th>
                                        <th data-orderable="false" data-searchable="false" data-name="fullname"
                                            data-data="fullname">Property Owner</th>
                                        <th data-orderable="false" data-searchable="false" data-name="action"
                                            data-data="action">Actions</th>
                                    </tr>
                                </thead>
                                <tbody>
                                </tbody>
                            </table>
                        </div>
                        <hr />
                    </div>
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

        fetch("{{ route('properties.mapData') }}")
            .then(response => response.json())
            .then(properties => {
                let bounds = [];

                properties.forEach(function(property) {
                    if (property.latitude && property.longitude) {
                        let totalBill = property.total_bills || 0;
                        let totalPaid = property.total_payments || 0;
                        let markerColor;

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
                        <b>Location:</b> ${property.location}<br>
                        <b>Status:</b> ${markerColor === 'blue' ? 'Fully Paid' : markerColor === 'yellow' ? 'Partial Payment' : 'No Payment'}
                    `);

                        marker.on('mouseover', function(e) {
                            popup.setLatLng(e.latlng).openOn(map);
                        });

                        marker.on('mouseout', function() {
                            map.closePopup(popup);
                        });

                        bounds.push([property.latitude, property.longitude]);
                    }
                });

                if (bounds.length > 0) {
                    map.fitBounds(bounds);
                }
            })
            .catch(error => {
                console.error('Error loading property data:', error);
            });
    </script>

    <script src="https://cdn.jsdelivr.net/npm/chart.js"></script>
    <script src="https://cdn.jsdelivr.net/npm/apexcharts"></script>
@endsection

@extends('layout.base')

@section('page-styles')
    <link href="{{ asset('assets/app/map/leaflet/leaflet.css') }}" rel="stylesheet">
    <link href="{{ asset('assets/app/map/leaflet/OverPassLayer.css') }}" rel="stylesheet">
    <link href="{{ asset('assets/app/map/leaflet/leaflet-geoman.css') }}" rel="stylesheet">
    <link href="{{ asset('assets/app/map/leaflet/L.Control.SlideMenu.css') }}" rel="stylesheet">
@endsection

@section('page-content')
    <div class="container-fluid">
        <div class="row">
            <div class="card">
                <!-- HEADER SECTION -->
                <div class="card-body border-bottom pb-3">
                    <div class="d-flex justify-content-between align-items-center mb-2">
                        <div>
                            <h4 class="fw-bold text-primary mb-1">
                                <i class="ri-building-line me-2"></i> Business Management
                            </h4>

                            <p class="mb-0 text-muted fs-14">
                                You are Viewing all Businesses Record from your
                                central database repository.
                            </p>

                        </div>
                        @can('businesses.create')
                            <a href="{{ route('new-businesses.create') }}" class="btn btn-sm btn-primary">
                                <i class="fa fa-plus me-1"></i> Add Business
                            </a>
                        @endcan
                    </div>
                </div>
            </div>

            <div class="col-md-12 ml-3">
                <label for="map" class="mb-3 d-block" style="font-size: 16px;">
                    Rate Payer Businesses On Map (
                    <span style="color: blue; font-weight: bold;">Blue:</span> Full Payment,
                    <span style="color: rgb(195, 195, 5); font-weight: bold;">Yellow:</span> Partial Payment,
                    <span style="color: red; font-weight: bold;">Red:</span> No Payment)
                </label>
            </div>

            <div id="map" class="shadow rounded" style="height: 600px;margin-bottom:30px"></div>

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
                            data-ajax="{{ route('new-businesses.index') }}" data-server-side="true" data-processing="true">
                            <thead>
                                <tr>
                                    <th data-orderable="false" data-searchable="false" data-name="DT_RowIndex"
                                        data-data="DT_RowIndex">S/N</th>
                                    <th data-name="erms_id" data-data="erms_id">ERMS ID</th>
                                    <th data-name="account_no" data-data="account_no">Account No.</th>
                                    <th data-name="business_name" data-data="business_name">Business Name</th>
                                    <th data-name="location" data-data="location">Location</th>
                                    <th data-name="phone_no" data-data="phone_no">Phone</th>
                                    <th data-orderable="false" data-searchable="false" data-name="fullname"
                                        data-data="fullname">Business Owner</th>
                                    <th data-orderable="false" data-searchable="false" data-name="action"
                                        data-data="action">Actions</th>
                                </tr>
                            </thead>
                            <tbody>
                            </tbody>
                        </table>
                    </div>
                </div>
            </div>
        </div>
    </div>
@endsection

@section('page-scripts')
    <script src="{{ asset('assets/vendor/datatables/js/jquery.dataTables.min.js') }}"></script>
    <script src="{{ asset('assets/vendor/datatables/js/dataTables.buttons.min.js') }}"></script>
    <script src="{{ asset('assets/vendor/datatables/js/buttons.html5.min.js') }}"></script>
    <script src="{{ asset('assets/vendor/datatables/js/jszip.min.js') }}"></script>
    <script src="{{ asset('assets/js/plugins-init/datatables.init.js') }}"></script>
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

        fetch("{{ route('businesses.mapData') }}")
            .then(response => response.json())
            .then(businesses => {
                let bounds = [];

                businesses.forEach(function(business) {
                    if (business.latitude && business.longitude) {
                        let totalBill = business.total_bills || 0;
                        let totalPaid = business.total_payments || 0;
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

                        var marker = L.marker([business.latitude, business.longitude], {
                            icon: customIcon
                        }).addTo(map);

                        var popup = L.popup().setContent(`
                            <b>Business No.:</b> ${business.business_no}<br>
                            <b>Owner:</b> ${business.owner}<br>
                            <b>Location:</b> ${business.location}<br>
                            <b>Status:</b> ${
                                markerColor === 'blue' ? 'Fully Paid' :
                                markerColor === 'yellow' ? 'Partial Payment' :
                                'No Payment'
                            }
                        `);

                        marker.on('mouseover', function(e) {
                            popup.setLatLng(e.latlng).openOn(map);
                        });

                        marker.on('mouseout', function() {
                            map.closePopup(popup);
                        });

                        bounds.push([business.latitude, business.longitude]);
                    }
                });

                if (bounds.length > 0) {
                    map.fitBounds(bounds);
                }
            })
            .catch(error => {
                console.error('Error loading business data:', error);
            });
    </script>
@endsection

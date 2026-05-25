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
                            <i class="ri-settings-5-line me-2"></i> Assembly Setup - Configurations
                        </h4>

                        <p class="mb-0 text-muted fs-14">
                            Manage your assembly here.
                        </p>
                    </div>
                    @can('assemblies.create')
                        <div>
                            <a href="{{ route('assembly.index') }}" class="btn btn-primary btn-sm ms-2">
                                <i class="ri-arrow-go-back-line"></i> Back
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
                        <form class="row g-3 needs-validation" action="{{ route('assembly.update', $assembly) }}"
                            method="POST">
                            @csrf

                            <div class="col-md-6 mb-3">
                                <label for="name" class="form-label">Assembly Name</label>
                                <input type="text" class="form-control" id="name" name="name"
                                    value="{{ $assembly->name }}" readonly>
                            </div>

                            <div class="col-md-6 mb-3">
                                <label for="assembly_code" class="form-label">Assembly Code</label>
                                <input type="text" class="form-control" id="assembly_code" name="assembly_code"
                                    value="{{ $assembly->assembly_code }}" readonly>
                            </div>

                            <div class="col-md-6 mb-3">
                                <label for="regional" class="form-label">Region Name</label>
                                <input type="text" class="form-control" id="regional" name="regional"
                                    value="{{ $assembly->region->name ?? 'N/A' }}" readonly>
                            </div>

                            <div class="col-md-6 mb-3">
                                <label for="regional_code" class="form-label">Regional Code</label>
                                <input type="text" class="form-control" id="regional_code" name="regional_code"
                                    value="{{ $assembly->regional_code }}" readonly>
                            </div>

                            <div class="col-md-6 mb-3">
                                <label for="supervisor" class="form-label">Assembly Admin</label>
                                <input type="text" class="form-control" id="supervisor" name="supervisor"
                                    value="{{ $assembly->assemblySupervisor->name ?? 'N/A' }} - {{ $assembly->assemblySupervisor->phone ?? 'N/A' }}"
                                    readonly>
                            </div>

                            <div class="col-md-6 mb-3">
                                <label for="supervisor" class="form-label">Assembly Category</label>
                                <input type="text" class="form-control" id="supervisor" name="supervisor"
                                    value="{{ $assembly->assembly_category ?? 'N/A' }}" readonly>
                            </div>

                            <div class="col-md-6 mb-3">
                                <label for="address" class="form-label">Address</label>
                                <input type="text" class="form-control" id="address" name="address"
                                    value="{{ $assembly->address ?? '' }}" readonly>
                            </div>

                            <div class="col-md-6 mb-3">
                                <label for="phone" class="form-label">Phone</label>
                                <input type="text" class="form-control" id="phone" name="phone"
                                    value="{{ $assembly->phone ?? '' }}" readonly>
                            </div>

                            <div class="col-md-6 mb-3">
                                <label for="status" class="form-label">Status</label>
                                <input type="text" class="form-control" id="status" name="status"
                                    value="{{ $assembly->status }}" readonly>
                            </div>

                            <div class="mb-4 col-md-6">
                                <label class="form-label">Bank Name</label>
                                <input type="text" class="form-control @error('bank_name') is-invalid @enderror"
                                    placeholder="Enter Assembly Bank Name" id="bank_name" name="bank_name"
                                    value="{{ $assembly->bank_name }}" readonly>

                                @error('bank_name')
                                    <div class="alert alert-danger">{{ $message }}</div>
                                @enderror
                            </div>

                            <div class="mb-4 col-md-6">
                                <label class="form-label">Bank Number</label>
                                <input type="text" class="form-control @error('bank_number') is-invalid @enderror"
                                    placeholder="Enter Assembly Bank Number" id="bank_number" name="bank_number"
                                    value=" {{ $assembly->bank_number }}" readonly>

                                @error('bank_number')
                                    <div class="alert alert-danger">{{ $message }}</div>
                                @enderror
                            </div>

                            <div class="mb-4 col-md-6">
                                <label class="form-label">Bank Branch</label>
                                <input type="text" class="form-control @error('bank_branch') is-invalid @enderror"
                                    placeholder="Enter Assembly Bank Branch" id="bank_branch" name="bank_branch"
                                    value="{{ $assembly->bank_branch }}" readonly>

                                @error('bank_branch')
                                    <div class="alert alert-danger">{{ $message }}</div>
                                @enderror
                            </div>

                            <div class="col-md-6 mb-3">
                            </div>

                            <div class="col-md-12">
                                <div class="row">
                                    <div class="col-md-6">
                                        @if (isset($assembly->logo))
                                            <label>Assembly Logo</label>
                                            <img src="{{ asset('storage/images/logo/' . $assembly->logo) }}"
                                                width="300" height="340" style="border-radius: 10px;">
                                        @else
                                            <h4 style="color:red">No logo uploaded for the selected assembly</h4>
                                        @endif
                                    </div>
                                </div>
                            </div>
                        </form>
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
        const map = L.map('map').setView([7.95277, -1.03071], 6);

        L.tileLayer('https://{s}.tile.openstreetmap.org/{z}/{x}/{y}.png', {
            maxZoom: 19,
            attribution: 'Â© OpenStreetMap contributors'
        }).addTo(map);

        const rawData = @json($assembly->geo_coordinate);
        const parsedData = JSON.parse(rawData);
        const boundaries = formatBoundaries(parsedData);

        const allBounds = [];
        boundaries.forEach(boundary => {
            if (boundary.type === 'polygon') {
                const coordinates = JSON.parse(boundary.coordinates).map(coord => [coord[1], coord[
                    0]]); // Swap latitude and longitude
                console.log('Polygon coordinates:', coordinates);
                L.polygon(coordinates, {
                    color: 'blue',
                    fillColor: '#3388ff',
                    fillOpacity: 0.5
                }).addTo(map);
                allBounds.push(...coordinates);
            } else if (boundary.type === 'circle') {
                const center = JSON.parse(boundary.coordinates).center;
                const radius = JSON.parse(boundary.coordinates).radius;
                L.circle([center.lat, center.lng], {
                    radius: radius,
                    color: 'red',
                    fillColor: '#f03',
                    fillOpacity: 0.5
                }).addTo(map);
                allBounds.push([center.lat, center.lng]);
            } else if (boundary.type === 'line') {
                const lineCoordinates = JSON.parse(boundary.coordinates).map(coord => [coord[1], coord[
                    0]]); // Swap latitude and longitude
                L.polyline(lineCoordinates, {
                    color: 'green'
                }).addTo(map);
                allBounds.push(...lineCoordinates);
            }
        });

        if (allBounds.length > 0) {
            const bounds = L.latLngBounds(allBounds);
            map.fitBounds(bounds);
        }

        function formatBoundaries(rawData) {
            const formattedBoundaries = rawData.map(data => {
                if (Array.isArray(data)) {
                    const isPolygon = Array.isArray(data[0]) && Array.isArray(data[data.length - 1]) &&
                        data[0][0] === data[data.length - 1][0] &&
                        data[0][1] === data[data.length - 1][1];

                    if (isPolygon) {
                        return {
                            type: 'polygon',
                            coordinates: JSON.stringify(data)
                        };
                    } else if (Array.isArray(data[0])) {
                        return {
                            type: 'line',
                            coordinates: JSON.stringify(data)
                        };
                    }
                } else if (typeof data === 'object' && data.center && data.radius) {
                    return {
                        type: 'circle',
                        coordinates: JSON.stringify({
                            center: data.center,
                            radius: data.radius
                        })
                    };
                }

                return {
                    type: 'unknown',
                    coordinates: JSON.stringify(data)
                };
            });

            return formattedBoundaries;
        }
    </script>
@endsection

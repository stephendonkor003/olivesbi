@extends('layout.base')

@section('page-styles')
    <link rel="stylesheet" href="https://unpkg.com/leaflet/dist/leaflet.css" />
    <link rel="stylesheet" href="https://unpkg.com/leaflet-draw/dist/leaflet.draw.css" />
    <script src="https://unpkg.com/leaflet/dist/leaflet.js"></script>
    <script src="https://unpkg.com/leaflet-draw/dist/leaflet.draw.js"></script>
@endsection

@section('page-content')
    <div class="container-fluid">
        <div class="card">

            <!-- HEADER SECTION -->
            <div class="card-body border-bottom pb-3">
                <div class="d-flex justify-content-between align-items-center mb-2">
                    <div>
                        <h4 class="fw-bold text-primary mb-1">
                            <i class="ri-community-line me-2"></i> Division Settings
                        </h4>

                        <p class="mb-0 text-muted fs-14">
                            You are Adding A New Division Record to your
                            central database repository.
                        </p>

                    </div>
                    @can('divisions.create')
                        <a href="{{ route('new-divisions.index') }}" class="btn btn-sm btn-primary">
                            <i class="fa fa-arrow-left me-1"></i> Back
                        </a>
                    @endcan
                </div>
            </div>
        </div>
        <div class="row">
            <div class="col-md-12">
                <div class="card">
                    <div class="card-body">
                        <form class="row g-3 needs-validation" method="POST"
                            action="{{ route('new-divisions.update', $division) }}">
                            @csrf

                            <div class="col-md-6 mb-3">
                                <label for="name" class="form-label">Division Name</label>
                                <input type="text" class="form-control @error('name') is-invalid @enderror"
                                    id="name" placeholder="Enter division name" name="name"
                                    value="{{ $division->name }}" readonly>

                                @error('name')
                                    <span class="invalid-feedback" role="alert">
                                        {{ $message }}
                                    </span>
                                @enderror
                            </div>

                            <div class="col-md-6 mb-3">
                                <label for="code" class="form-label">Division Code</label>
                                <input type="text" class="form-control @error('code') is-invalid @enderror"
                                    id="code" placeholder="Enter division code" name="code"
                                    value="{{ $division->code }}" readonly>

                                @error('code')
                                    <span class="invalid-feedback" role="alert">
                                        {{ $message }}
                                    </span>
                                @enderror
                            </div>

                            <div class="col-md-6 mb-3">
                                <label for="assembly_code" class="form-label">Assembly Code</label>
                                <input type="text" class="form-control @error('assembly_code') is-invalid @enderror"
                                    id="assembly_code" name="assembly_code" placeholder="Enter block assembly_code"
                                    value="{{ $division->assembly->name ?? '' }}" readonly>

                                @error('assembly_code')
                                    <span class="invalid-feedback" role="alert">
                                        {{ $message }}
                                    </span>
                                @enderror
                            </div>

                            <div class="col-md-12 mb-3">
                                <label for="geofence" class="form-label">Draw Geofence</label>
                                <div id="map" style="height: 400px; border: 1px solid #ccc;"></div>
                                <input type="hidden" class="form-control @error('geofence') is-invalid @enderror"
                                    id="geofence" name="geofence" placeholder="Enter geofence by drawing" readonly>

                                @error('geofence')
                                    <span class="invalid-feedback" role="alert">
                                        {{ $message }}
                                    </span>
                                @enderror
                            </div>

                            <div class="col-md-12">
                                {{-- <button type="submit" class="btn btn-primary">Update</button> --}}
                            </div>
                        </form>
                    </div>
                </div>
            </div>
        </div>
    </div>
@endsection

@section('page-scripts')
    <script>
        document.addEventListener("DOMContentLoaded", function() {
            var map = L.map('map').setView([5.6906, -0.1893], 15);

            L.tileLayer('https://{s}.tile.openstreetmap.org/{z}/{x}/{y}.png', {
                maxZoom: 19
            }).addTo(map);

            // FeatureGroup is to store editable layers
            var drawnItems = new L.FeatureGroup();
            map.addLayer(drawnItems);

            var drawControl = new L.Control.Draw({
                draw: {
                    polyline: false,
                    rectangle: false,
                    circle: false,
                    marker: false,
                    circlemarker: false,
                    polygon: false
                }
            });
            map.addControl(drawControl);

            @if (!empty($division->geofence))
                var existingGeoJSON = {!! json_encode($division->geofence) !!};

                var existingLayer = L.geoJSON(existingGeoJSON);
                existingLayer.eachLayer(function(layer) {
                    drawnItems.addLayer(layer);
                    map.fitBounds(layer.getBounds());
                });

                document.getElementById('geofence').value = JSON.stringify(existingGeoJSON);
            @endif

            map.on(L.Draw.Event.CREATED, function(e) {
                var layer = e.layer;
                drawnItems.clearLayers();
                drawnItems.addLayer(layer);

                var geojson = layer.toGeoJSON();

                if (geojson.geometry.type === "Polygon") {
                    geojson.geometry = {
                        "type": "MultiPolygon",
                        "coordinates": [geojson.geometry.coordinates]
                    };
                }

                var featureCollection = {
                    "type": "FeatureCollection",
                    "features": [geojson]
                };

                document.getElementById('geofence').value = JSON.stringify(featureCollection);
            });
        });
    </script>
@endsection

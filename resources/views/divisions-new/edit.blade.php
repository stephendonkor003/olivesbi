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
                            @method('PUT')

                            <div class="col-md-6 mb-3">
                                <label for="name" class="form-label">Division Name</label>
                                <input type="text" class="form-control @error('name') is-invalid @enderror"
                                    id="name" placeholder="Enter division name" name="name"
                                    value="{{ old('name', $division->name) }}" required>

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
                                    value="{{ old('code', $division->code) }}" required>

                                @error('code')
                                    <span class="invalid-feedback" role="alert">
                                        {{ $message }}
                                    </span>
                                @enderror
                            </div>

                            <div class="col-md-6 mb-3">
                                <label for="assembly_code" class="form-label">Assembly Code</label>
                                @if(!empty(Auth::user()->assembly_code))
                                    <select class="form-control" id="assembly_code" disabled>
                                        @foreach ($assemblies as $assembly)
                                            <option value="{{ $assembly->assembly_code }}" selected>
                                                {{ $assembly->name }} ({{ $assembly->assembly_code }})
                                            </option>
                                        @endforeach
                                    </select>
                                    <input type="hidden" name="assembly_code" value="{{ Auth::user()->assembly_code }}">
                                    <small class="text-muted d-block mt-1">Auto-assigned to your assembly</small>
                                @else
                                    <select class="form-control @error('assembly_code') is-invalid @enderror" id="assembly_code"
                                        name="assembly_code" required>
                                        <option disabled selected>Select Assembly</option>
                                        @foreach ($assemblies as $assembly)
                                            <option value="{{ $assembly->assembly_code }}"
                                                {{ old('assembly_code', $division->assembly_code) == $assembly->assembly_code ? 'selected' : '' }}>
                                                {{ $assembly->name }}
                                                ({{ $assembly->assembly_code }})
                                            </option>
                                        @endforeach
                                    </select>

                                    @error('assembly_code')
                                        <span class="invalid-feedback" role="alert">
                                            {{ $message }}
                                        </span>
                                    @enderror
                                @endif
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
                                <button type="submit" class="btn btn-primary">Update</button>
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

            // Layer to store existing divisions (read-only)
            var existingDivisionsLayer = new L.FeatureGroup();
            map.addLayer(existingDivisionsLayer);

            var drawControl = new L.Control.Draw({
                draw: {
                    polyline: false,
                    rectangle: false,
                    circle: false,
                    marker: false,
                    circlemarker: false,
                    polygon: true
                },
                edit: {
                    featureGroup: drawnItems
                }
            });
            map.addControl(drawControl);

            // Load geofence - prioritize old input (after validation failure), then existing division geofence
            @if(old('geofence'))
                // Validation failed - load the edited geofence from old input
                try {
                    var oldGeoJSON = {!! json_encode(old('geofence')) !!};

                    // Check if it's a string (needs parsing) or already an object
                    if (typeof oldGeoJSON === 'string') {
                        oldGeoJSON = JSON.parse(oldGeoJSON);
                    }

                    var oldLayer = L.geoJSON(oldGeoJSON, {
                        style: {
                            color: '#3388ff',
                            weight: 3,
                            opacity: 0.8,
                            fillOpacity: 0.3
                        }
                    });
                    oldLayer.eachLayer(function(layer) {
                        drawnItems.addLayer(layer);
                        map.fitBounds(layer.getBounds());
                    });

                    document.getElementById('geofence').value = JSON.stringify(oldGeoJSON);
                } catch (e) {
                    console.error('Error loading old geofence:', e);
                }
            @elseif (!empty($division->geofence))
                // Initial page load - load existing division geofence
                var existingGeoJSON = {!! json_encode($division->geofence) !!};

                var existingLayer = L.geoJSON(existingGeoJSON, {
                    style: {
                        color: '#3388ff',
                        weight: 3,
                        opacity: 0.8,
                        fillOpacity: 0.3
                    }
                });
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

            // Function to load existing divisions for an assembly (excluding current division)
            function loadExistingDivisions(assemblyCode) {
                if (!assemblyCode) {
                    existingDivisionsLayer.clearLayers();
                    return;
                }

                fetch(`/new-division/api/divisions/${assemblyCode}`)
                    .then(response => response.json())
                    .then(divisions => {
                        existingDivisionsLayer.clearLayers();

                        // Filter out the current division being edited
                        var currentDivisionId = {{ $division->id }};
                        var otherDivisions = divisions.filter(d => d.id !== currentDivisionId);

                        otherDivisions.forEach(division => {
                            if (division.geofence && division.geofence.features) {
                                var geojsonLayer = L.geoJSON(division.geofence, {
                                    style: {
                                        color: '#FF6B6B',
                                        weight: 2,
                                        opacity: 0.7,
                                        fillOpacity: 0.2,
                                        fillColor: '#FF6B6B'
                                    },
                                    onEachFeature: function(feature, layer) {
                                        layer.bindPopup(`<b>${division.name}</b><br>Code: ${division.code}<br><small style="color: #999;">Existing Division</small>`);
                                    }
                                });
                                existingDivisionsLayer.addLayer(geojsonLayer);
                            }
                        });

                        // Fit map to show all divisions including current one
                        var allBounds = [];
                        if (drawnItems.getLayers().length > 0) {
                            allBounds.push(drawnItems.getBounds());
                        }
                        if (existingDivisionsLayer.getLayers().length > 0) {
                            allBounds.push(existingDivisionsLayer.getBounds());
                        }

                        if (allBounds.length > 0) {
                            var combinedBounds = allBounds[0];
                            for (var i = 1; i < allBounds.length; i++) {
                                combinedBounds.extend(allBounds[i]);
                            }
                            map.fitBounds(combinedBounds, { padding: [50, 50] });
                        }
                    })
                    .catch(error => {
                        console.error('Error loading existing divisions:', error);
                    });
            }

            // Load existing divisions when assembly is selected
            document.getElementById('assembly_code').addEventListener('change', function() {
                var assemblyCode = this.value;
                loadExistingDivisions(assemblyCode);
            });

            // Load divisions on page load
            var initialAssemblyCode = '{{ old('assembly_code', $division->assembly_code) }}';
            if (initialAssemblyCode) {
                loadExistingDivisions(initialAssemblyCode);
            }
        });
    </script>
@endsection

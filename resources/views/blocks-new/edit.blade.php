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
                            <i class="ri-building-4-line me-2"></i> Blocks Settings
                        </h4>

                        <p class="mb-0 text-muted fs-14">
                            You are viewing Blocks within Divisions Record from your
                            central database repository.
                        </p>
                    </div>
                    @can('blocks.create')
                        <a href="{{ route('new-blocks.index') }}" class="btn btn-sm btn-primary">
                            <i class="ri-arrow-go-back-line me-1"></i> Back
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
                            action="{{ route('new-blocks.update', $block) }}">
                            @csrf
                            @method('PUT')

                            <input type="hidden" name="division_url" url="{{ route('ajax.division') }}">

                            <div class="col-md-6 mb-3">
                                <label for="name" class="form-label">Block Name</label>
                                <input type="text" class="form-control @error('name') is-invalid @enderror"
                                    id="name" name="name" placeholder="Enter block name" value="{{ old('name', $block->name) }}"
                                    required>

                                @error('name')
                                    <span class="invalid-feedback" role="alert">
                                        {{ $message }}
                                    </span>
                                @enderror
                            </div>

                            <div class="col-md-6 mb-3">
                                <label for="code" class="form-label">Block Code</label>
                                <input type="text" class="form-control @error('code') is-invalid @enderror"
                                    id="code" name="code" placeholder="Enter block code" value="{{ old('code', $block->code) }}"
                                    required>

                                @error('code')
                                    <span class="invalid-feedback" role="alert">
                                        {{ $message }}
                                    </span>
                                @enderror
                            </div>

                            <div class="col-md-6 mb-3">
                                <label for="assembly_code" class="form-label">Assembly Code</label>
                                @if(!empty(Auth::user()->assembly_code))
                                    <select class="form-control" id="assembly_code" name="assembly_code" disabled>
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
                                        @foreach ($assemblies as $assembly)
                                            <option value="{{ $assembly->assembly_code }}"
                                                {{ old('assembly_code', $block->assembly_code) == $assembly->assembly_code ? 'selected' : '' }}>
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

                            <div class="col-md-6 mb-3">
                                <label for="division_id" class="form-label">Division Name</label>
                                <select class="form-control @error('division_id') is-invalid @enderror" id="division_id"
                                    name="division_id" required>
                                    <option disabled>Select Division</option>
                                    @foreach ($divisions->where('assembly_code', $block->assembly_code) as $division)
                                        <option value="{{ $division->id }}"
                                            data-geofence="{{ json_encode($division->geofence) }}"
                                            {{ old('division_id', $block->division_id) == $division->id ? 'selected' : '' }}>
                                            {{ $division->name }}
                                        </option>
                                    @endforeach
                                </select>

                                @error('division_id')
                                    <span class="invalid-feedback" role="alert">
                                        {{ $message }}
                                    </span>
                                @enderror
                            </div>

                            <div class="col-md-12 mb-3">
                                <label for="geofence" class="form-label">Draw Geofence</label>
                                <div id="map" style="height: 400px; border: 1px solid #ccc; @error('geofence') border-color: #dc3545; @enderror"></div>
                                <input type="hidden" class="form-control @error('geofence') is-invalid @enderror"
                                    id="geofence" name="geofence" placeholder="Enter geofence by drawing" readonly>

                                @error('geofence')
                                    <div class="text-danger mt-2" role="alert" style="font-size: 0.875em;">
                                        <strong>{{ $message }}</strong>
                                    </div>
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
    <script src="{{ asset('assets/js/general/app.js?t=' . time()) }}"></script>

    <script>
        document.addEventListener("DOMContentLoaded", function() {
            var map = L.map('map').setView([5.6906, -0.1893], 15);

            L.tileLayer('https://{s}.tile.openstreetmap.org/{z}/{x}/{y}.png', {
                maxZoom: 19
            }).addTo(map);

            // FeatureGroup is to store editable layers
            var drawnItems = new L.FeatureGroup();
            map.addLayer(drawnItems);

            // Layer to display division boundary
            var divisionBoundaryLayer = null;

            // Layer to display existing blocks
            var existingBlocksLayer = new L.FeatureGroup();
            map.addLayer(existingBlocksLayer);

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

            // Store all divisions data for easy lookup
            var divisionsData = []; // Will be filtered based on assembly

            var assemblySelect = $('select[name="assembly_code"]');

            // Handle assembly change to load divisions
            assemblySelect.on('change', function() {
                var assemblyCode = this.value;
                var divisionSelect = document.getElementById('division_id');
                var divisionUrl = document.querySelector('input[name="division_url"]').getAttribute('url');
                var currentDivisionId = '{{ old('division_id', $block->division_id) }}';

                // Completely clear division select
                while (divisionSelect.options.length > 0) {
                    divisionSelect.remove(0);
                }

                // Add default option
                var defaultOption = document.createElement('option');
                defaultOption.disabled = true;
                defaultOption.selected = true;
                defaultOption.textContent = 'Select Division';
                divisionSelect.appendChild(defaultOption);

                // Clear division boundary from map
                if (divisionBoundaryLayer) {
                    map.removeLayer(divisionBoundaryLayer);
                    divisionBoundaryLayer = null;
                }

                if (assemblyCode) {
                    // Fetch divisions for selected assembly
                    fetch(divisionUrl, {
                        method: 'POST',
                        headers: {
                            'Content-Type': 'application/json',
                            'X-CSRF-TOKEN': document.querySelector('input[name="_token"]').value
                        },
                        body: JSON.stringify({ assembly_code: assemblyCode })
                    })
                    .then(response => response.json())
                    .then(data => {
                        if (data.success) {
                            divisionsData = data.data || [];

                            // Clear again to ensure no duplicates
                            while (divisionSelect.options.length > 1) {
                                divisionSelect.remove(1);
                            }

                            // Populate division dropdown
                            divisionsData.forEach(function(division) {
                                var option = document.createElement('option');
                                option.value = division.id;
                                option.textContent = division.name;
                                divisionSelect.appendChild(option);
                            });

                            // Restore division selection (old value or block's division)
                            if (currentDivisionId && divisionsData.findIndex((item) => item.id == currentDivisionId) !== -1) {
                                divisionSelect.value = currentDivisionId;
                                displayDivisionGeofence(currentDivisionId);
                            }
                        } else {
                            console.error('Error:', data.message);
                            alert('Failed to load divisions: ' + data.message);
                        }
                    })
                    .catch(error => {
                        console.error('Error fetching divisions:', error);
                        alert('Failed to load divisions. Please try again.');
                    });
                }
            });

            // Function to display division geofence
            function displayDivisionGeofence(divisionId) {
                // Find the selected division
                var selectedDivision = divisionsData.find(function(div) {
                    return div.id == divisionId;
                });

                // Remove previous division boundary if exists
                if (divisionBoundaryLayer) {
                    map.removeLayer(divisionBoundaryLayer);
                    divisionBoundaryLayer = null;
                }

                // Display division geofence if available
                if (selectedDivision && selectedDivision.geofence) {
                    try {
                        divisionBoundaryLayer = L.geoJSON(selectedDivision.geofence, {
                            style: {
                                color: '#3388ff',
                                weight: 3,
                                opacity: 0.8,
                                fillColor: '#3388ff',
                                fillOpacity: 0.1,
                                dashArray: '5, 5'
                            }
                        }).addTo(map);

                        // Fit map to division bounds only if no block geofence exists (neither old nor existing)
                        @if (!old('geofence') && empty($block->geofence))
                            map.fitBounds(divisionBoundaryLayer.getBounds(), {
                                padding: [50, 50]
                            });
                        @endif
                    } catch (error) {
                        console.error('Error displaying division geofence:', error);
                    }
                }

                // Load existing blocks for this division (excluding current block)
                loadExistingBlocks(divisionId);
            }

            // Function to load and display existing blocks
            function loadExistingBlocks(divisionId) {
                // Clear existing blocks layer
                existingBlocksLayer.clearLayers();

                if (!divisionId) {
                    return;
                }

                // Fetch existing blocks for this division
                fetch(`/new-block/api/blocks/${divisionId}`)
                    .then(response => response.json())
                    .then(blocks => {
                        blocks.forEach(block => {
                            // Exclude current block being edited
                            if (block.id == '{{ $block->id }}') {
                                return;
                            }

                            if (block.geofence) {
                                try {
                                    var blockLayer = L.geoJSON(block.geofence, {
                                        style: {
                                            color: '#ff0000',
                                            weight: 2,
                                            opacity: 0.8,
                                            fillColor: '#ff0000',
                                            fillOpacity: 0.2
                                        }
                                    });

                                    blockLayer.bindPopup(`<strong>${block.name}</strong><br>Code: ${block.code}`);
                                    existingBlocksLayer.addLayer(blockLayer);
                                } catch (error) {
                                    console.error(`Error displaying block ${block.name}:`, error);
                                }
                            }
                        });
                    })
                    .catch(error => {
                        console.error('Error fetching existing blocks:', error);
                    });
            }

            // Trigger assembly change on page load to populate divisions
            assemblySelect.trigger('change');

            // Load existing blocks for the current division immediately
            @if(old('division_id'))
                loadExistingBlocks('{{ old('division_id') }}');
            @elseif($block->division_id)
                loadExistingBlocks('{{ $block->division_id }}');
            @endif

            // Determine which geofence to display (old value takes precedence for validation errors)
            @if (old('geofence'))
                // Restore old geofence if validation failed
                try {
                    var oldGeofence = {!! old('geofence') !!};
                    var oldLayer = L.geoJSON(oldGeofence);
                    oldLayer.eachLayer(function(layer) {
                        drawnItems.addLayer(layer);
                    });
                    map.fitBounds(drawnItems.getBounds(), { padding: [50, 50] });
                    document.getElementById('geofence').value = {!! json_encode(old('geofence')) !!};
                } catch (error) {
                    console.error('Error restoring old geofence:', error);
                }
            @elseif (!empty($block->geofence))
                // Display existing block geofence
                var existingGeoJSON = {!! json_encode($block->geofence) !!};
                var existingLayer = L.geoJSON(existingGeoJSON);
                existingLayer.eachLayer(function(layer) {
                    drawnItems.addLayer(layer);
                    map.fitBounds(layer.getBounds());
                });
                document.getElementById('geofence').value = JSON.stringify(existingGeoJSON);
            @endif

            // Listen for division selection changes
            document.getElementById('division_id').addEventListener('change', function() {
                displayDivisionGeofence(this.value);
            });

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

@extends('layout.base')

@section('page-styles')
    <link rel="stylesheet" href="{{ asset('assets/css/autocomplete.css') }}">
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
                            <i class="ri-building-line me-2"></i> Property Management
                        </h4>

                        <p class="mb-0 text-muted fs-14">
                            You About to Add New Property Record from your
                            central database repository.
                        </p>

                    </div>
                    @can('properties.create')
                        <a href="{{ route('new-properties.index') }}" class="btn btn-sm btn-primary">
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
                            action="{{ route('new-properties.update', $property) }}">
                            @csrf
                            @method('PUT')

                            <input type="hidden" name="property_use_url" url="{{ route('rates.property-use') }}">
                            <input type="hidden" name="division_url" url="{{ route('ajax.division') }}">
                            <input type="hidden" name="block_url" url="{{ route('ajax.block') }}">
                            <input type="hidden" name="owner_id" id="owner_id" value="{{ $property->owner_id }}">
                            <input type="hidden" name="cntr_geom" id="cntr_geom">

                            <div class="col-sm-6 mb-3">
                                <div class="form-group">
                                    <label for="hseno" class="form-label">House No.</label>
                                    <input type="text" class="form-control @error('hseno') is-invalid @enderror"
                                        id="hseno" name="hseno" placeholder="House No."
                                        value="{{ old('hseno', $property->hseno) }}">

                                    @error('hseno')
                                        <span class="invalid-feedback" role="alert">
                                            {{ $message }}
                                        </span>
                                    @enderror
                                </div>
                            </div>

                            <div class="col-sm-6 mb-3">
                                <div class="form-group">
                                    <label for="digitaladdress" class="form-label">Digital Address</label>
                                    <input type="text" class="form-control @error('digitaladdress') is-invalid @enderror"
                                        id="digitaladdress" name="digitaladdress" placeholder="Digital address"
                                        value="{{ old('digitaladdress', $property->digitaladdress) }}">

                                    @error('digitaladdress')
                                        <span class="invalid-feedback" role="alert">
                                            {{ $message }}
                                        </span>
                                    @enderror
                                </div>
                            </div>

                            <div class="col-sm-6 mb-3">
                                <div class="form-group">
                                    <label for="location" class="form-label">Location</label>
                                    <input type="text" class="form-control @error('location') is-invalid @enderror"
                                        id="location" name="location" placeholder="Location"
                                        value="{{ old('location', $property->location) }}">

                                    @error('location')
                                        <span class="invalid-feedback" role="alert">
                                            {{ $message }}
                                        </span>
                                    @enderror
                                </div>
                            </div>

                            <div class="col-sm-6 mb-3">
                                <div class="form-group">
                                    <label for="streetname" class="form-label">Street Name</label>
                                    <input type="text" class="form-control" id="streetname" name="streetname"
                                        placeholder="Street name" value="{{ old('streetname', $property->streetname) }}">
                                </div>
                            </div>

                            <div class="col-sm-6 mb-3">
                                <label for="entity_type" class="form-label">Associate Owner</label>
                                <small class="form-text text-muted d-block mb-1">Search by name, email, phone, or ERMS ID (min. 2 characters)</small>
                                @php
                                    $firstname = $property->customer->fname ?? '';
                                    $lastname = $property->customer->lname ?? '';
                                    $name = $firstname . ' ' . $lastname;
                                @endphp
                                <div class="autocomplete" style="position: relative;">
                                    <input type="text" class="form-control" id="customer" name="customer"
                                        placeholder="Type at least 2 characters to search..." value="{{ old('customer',$name) }}">
                                </div>
                            </div>

                            <!-- Add Assembly field here -->
                            <div class="col-sm-6 mb-3">
                                <div class="form-group">
                                    <label for="assembly" class="form-label">Property Assembly</label>
                                    @if(!empty(Auth::user()->assembly_code))
                                        <select class="form-control" id="assembly" name="assembly_code" disabled>
                                            @foreach ($assemblies as $assembly)
                                                <option value="{{ $assembly->assembly_code }}" selected>
                                                    {{ $assembly->name }} Assembly
                                                </option>
                                            @endforeach
                                        </select>
                                        <input type="hidden" name="assembly_code" value="{{ Auth::user()->assembly_code }}">
                                        <small class="text-muted d-block mt-1">Auto-assigned to your assembly</small>
                                    @else
                                        <select class="form-control @error('assembly_code') is-invalid @enderror"
                                            id="assembly" name="assembly_code">
                                            <option disabled selected>Select Assembly</option>
                                            @foreach ($assemblies as $assembly)
                                                <option value="{{ $assembly->assembly_code }}"
                                                    {{ old('assembly_code', $property->assembly_code) == $assembly->assembly_code ? 'selected' : '' }}>
                                                    {{ $assembly->name }}
                                                    Assembly
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
                            </div>

                            <div class="col-sm-6 mb-3">
                                <label for="division_id" class="form-label">Property Division</label>
                                <select class="form-control @error('division_id') is-invalid @enderror" id="division_id"
                                    name="division_id" required>
                                    <option disabled selected>Select Division</option>
                                    @foreach ($divisions as $division)
                                        <option value="{{ $division->id }}"
                                            {{ old('division_id', $property->polygon?->block?->division?->id) == $division->id ? 'selected' : '' }}>
                                            {{ $division->name }}</option>
                                    @endforeach
                                </select>

                                @error('division_id')
                                    <span class="invalid-feedback" role="alert">
                                        {{ $message }}
                                    </span>
                                @enderror
                            </div>

                            <div class="col-sm-6 mb-3">
                                <label for="block_id" class="form-label">Property Block</label>
                                <select class="form-control @error('block_id') is-invalid @enderror" id="block_id"
                                    name="block_id" required>
                                    <option disabled selected>Select Block</option>
                                    @foreach ($blocks as $block)
                                        <option value="{{ $block->id }}"
                                            geofence="{{ json_encode($block->geofence) ?? '' }}"
                                            {{ old('block_id', $property->polygon?->block?->id) == $block->id ? 'selected' : '' }}>
                                            {{ $block->name }}</option>
                                    @endforeach
                                </select>

                                @error('block_id')
                                    <span class="invalid-feedback" role="alert">
                                        {{ $message }}
                                    </span>
                                @enderror
                            </div>

                            <div class="col-sm-6 mb-3">
                                <div class="form-group">
                                    <label for="rated" class="form-label">Is Property Rated?</label>
                                    <select class="form-control" id="is_rated" name="is_rated">
                                        <option disabled selected>Select If Rated</option>
                                        <option value="1" {{ old('is_rated', $property->is_rated) == '1' ? 'selected' : '' }}>Yes</option>
                                        <option value="0" {{ old('is_rated', $property->is_rated) == '0' ? 'selected' : '' }}>No</option>
                                    </select>
                                    <small class="form-text text-muted">Select "Yes" to search for an existing rated property within the selected block</small>
                                    @error('valuation_number')
                                    <span class="invalid-feedback" role="alert">
                                        {{ $message }}
                                    </span>
                                    @enderror
                                </div>
                            </div>

                            <div class="col-sm-6 mb-3">
                                <div class="form-group">
                                    <label for="rateable_value" class="form-label">Rateable Value</label>
                                    <input type="text" class="form-control @error('rateable_value') is-invalid @enderror" id="rateable_value" name="rateable_value"
                                        placeholder="Rateable value" value="{{ old('rateable_value', $property->rateable_value) }}">
                                    @error('rateable_value')
                                    <span class="invalid-feedback" role="alert">
                                        {{ $message }}
                                    </span>
                                    @enderror
                                </div>
                            </div>

                            <div class="col-sm-6 mb-3">
                                <label for="longitude" class="form-label">Longitude</label>
                                <input type="text" class="form-control @error('longitude') is-invalid @enderror"
                                    id="longitude" name="longitude" placeholder="Longitude"
                                    value="{{ old('longitude', $property->polygon->cntr_x_coord ?? '') }}" readonly>

                                @error('longitude')
                                    <span class="invalid-feedback" role="alert">
                                        {{ $message }}
                                    </span>
                                @enderror
                            </div>

                            <div class="col-sm-6 mb-3">
                                <label for="latitude" class="form-label">Latitude</label>
                                <input type="text" class="form-control @error('latitude') is-invalid @enderror"
                                    id="latitude" name="latitude" placeholder="Latitude"
                                    value="{{ old('latitude', $property->polygon->cntr_y_coord ?? '') }}" readonly>

                                @error('latitude')
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

                            <div class="row mb-3">
                                <div class="col-sm-12">
                                    <button type="submit" class="btn btn-primary">Update</button>
                                </div>
                            </div>
                        </form>
                    </div>
                </div>
            </div>
        </div>

        <div class="modal fade" id="ratedPropertiesModal" tabindex="-1" aria-labelledby="ratedPropertiesLabel"
             aria-hidden="true" data-bs-backdrop="static">
            <div class="modal-dialog modal-xl modal-dialog-scrollable">
                <div class="modal-content">
                    <div class="modal-header">
                        <h5 class="modal-title fw-bold text-primary" id="ratedPropertiesLabel">
                            Assessed Properties On Map
                        </h5>
                        <button type="button" class="btn-close" data-bs-dismiss="modal" aria-label="Close"></button>
                    </div>
                    <div class="modal-body">
                        <div class="row mb-3 align-items-end">
                            <div class="col-sm-6">
                                <label for="valuation_number" class="form-label">Search By Valuation Number</label>
                                <div class="autocomplete">
                                    <input type="text"
                                           class="form-control @error('valuation_number') is-invalid @enderror"
                                           id="valuation_number" name="valuation_number" placeholder="Valuation Number"
                                           value="{{ old('valuation_number', $property->valuation_number ?? '') }}">
                                </div>
                                @error('valuation_number')
                                <div class="text-danger mt-2" role="alert" style="font-size: 0.875em;">
                                    <strong>{{ $message }}</strong>
                                </div>
                                @enderror
                            </div>

                            <div class="col-sm-6">
                                <button type="submit" id="searchPropertyBtn" class="btn btn-primary">Search
                                    Property
                                </button>
                            </div>
                        </div>

                        <div id="ratedPropertiesMap"
                             style="height: 500px; border: 1px solid #ccc;"
                             data-route="{{ route('ajax.rated-properties') }}">
                        </div>
                    </div>
                </div>
            </div>
        </div>
    </div>
@endsection

@section('page-scripts')
    <script src="{{ asset('assets/js/general.js?v1=' . time()) }}"></script>
    <script src="https://unpkg.com/@turf/turf/turf.min.js"></script>

    <script>
        $(document).ready(function() {
            $.ajaxSetup({
                headers: {
                    'X-CSRF-TOKEN': $('meta[name="csrf-token"]').attr('content')
                }
            });

            function autocomplete(inp, arr) {
                var currentFocus;
                inp.addEventListener("input", function(e) {
                    var a, b, i, val = this.value;
                    closeAllLists();
                    if (!val || val.length < 2) {
                        return false;
                    }
                    currentFocus = -1;
                    a = document.createElement("DIV");
                    a.setAttribute("id", this.id + "autocomplete-list");
                    a.setAttribute("class", "autocomplete-items");
                    a.style.maxHeight = "300px";
                    a.style.overflowY = "auto";
                    a.style.border = "1px solid #d4d4d4";
                    a.style.borderTop = "none";
                    a.style.zIndex = "99";
                    a.style.position = "absolute";
                    a.style.backgroundColor = "#fff";
                    a.style.boxShadow = "0 4px 6px rgba(0,0,0,0.1)";
                    this.parentNode.appendChild(a);

                    var matchCount = 0;
                    var maxResults = 10; // Limit results for better UX

                    for (i = 0; i < arr.length && matchCount < maxResults; i++) {
                        var customer = arr[i];
                        var searchStr = val.toUpperCase();
                        var fullName = (customer.full_name || '').toUpperCase();
                        var email = (customer.email || '').toUpperCase();
                        var phone = (customer.phone || '').toUpperCase();
                        var ermsId = (customer.erms_id || '').toUpperCase();

                        // Check if search term matches any field
                        var matchField = null;
                        var matchIndex = -1;

                        if (fullName.indexOf(searchStr) !== -1) {
                            matchField = 'name';
                            matchIndex = fullName.indexOf(searchStr);
                        } else if (email.indexOf(searchStr) !== -1) {
                            matchField = 'email';
                            matchIndex = email.indexOf(searchStr);
                        } else if (phone.indexOf(searchStr) !== -1) {
                            matchField = 'phone';
                            matchIndex = phone.indexOf(searchStr);
                        } else if (ermsId.indexOf(searchStr) !== -1) {
                            matchField = 'erms_id';
                            matchIndex = ermsId.indexOf(searchStr);
                        }

                        if (matchField) {
                            matchCount++;
                            b = document.createElement("DIV");
                            b.style.padding = "10px";
                            b.style.cursor = "pointer";
                            b.style.borderBottom = "1px solid #f0f0f0";
                            b.style.transition = "background-color 0.2s";

                            // Add hover effect
                            b.addEventListener("mouseenter", function() {
                                this.style.backgroundColor = "#f8f9fa";
                            });
                            b.addEventListener("mouseleave", function() {
                                this.style.backgroundColor = "#fff";
                            });

                            // Build display HTML with highlighted match
                            var displayHTML = '<div style="font-weight: 600;">';
                            if (matchField === 'name') {
                                var nameText = customer.full_name || '';
                                var matchStart = nameText.toUpperCase().indexOf(searchStr);
                                displayHTML += nameText.substr(0, matchStart) +
                                    '<strong style="background-color: #ffeb3b;">' + nameText.substr(matchStart, val.length) + '</strong>' +
                                    nameText.substr(matchStart + val.length);
                            } else {
                                displayHTML += (customer.full_name || 'No Name');
                            }
                            displayHTML += '</div>';

                            // Add secondary info
                            displayHTML += '<div style="font-size: 0.85em; color: #666; margin-top: 4px;">';
                            displayHTML += '<span><i class="ri-account-circle-line"></i> ' + (customer.erms_id || 'N/A') + '</span>';
                            if (customer.email) {
                                displayHTML += ' | <span><i class="ri-mail-line"></i> ' + customer.email + '</span>';
                            }
                            if (customer.phone) {
                                displayHTML += ' | <span><i class="ri-phone-line"></i> ' + customer.phone + '</span>';
                            }
                            displayHTML += '</div>';

                            // Highlight matched field
                            if (matchField === 'email' && customer.email) {
                                displayHTML = displayHTML.replace(customer.email,
                                    '<strong style="background-color: #ffeb3b;">' + customer.email + '</strong>');
                            } else if (matchField === 'phone' && customer.phone) {
                                displayHTML = displayHTML.replace(customer.phone,
                                    '<strong style="background-color: #ffeb3b;">' + customer.phone + '</strong>');
                            } else if (matchField === 'erms_id' && customer.erms_id) {
                                displayHTML = displayHTML.replace(customer.erms_id,
                                    '<strong style="background-color: #ffeb3b;">' + customer.erms_id + '</strong>');
                            }

                            b.innerHTML = displayHTML;
                            b.innerHTML += "<input type='hidden' value='" + (customer.full_name || '') + "'>";
                            b.innerHTML += "<input type='hidden' value='" + customer.id + "'>";

                            b.addEventListener("click", function(e) {
                                inp.value = this.getElementsByTagName("input")[0].value;
                                var customerId = this.getElementsByTagName("input")[1].value;
                                document.getElementById('owner_id').value = customerId;
                                closeAllLists();
                            });
                            a.appendChild(b);
                        }
                    }

                    // Show "no results" message if no matches
                    if (matchCount === 0) {
                        b = document.createElement("DIV");
                        b.innerHTML = '<div style="padding: 10px; color: #999;">No owners found matching "' + val + '"</div>';
                        a.appendChild(b);
                    }

                    // Show info message about result count
                    if (matchCount >= maxResults) {
                        b = document.createElement("DIV");
                        b.style.padding = "8px 10px";
                        b.style.backgroundColor = "#f0f0f0";
                        b.style.borderTop = "1px solid #ddd";
                        b.style.fontStyle = "italic";
                        b.innerHTML = '<div style="color: #666; font-size: 0.85em;">Showing first ' + maxResults + ' results. Type more to refine search.</div>';
                        a.appendChild(b);
                    }
                });

                /*execute a function presses a key on the keyboard:*/
                inp.addEventListener("keydown", function(e) {
                    var x = document.getElementById(this.id + "autocomplete-list");
                    if (x) x = x.getElementsByTagName("div");
                    if (e.keyCode == 40) {
                        /*If the arrow DOWN key is pressed,
                        increase the currentFocus variable:*/
                        currentFocus++;
                        /*and and make the current item more visible:*/
                        addActive(x);
                    } else if (e.keyCode == 38) { //up
                        /*If the arrow UP key is pressed,
                        decrease the currentFocus variable:*/
                        currentFocus--;
                        /*and and make the current item more visible:*/
                        addActive(x);
                    } else if (e.keyCode == 13) {
                        /*If the ENTER key is pressed, prevent the form from being submitted,*/
                        e.preventDefault();
                        if (currentFocus > -1) {
                            /*and simulate a click on the "active" item:*/
                            if (x) x[currentFocus].click();
                        }
                    }
                });

                function addActive(x) {
                    /*a function to classify an item as "active":*/
                    if (!x) return false;
                    /*start by removing the "active" class on all items:*/
                    removeActive(x);
                    if (currentFocus >= x.length) currentFocus = 0;
                    if (currentFocus < 0) currentFocus = (x.length - 1);
                    /*add class "autocomplete-active":*/
                    x[currentFocus].classList.add("autocomplete-active");
                }

                function removeActive(x) {
                    /*a function to remove the "active" class from all autocomplete items:*/
                    for (var i = 0; i < x.length; i++) {
                        x[i].classList.remove("autocomplete-active");
                    }
                }

                function closeAllLists(elmnt) {
                    /*close all autocomplete lists in the document,
                    except the one passed as an argument:*/
                    var x = document.getElementsByClassName("autocomplete-items");
                    for (var i = 0; i < x.length; i++) {
                        if (elmnt != x[i] && elmnt != inp) {
                            x[i].parentNode.removeChild(x[i]);
                        }
                    }
                }

                /*execute a function when someone clicks in the document:*/
                document.addEventListener("click", function(e) {
                    closeAllLists(e.target);
                });
            }

            /*An array containing all the customer ids in the customer table:*/
            var customers = @json($customers);

            autocomplete(document.getElementById("customer"), customers);

        });
    </script>

    <script>
        // Make map and drawnItems globally accessible
        var propertyMap;
        var propertyDrawnItems;
        var propertyCurrentPolygon = null;
        var propertySelectedMarker = null;

        // Global function to display block geofence
        function displayBlockGeofence(blockSelect) {
            var selectedOption = blockSelect.options[blockSelect.selectedIndex];
            var geofence = selectedOption.getAttribute('geofence');

            if (geofence) {
                try {
                    var geojsonData = JSON.parse(geofence);
                    propertyDrawnItems.clearLayers();

                    var geoLayer = L.geoJSON(geojsonData, {
                        style: function (feature) {
                            return {
                                color: "#00008B",
                                weight: 2,
                                opacity: 1,
                                fillColor: "#ADD8E6",
                                fillOpacity: 0.6
                            };
                        }
                    }).addTo(propertyDrawnItems);

                    propertyCurrentPolygon = geojsonData.features[0];
                    propertyMap.fitBounds(geoLayer.getBounds());
                    document.getElementById('geofence').value = JSON.stringify(geojsonData);
                } catch (e) {
                    console.error("Invalid geofence JSON", e);
                }
            }
        }

        document.addEventListener("DOMContentLoaded", function() {
            propertyMap = L.map('map').setView([5.6906, -0.1893], 15);

            L.tileLayer('https://{s}.tile.openstreetmap.org/{z}/{x}/{y}.png', {
                maxZoom: 19
            }).addTo(propertyMap);

            propertyDrawnItems = new L.FeatureGroup();
            propertyMap.addLayer(propertyDrawnItems);

            var drawControl = new L.Control.Draw({
                draw: {
                    polyline: false,
                    rectangle: false,
                    circle: false,
                    marker: false,
                    circlemarker: false,
                    polygon: false
                },
                edit: {
                    featureGroup: propertyDrawnItems
                }
            });
            propertyMap.addControl(drawControl);

            // Load polygon and marker if editing existing property (or old values)
            @if(old('geofence'))
                // Restore old geofence if validation failed
                try {
                    var oldGeofence = {!! old('geofence') !!};
                    propertyDrawnItems.clearLayers();

                    var geoLayer = L.geoJSON(oldGeofence, {
                        style: function (feature) {
                            return {
                                color: "#00008B",
                                weight: 2,
                                opacity: 1,
                                fillColor: "#ADD8E6",
                                fillOpacity: 0.6
                            };
                        }
                    }).addTo(propertyDrawnItems);

                    propertyCurrentPolygon = oldGeofence.features[0];
                    document.getElementById('geofence').value = {!! json_encode(old('geofence')) !!};
                    propertyMap.fitBounds(geoLayer.getBounds());
                } catch (error) {
                    console.error('Error restoring old geofence:', error);
                }
            @else
                // Load existing property geofence
                var existingGeofence = @json($property->polygon->block->geofence ?? null);
                var existingLat = {{ $property->polygon->cntr_y_coord ?? 'null' }};
                var existingLng = {{ $property->polygon->cntr_x_coord ?? 'null' }};
                var existingPoint = @json($property->polygon->cntr_geom ?? null);

                if (existingGeofence) {
                    try {
                        propertyDrawnItems.clearLayers();

                        var geoLayer = L.geoJSON(existingGeofence, {
                            style: {
                                color: "#00008B",
                                weight: 2,
                                opacity: 1,
                                fillColor: "#ADD8E6",
                                fillOpacity: 0.6
                            }
                        }).addTo(propertyDrawnItems);

                        propertyCurrentPolygon = existingGeofence.features[0];
                        propertyMap.fitBounds(geoLayer.getBounds());
                        document.getElementById('geofence').value = JSON.stringify(existingGeofence);
                    } catch (e) {
                        console.error("Invalid geofence JSON", e);
                    }
                }
            @endif

            // Restore marker if coordinates exist (old values take precedence)
            @if(old('latitude') && old('longitude'))
                var lat = parseFloat('{{ old('latitude') }}');
                var lng = parseFloat('{{ old('longitude') }}');

                if (!isNaN(lat) && !isNaN(lng)) {
                    propertySelectedMarker = L.marker([lat, lng]).addTo(propertyDrawnItems);
                    propertyMap.setView([lat, lng], 17);
                }
            @elseif(isset($property->polygon->cntr_y_coord) && isset($property->polygon->cntr_x_coord))
                var existingLat = {{ $property->polygon->cntr_y_coord }};
                var existingLng = {{ $property->polygon->cntr_x_coord }};
                var existingPoint = @json($property->polygon->cntr_geom);

                if (existingLat && existingLng) {
                    propertySelectedMarker = L.marker([existingLat, existingLng]).addTo(propertyDrawnItems);
                    document.getElementById("latitude").value = parseFloat(existingLat).toFixed(8);
                    document.getElementById("longitude").value = parseFloat(existingLng).toFixed(8);
                    document.getElementById("cntr_geom").value = existingPoint;
                }
            @endif

            // When user draws new polygon
            propertyMap.on(L.Draw.Event.CREATED, function(e) {
                var layer = e.layer;
                propertyDrawnItems.clearLayers();
                propertyDrawnItems.addLayer(layer);

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

                propertyCurrentPolygon = geojson;
                document.getElementById('geofence').value = JSON.stringify(featureCollection);
            });

            // Load polygon from block select
            document.getElementById('block_id').addEventListener('change', function() {
                displayBlockGeofence(this);

                // Clear "Is Property Rated?" when block changes
                var isRatedSelect = document.getElementById('is_rated');
                if (isRatedSelect.value !== '' && isRatedSelect.value !== 'Select If Rated') {
                    isRatedSelect.value = '';
                    isRatedSelect.selectedIndex = 0;
                }
            });

            // Clear dependent fields when assembly changes
            document.getElementById('assembly').addEventListener('change', function() {
                var divisionSelect = document.getElementById('division_id');
                var blockSelect = document.getElementById('block_id');
                var isRatedSelect = document.getElementById('is_rated');

                // Clear division
                divisionSelect.innerHTML = '<option disabled selected>Select Division</option>';

                // Clear block
                blockSelect.innerHTML = '<option disabled selected>Select Block</option>';

                // Clear is_rated
                isRatedSelect.value = '';
                isRatedSelect.selectedIndex = 0;

                // Clear map layers
                propertyDrawnItems.clearLayers();
                propertyCurrentPolygon = null;
                document.getElementById('geofence').value = '';
            });

            // Clear dependent fields when division changes
            document.getElementById('division_id').addEventListener('change', function() {
                var blockSelect = document.getElementById('block_id');
                var isRatedSelect = document.getElementById('is_rated');

                // Clear block (will be repopulated by general.js)
                // Don't clear innerHTML here as general.js will populate it

                // Clear is_rated
                isRatedSelect.value = '';
                isRatedSelect.selectedIndex = 0;

                // Clear map layers
                propertyDrawnItems.clearLayers();
                propertyCurrentPolygon = null;
                document.getElementById('geofence').value = '';
            });

            propertyMap.on("click", function(e) {
                if (!propertyCurrentPolygon) {
                    alert("In order to pick a coordinate, you first need to select property block.");
                    return;
                }

                var latlng = e.latlng;
                var point = turf.point([latlng.lng, latlng.lat]);

                if (turf.booleanPointInPolygon(point, propertyCurrentPolygon)) {
                    var lat = parseFloat(latlng.lat).toFixed(8);
                    var lng = parseFloat(latlng.lng).toFixed(8);

                    document.getElementById("latitude").value = lat;
                    document.getElementById("longitude").value = lng;

                    document.getElementById("cntr_geom").value = `POINT (${latlng.lng} ${latlng.lat})`;

                    if (propertySelectedMarker) {
                        propertyDrawnItems.removeLayer(propertySelectedMarker);
                    }

                    propertySelectedMarker = L.marker([latlng.lat, latlng.lng]).addTo(propertyDrawnItems);
                } else {
                    alert("The selected point is outside the polygon area.");
                }
            });
        });

        // Restore old values on validation error
        $(document).ready(function() {
            @if(old('assembly_code'))
            // Trigger assembly change to load divisions
            var assemblySelect = $('select[name="assembly_code"]');
            if (assemblySelect.length > 0) {
                assemblySelect.trigger('change');

                // Wait for divisions to load, then select old division
                setTimeout(function() {
                    @if(old('division_id'))
                    $('select[name="division_id"]').val('{{ old('division_id') }}');
                    $('select[name="division_id"]').trigger('change');

                    // Wait for blocks to load, then select old block
                    setTimeout(function() {
                        @if(old('block_id'))
                        var blockSelect = document.getElementById('block_id');
                        blockSelect.value = '{{ old('block_id') }}';

                        // Geofence already restored above, just need to ensure it's set
                        // Wait for block geofence to load on map, then restore marker if coordinates exist
                        setTimeout(function() {
                            @if(old('latitude') && old('longitude'))
                            var lat = parseFloat('{{ old('latitude') }}');
                            var lng = parseFloat('{{ old('longitude') }}');

                            if (!isNaN(lat) && !isNaN(lng) && propertyMap && propertyDrawnItems) {
                                // Remove any existing marker
                                if (propertySelectedMarker) {
                                    propertyDrawnItems.removeLayer(propertySelectedMarker);
                                }

                                // Place marker at old coordinates
                                propertySelectedMarker = L.marker([lat, lng]).addTo(propertyDrawnItems);

                                // Center map on marker (don't zoom too much, keep block visible)
                                propertyMap.setView([lat, lng], 17);
                            }
                            @endif
                        }, 500);
                        @endif
                    }, 500);
                    @endif
                }, 500);
            }
            @endif

            @if(old('owner_id'))
            // Restore owner_id hidden field
            $('#owner_id').val('{{ old('owner_id') }}');
            @endif
        });

        // Autocomplete for valuation numbers
        function autocomplete_2(inp, arr) {
            var currentFocus;
            inp.addEventListener("input", function (e) {
                var a, b, i, val = this.value;
                closeAllLists();
                if (!val) {
                    return false;
                }
                currentFocus = -1;
                a = document.createElement("DIV");
                a.setAttribute("id", this.id + "autocomplete-list");
                a.setAttribute("class", "autocomplete-items");
                this.parentNode.appendChild(a);
                for (i = 0; i < arr.length; i++) {
                    if (arr[i].substr(0, val.length).toUpperCase() == val.toUpperCase()) {
                        b = document.createElement("DIV");
                        b.innerHTML = "<strong>" + arr[i].substr(0, val.length) + "</strong>";
                        b.innerHTML += arr[i].substr(val.length);
                        b.innerHTML += "<input type='hidden' value='" + arr[i] + "'>";
                        b.addEventListener("click", function (e) {
                            inp.value = this.getElementsByTagName("input")[0].value;
                            closeAllLists();
                        });
                        a.appendChild(b);
                    }
                }
            });

            inp.addEventListener("keydown", function (e) {
                var x = document.getElementById(this.id + "autocomplete-list");
                if (x) x = x.getElementsByTagName("div");
                if (e.keyCode == 40) {
                    currentFocus++;
                    addActive(x);
                } else if (e.keyCode == 38) {
                    currentFocus--;
                    addActive(x);
                } else if (e.keyCode == 13) {
                    e.preventDefault();
                    if (currentFocus > -1) {
                        if (x) x[currentFocus].click();
                    }
                }
            });

            function addActive(x) {
                if (!x) return false;
                removeActive(x);
                if (currentFocus >= x.length) currentFocus = 0;
                if (currentFocus < 0) currentFocus = (x.length - 1);
                x[currentFocus].classList.add("autocomplete-active");
            }

            function removeActive(x) {
                for (var i = 0; i < x.length; i++) {
                    x[i].classList.remove("autocomplete-active");
                }
            }

            function closeAllLists(elmnt) {
                var x = document.getElementsByClassName("autocomplete-items");
                for (var i = 0; i < x.length; i++) {
                    if (elmnt != x[i] && elmnt != inp) {
                        x[i].parentNode.removeChild(x[i]);
                    }
                }
            }

            document.addEventListener("click", function (e) {
                closeAllLists(e.target);
            });
        }

        var valuationNumbers = @json($valuationNumbers);
        autocomplete_2(document.getElementById("valuation_number"), valuationNumbers);

        // Handle rated properties modal
        document.addEventListener("DOMContentLoaded", function () {
            var ratedSelect = document.getElementById("is_rated");
            var map = null;
            var markers = [];
            var allProperties = [];

            ratedSelect.addEventListener("change", function () {
                if (this.value == "1") {
                    // Validate that assembly, division, and block are selected
                    var assemblyCode = document.getElementById('assembly').value;
                    var divisionId = document.getElementById('division_id').value;
                    var blockId = document.getElementById('block_id').value;

                    if (!assemblyCode || assemblyCode === 'Select Assembly') {
                        alert('Please select an Assembly first before searching for rated properties.');
                        this.value = '';
                        return;
                    }

                    if (!divisionId || divisionId === 'Select Division') {
                        alert('Please select a Division first before searching for rated properties.');
                        this.value = '';
                        return;
                    }

                    if (!blockId || blockId === 'Select Block') {
                        alert('Please select a Block first before searching for rated properties.');
                        this.value = '';
                        return;
                    }

                    var modal = new bootstrap.Modal(document.getElementById("ratedPropertiesModal"));
                    modal.show();

                    setTimeout(initRatedPropertiesMap, 300);
                }
            });

            var searchBtn = document.getElementById("searchPropertyBtn");
            if (searchBtn) {
                searchBtn.addEventListener("click", function (e) {
                    e.preventDefault();
                    searchProperty();
                });
            }

            var valuationInput = document.getElementById("valuation_number");
            if (valuationInput) {
                valuationInput.addEventListener("keypress", function (e) {
                    if (e.key === "Enter") {
                        e.preventDefault();
                        searchProperty();
                    }
                });
            }

            function initRatedPropertiesMap() {
                var mapDiv = document.getElementById("ratedPropertiesMap");

                // Initialize map only once
                if (!mapDiv.dataset.initialized) {
                    map = L.map('ratedPropertiesMap').setView([5.6906, -0.1893], 15);
                    L.tileLayer("https://{s}.tile.openstreetmap.org/{z}/{x}/{y}.png", {
                        maxZoom: 19,
                        attribution: '© OpenStreetMap contributors'
                    }).addTo(map);
                    mapDiv.dataset.initialized = true;
                }

                // Always reload properties based on current selections
                loadAllProperties();
            }

            function loadAllProperties() {
                // Get selected assembly, division, and block
                var assemblyCode = document.getElementById('assembly').value;
                var divisionId = document.getElementById('division_id').value;
                var blockId = document.getElementById('block_id').value;

                // Build query parameters
                var params = new URLSearchParams();
                if (assemblyCode) params.append('assembly_code', assemblyCode);
                if (divisionId) params.append('division_id', divisionId);
                if (blockId) params.append('block_id', blockId);

                var url = "{!! route('ajax.rated-properties') !!}?" + params.toString();

                fetch(url)
                    .then(response => response.json())
                    .then(data => {
                        console.log('Loaded properties:', data);
                        allProperties = data;

                        if (data.length === 0) {
                            showAlert('No rated properties found within the selected block.', 'info');
                        }

                        displayAllMarkers(data);
                    })
                    .catch(error => {
                        console.error('Error fetching properties:', error);
                        showAlert('Error loading properties. Please try again.', 'danger');
                    });
            }

            function displayAllMarkers(properties) {
                clearMarkers();

                properties.forEach(function (property) {
                    if (property.Latitude && property.Longitude) {
                        var lat = parseFloat(property.Latitude);
                        var lng = parseFloat(property.Longitude);

                        if (!isNaN(lat) && !isNaN(lng)) {
                            addMarker(property, lat, lng);
                        }
                    }
                });

                if (markers.length > 0) {
                    var group = new L.featureGroup(markers);
                    map.fitBounds(group.getBounds().pad(0.1));
                }
            }

            function escapeHtml(text) {
                if (!text) return text;
                var div = document.createElement('div');
                div.textContent = text;
                return div.innerHTML;
            }

            function addMarker(property, lat, lng) {
                console.log('Adding marker for:', property.Valuation_Number, 'at', lat, lng);

                if (!map) {
                    console.error('Map is not initialized');
                    return;
                }

                var marker = L.marker([lat, lng])
                    .addTo(map)
                    .bindPopup(
                        `<b>${escapeHtml(property.Owner_Name ?? 'No Name')}</b><br>
                     <strong>Valuation #:</strong> ${escapeHtml(property.Valuation_Number ?? 'N/A')}<br>
                     <strong>Address:</strong> ${escapeHtml(property.Digital_Address ?? 'N/A')}<br>
                     <strong>Rateable Value:</strong> ${escapeHtml(property.Rateable_Value ?? 'N/A')}`
                    )
                    .bindTooltip(
                        `Owner: ${escapeHtml(property.Owner_Name ?? 'No Name')}<br>
                     Digital Address: ${escapeHtml(property.Digital_Address ?? 'N/A')}<br>
                     Valuation #: ${escapeHtml(property.Valuation_Number ?? 'N/A')}`, {
                            permanent: false,
                            direction: 'top',
                            offset: [0, -10]
                        }
                    );

                marker.on("click", function () {
                    fillFormFields(property, lat, lng);

                    setTimeout(function () {
                        var modalElement = document.getElementById("ratedPropertiesModal");
                        var modalInstance = bootstrap.Modal.getInstance(modalElement);
                        if (modalInstance) {
                            modalInstance.hide();
                        }
                    }, 500);
                });

                markers.push(marker);
                console.log('Marker added. Total markers:', markers.length);
            }

            function clearMarkers() {
                console.log('Clearing markers. Current count:', markers.length);

                if (!map) {
                    console.error('Map is not initialized');
                    return;
                }

                markers.forEach(function (marker) {
                    map.removeLayer(marker);
                });
                markers = [];
                console.log('All markers cleared');
            }

            function searchProperty() {
                var valuationNumber = document.getElementById("valuation_number").value.trim();
                var assemblyCode = document.getElementById('assembly').value.trim();

                if (!assemblyCode) {
                    showAlert('Please select an assembly', 'warning');
                    return;
                }

                if (!valuationNumber) {
                    showAlert('Please enter a valuation number', 'warning');
                    return;
                }

                if (!map) {
                    showAlert('Map is not initialized. Please try again.', 'danger');
                    return;
                }

                var searchBtn = document.getElementById("searchPropertyBtn");
                var originalBtnText = searchBtn.innerHTML;
                searchBtn.innerHTML = '<span class="spinner-border spinner-border-sm me-2"></span>Searching...';
                searchBtn.disabled = true;

                fetch(
                    `{{ route('ajax.search-rated-property') }}?assembly_code=${assemblyCode}&valuation_number=${encodeURIComponent(valuationNumber)}`
                )
                    .then(response => {
                        console.log('Response status:', response.status);
                        if (!response.ok) {
                            throw new Error('Property not found');
                        }
                        return response.json();
                    })
                    .then(property => {
                        console.log('Found property:', property);

                        if (!property || !property.Latitude || !property.Longitude) {
                            showAlert('Property found but has no location coordinates', 'warning');
                            searchBtn.innerHTML = originalBtnText;
                            searchBtn.disabled = false;
                            return;
                        }

                        var lat = parseFloat(property.Latitude);
                        var lng = parseFloat(property.Longitude);

                        console.log('Parsed coordinates:', lat, lng);

                        if (isNaN(lat) || isNaN(lng)) {
                            showAlert('Invalid coordinates for this property', 'warning');
                            searchBtn.innerHTML = originalBtnText;
                            searchBtn.disabled = false;
                            return;
                        }

                        clearMarkers();

                        addMarker(property, lat, lng);

                        map.setView([lat, lng], 18);

                        setTimeout(function () {
                            if (markers.length > 0) {
                                markers[0].openPopup();
                            }
                        }, 100);

                        fillFormFields(property, lat, lng);

                        showAlert('Property found and displayed on map', 'success');

                        searchBtn.innerHTML = originalBtnText;
                        searchBtn.disabled = false;
                    })
                    .catch(error => {
                        console.error('Error searching property:', error);
                        showAlert('Property not found. Please check the valuation number.', 'danger');
                        searchBtn.innerHTML = originalBtnText;
                        searchBtn.disabled = false;
                    });
            }

            function fillFormFields(property, lat, lng) {
                var latField = document.getElementById("latitude");
                var lngField = document.getElementById("longitude");
                var rateableField = document.getElementById("rateable_value");
                var addressField = document.getElementById("digitaladdress");
                var valuationField = document.getElementById("valuation_number");
                var cntrGeomField = document.getElementById("cntr_geom");

                if (latField) latField.value = lat.toFixed(8).trim();
                if (lngField) lngField.value = lng.toFixed(8).trim();
                if (rateableField) rateableField.value = (property.Rateable_Value ?? '').toString().trim();
                if (addressField) addressField.value = (property.Digital_Address ?? '').toString().trim();
                if (valuationField) valuationField.value = (property.Valuation_Number ?? '').toString().trim();
                if (cntrGeomField) cntrGeomField.value = `POINT (${lng} ${lat})`;

                // Place marker on the main property map
                if (propertyMap && propertyDrawnItems) {
                    // Remove any existing marker
                    if (propertySelectedMarker) {
                        propertyDrawnItems.removeLayer(propertySelectedMarker);
                    }

                    // Place new marker at selected coordinates
                    propertySelectedMarker = L.marker([lat, lng]).addTo(propertyDrawnItems);

                    // Center map on marker
                    propertyMap.setView([lat, lng], 17);
                }
            }

            function showAlert(message, type) {
                var existingAlert = document.getElementById("searchAlert");
                if (existingAlert) {
                    existingAlert.remove();
                }

                var modalBody = document.querySelector("#ratedPropertiesModal .modal-body");
                if (!modalBody) {
                    console.error('Modal body not found');
                    return;
                }

                var alertDiv = document.createElement("div");
                alertDiv.id = "searchAlert";
                alertDiv.className = `alert alert-${type} alert-dismissible fade show mb-3`;
                alertDiv.innerHTML = `
                    ${message}
                    <button type="button" class="btn-close" data-bs-dismiss="alert" aria-label="Close"></button>
                `;

                modalBody.prepend(alertDiv);

                setTimeout(function () {
                    if (alertDiv.parentElement) {
                        alertDiv.remove();
                    }
                }, 5000);
            }

            // Ensure map invalidates size when modal is shown
            var modalElement = document.getElementById("ratedPropertiesModal");
            if (modalElement) {
                modalElement.addEventListener('shown.bs.modal', function () {
                    if (map) {
                        setTimeout(function () {
                            map.invalidateSize();
                        }, 100);
                    }
                });
            }
        });
    </script>
@endsection

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
                            <i class="ri-building-line me-2"></i> Business Management
                        </h4>

                        <p class="mb-0 text-muted fs-14">
                            You About to Add New Business Record from your
                            central database repository.
                        </p>
                    </div>
                    @can('businesses.create')
                        <a href="{{ route('new-businesses.index') }}" class="btn btn-sm btn-primary">
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
                        <form class="row g-3 needs-validation" method="POST" action="{{ route('new-businesses.store') }}">
                            @csrf

                            <input type="hidden" name="property_use_url" url="{{ route('ajax.division') }}">
                            <input type="hidden" name="division_url" url="{{ route('ajax.division') }}">
                            <input type="hidden" name="block_url" url="{{ route('ajax.block') }}">
                            <input type="hidden" name="owner_id" id="owner_id" value="{{ old('owner_id') }}">
                            <input type="hidden" name="cntr_geom" id="cntr_geom" value="{{ old('cntr_geom') }}">
                            <input type="hidden" id="old_division_id" value="{{ old('division_id') }}">
                            <input type="hidden" id="old_block_id" value="{{ old('block_id') }}">
                            <input type="hidden" id="old_assembly_code" value="{{ old('assembly_code') }}">


                            <div class="col-sm-6 mb-3">
                                <label for="business_name" class="form-label">Business Name</label>
                                <input type="text" class="form-control @error('business_name') is-invalid @enderror" value="{{old('business_name')}}" name="business_name" placeholder="Business Name">
                                @error('business_name')
                                    <span class="invalid-feedback" role="alert">
                                        {{ $message }}
                                    </span>
                                @enderror
                            </div>

                            <div class="col-sm-6 mb-3">
                                <label for="registration_no" class="form-label">Registration No.</label>
                                <input type="text" class="form-control @error('registration_no') is-invalid @enderror" value="{{old('registration_no')}}" name="registration_no"
                                    placeholder="Registration No.">
                                @error('registration_no')
                                <span class="invalid-feedback" role="alert">
                                        {{ $message }}
                                    </span>
                                @enderror
                            </div>

                            <div class="col-sm-6 mb-3">
                                <label for="permit_no" class="form-label">Permit No.</label>
                                <input type="text" class="form-control @error('permit_no') is-invalid @enderror" value="{{old('permit_no')}}" name="permit_no" placeholder="Permit No.">
                                @error('permit_no')
                                <span class="invalid-feedback" role="alert">
                                        {{ $message }}
                                    </span>
                                @enderror
                            </div>

                            <div class="col-sm-6 mb-3">
                                <label for="hseno" class="form-label">House Number</label>
                                <input type="text" class="form-control @error('hseno') is-invalid @enderror" value="{{old('hseno')}}" name="hseno" placeholder="House Number">
                                @error('hseno')
                                <span class="invalid-feedback" role="alert">
                                        {{ $message }}
                                    </span>
                                @enderror
                            </div>

                            <div class="col-sm-6 mb-3">
                                <label for="digitaladdress" class="form-label">Digital Address</label>
                                <input type="text" class="form-control @error('digitaladdress') is-invalid @enderror" value="{{old('digitaladdress')}}" name="digitaladdress"
                                    placeholder="Digital Address">
                                @error('digitaladdress')
                                <span class="invalid-feedback" role="alert">
                                        {{ $message }}
                                    </span>
                                @enderror
                            </div>

                            <div class="col-sm-6 mb-3">
                                <label for="location" class="form-label">Location</label>
                                <input type="text" class="form-control @error('location') is-invalid @enderror" value="{{old('location')}}" name="location" placeholder="Location">
                                @error('location')
                                <span class="invalid-feedback" role="alert">
                                        {{ $message }}
                                    </span>
                                @enderror
                            </div>

                            <div class="col-sm-6 mb-3">
                                <label for="street_name" class="form-label">Street Name</label>
                                <input type="text" class="form-control @error('street_name') is-invalid @enderror" value="{{old('street_name')}}" name="street_name" placeholder="Street Name">
                                @error('street_name')
                                <span class="invalid-feedback" role="alert">
                                        {{ $message }}
                                    </span>
                                @enderror
                            </div>

                            @include('shared.global-location-fields')

                            <div class="col-sm-6 mb-3">
                                <label for="phone_no" class="form-label">Phone Number</label>
                                <input type="text" class="form-control @error('phone_no') is-invalid @enderror" value="{{old('phone_no')}}" name="phone_no" placeholder="Phone Number">
                                @error('phone_no')
                                <span class="invalid-feedback" role="alert">
                                        {{ $message }}
                                    </span>
                                @enderror
                            </div>

                            <div class="col-sm-6 mb-3">
                                <label for="address" class="form-label">Address</label>
                                <input type="text" class="form-control @error('address') is-invalid @enderror" value="{{old('address')}}" name="address" placeholder="Business Address">
                                @error('address')
                                <span class="invalid-feedback" role="alert">
                                        {{ $message }}
                                    </span>
                                @enderror
                            </div>

                            <div class="col-sm-6 mb-3">
                                <label for="email" class="form-label">Email</label>
                                <input type="email" class="form-control @error('email') is-invalid @enderror" value="{{old('email')}}" name="email" placeholder="Email">
                                @error('email')
                                <span class="invalid-feedback" role="alert">
                                        {{ $message }}
                                    </span>
                                @enderror
                            </div>

                            <div class="col-sm-6 mb-3">
                                <label for="customer" class="form-label">Associate Owner</label>
                                <div class="autocomplete">
                                    <input type="text" class="form-control @error('owner_id') is-invalid @enderror" value="{{old('customer')}}" id="customer" name="customer"
                                        placeholder="Enter associate owner name">
                                </div>
                                @error('owner_id')
                                <span class="invalid-feedback" role="alert">
                                        {{ $message }}
                                    </span>
                                @enderror
                            </div>

                            <div class="col-sm-6 mb-3">
                                <div class="form-group">
                                    <label for="assembly" class="form-label">Assembly</label>
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
                                                <option value="{{ $assembly->assembly_code }}" @selected(old('assembly_code', '') == $assembly->assembly_code)>{{ $assembly->name }}
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
                                </select>

                                @error('block_id')
                                    <span class="invalid-feedback" role="alert">
                                        {{ $message }}
                                    </span>
                                @enderror
                            </div>

                            <div class="col-sm-6 mb-3">
                                <label for="longitude" class="form-label">Longitude</label>
                                <input type="text" class="form-control @error('longitude') is-invalid @enderror"
                                    id="longitude" name="longitude" placeholder="Longitude" value="{{old('longitude')}}" readonly>

                                @error('longitude')
                                    <span class="invalid-feedback" role="alert">
                                        {{ $message }}
                                    </span>
                                @enderror
                            </div>

                            <div class="col-sm-6 mb-3">
                                <label for="latitude" class="form-label">Latitude</label>
                                <input type="text" class="form-control @error('latitude') is-invalid @enderror"
                                    id="latitude" name="latitude" placeholder="Latitude" value="{{old('latitude')}}" readonly>

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

                            <div class="col-sm-12">
                                <button type="submit" class="btn btn-primary">Save</button>
                            </div>
                        </form>
                    </div>
                </div>
            </div>
        </div>
    </div>
@endsection

@section('page-scripts')
    <script src="{{ asset('assets/js/general/business.js') }}"></script>
    <script src="{{ asset('assets/js/general.js?v1=1234') }}"></script>
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
                inp.addEventListener("input", function (e) {
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

                            b.addEventListener("click", function (e) {
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

            var customers = @json($customers);

            autocomplete(document.getElementById("customer"), customers);

        });
    </script>

    <script>
        // Make map variables globally accessible for restoration
        var businessMap;
        var businessDrawnItems;
        var currentPolygon = null;
        var selectedMarker = null;

        // Global function to display block geofence
        function displayBlockGeofence(blockSelect) {
            var selectedOption = blockSelect.options[blockSelect.selectedIndex];
            var geofence = selectedOption.getAttribute('geofence');

            if (geofence) {
                try {
                    var geojsonData = JSON.parse(geofence);
                    businessDrawnItems.clearLayers();

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
                    }).addTo(businessDrawnItems);

                    currentPolygon = geojsonData.features[0];
                    businessMap.fitBounds(geoLayer.getBounds());
                    document.getElementById('geofence').value = JSON.stringify(geojsonData);
                } catch (e) {
                    console.error("Invalid geofence JSON", e);
                }
            }
        }

        document.addEventListener("DOMContentLoaded", function() {
            businessMap = L.map('map').setView([5.6906, -0.1893], 15);

            L.tileLayer('https://{s}.tile.openstreetmap.org/{z}/{x}/{y}.png', {
                maxZoom: 19
            }).addTo(businessMap);

            businessDrawnItems = new L.FeatureGroup();
            businessMap.addLayer(businessDrawnItems);

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
                    featureGroup: businessDrawnItems
                }
            });
            businessMap.addControl(drawControl);

            // When user draws new polygon
            businessMap.on(L.Draw.Event.CREATED, function(e) {
                var layer = e.layer;
                businessDrawnItems.clearLayers();
                businessDrawnItems.addLayer(layer);

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

                currentPolygon = geojson;
                document.getElementById('geofence').value = JSON.stringify(featureCollection);
            });

            // Load polygon from block select
            document.getElementById('block_id').addEventListener('change', function() {
                displayBlockGeofence(this);
            });

            // Clear dependent fields when assembly changes
            document.getElementById('assembly').addEventListener('change', function () {
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
            document.getElementById('division_id').addEventListener('change', function () {
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

            businessMap.on("click", function(e) {
                if (!currentPolygon) {
                    alert("In order to pick a coordinate, you first need to select property block.");
                    return;
                }

                var latlng = e.latlng;
                var point = turf.point([latlng.lng, latlng.lat]);

                if (turf.booleanPointInPolygon(point, currentPolygon)) {
                    var lat = parseFloat(latlng.lat).toFixed(8);
                    var lng = parseFloat(latlng.lng).toFixed(8);

                    document.getElementById("latitude").value = lat;
                    document.getElementById("longitude").value = lng;

                    document.getElementById("cntr_geom").value = `POINT (${latlng.lng} ${latlng.lat})`;

                    if (selectedMarker) {
                        businessDrawnItems.removeLayer(selectedMarker);
                    }

                    selectedMarker = L.marker([latlng.lat, latlng.lng]).addTo(businessDrawnItems);
                } else {
                    alert("The selected point is outside the polygon area.");
                }
            });
        });

        // Restore old values on validation error
        $(document).ready(function () {
            @if(old('assembly_code'))
            // Trigger assembly change to load divisions
            var assemblySelect = $('select[name="assembly_code"]');
            if (assemblySelect.length > 0) {
                assemblySelect.trigger('change');

                // Wait for divisions to load, then select old division
                setTimeout(function () {
                    @if(old('division_id'))
                    $('select[name="division_id"]').val('{{ old('division_id') }}');
                    $('select[name="division_id"]').trigger('change');

                    // Wait for blocks to load, then select old block
                    setTimeout(function () {
                        @if(old('block_id'))
                        var blockSelect = document.getElementById('block_id');
                        blockSelect.value = '{{ old('block_id') }}';

                        // Restore block geofence directly from old value if available
                        @if(old('geofence'))
                            try {
                            var oldGeofence = {!! old('geofence') !!};
                            businessDrawnItems.clearLayers();

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
                            }).addTo(businessDrawnItems);

                            currentPolygon = oldGeofence.features[0];
                            document.getElementById('geofence').value = {!! json_encode(old('geofence')) !!};
                            businessMap.fitBounds(geoLayer.getBounds());
                        } catch (error) {
                            console.error('Error restoring old geofence:', error);
                            // Fallback to displayBlockGeofence if direct restoration fails
                            if (typeof displayBlockGeofence === 'function') {
                                displayBlockGeofence(blockSelect);
                            }
                        }
                        @else
                        // No old geofence, try to load from block option
                        if (typeof displayBlockGeofence === 'function') {
                            displayBlockGeofence(blockSelect);
                        }
                        @endif

                        // Wait for block geofence to load on map, then restore marker if coordinates exist
                        setTimeout(function () {
                            @if(old('latitude') && old('longitude'))
                            var lat = parseFloat('{{ old('latitude') }}');
                            var lng = parseFloat('{{ old('longitude') }}');

                            if (!isNaN(lat) && !isNaN(lng) && businessMap && businessDrawnItems) {
                                // Remove any existing marker
                                if (selectedMarker) {
                                    businessDrawnItems.removeLayer(selectedMarker);
                                }

                                // Place marker at old coordinates
                                selectedMarker = L.marker([lat, lng]).addTo(businessDrawnItems);

                                // Center map on marker (don't zoom too much, keep block visible)
                                businessMap.setView([lat, lng], 17);
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

            // Auto-trigger cascade for pre-assigned assembly
            @if(!empty(Auth::user()->assembly_code))
            var assemblySelect = $('select[name="assembly_code"]');
            if (assemblySelect.length > 0) {
                assemblySelect.trigger('change');
            }
            @endif
        });
    </script>
@endsection

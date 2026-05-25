@php
    $assemblies = $assemblies ?? collect();
@endphp

@once
    <link href="{{ asset('assets/app/map/leaflet/leaflet.css') }}" rel="stylesheet">
    <script src="{{ asset('assets/app/map/leaflet/leaflet.js') }}"></script>
    <script src="{{ asset('assets/app/map/leaflet/osmtogeojson.js') }}"></script>
@endonce

<style>
    .content-grid {
        grid-template-columns: 1fr !important;
    }
    .content-grid > .side-panel-stack {
        display: none;
    }
    .parcel-workspace {
        display: grid;
        grid-template-columns: minmax(0, 1.9fr) minmax(300px, .55fr);
        gap: 1rem;
        align-items: start;
    }
    .parcel-map-panel,
    .parcel-form-panel,
    .parcel-tip-panel {
        background: #fff;
        border: 1px solid var(--line);
        border-radius: 14px;
        box-shadow: 0 1px 8px rgba(26,92,42,.04);
    }
    .parcel-map-panel { overflow: hidden; }
    .parcel-toolbar {
        padding: 1rem;
        border-bottom: 1px solid var(--line);
        display: grid;
        grid-template-columns: minmax(0, 1fr) auto;
        gap: .75rem;
        align-items: center;
    }
    .parcel-search-box {
        display: grid;
        grid-template-columns: minmax(0, 1fr) auto;
        gap: .55rem;
        min-width: 0;
    }
    .parcel-input,
    .parcel-select,
    .parcel-textarea {
        width: 100%;
        border: 1px solid var(--line);
        border-radius: 9px;
        padding: .74rem .82rem;
        color: #17251b;
        font: inherit;
        background: #fff;
        transition: border-color .18s, box-shadow .18s;
    }
    .parcel-input:focus,
    .parcel-select:focus,
    .parcel-textarea:focus {
        outline: 0;
        border-color: var(--accent);
        box-shadow: 0 0 0 3px rgba(76,199,106,.12);
    }
    .parcel-textarea {
        resize: vertical;
        min-height: 90px;
    }
    .parcel-btn,
    .parcel-icon-btn {
        display: inline-flex;
        align-items: center;
        justify-content: center;
        gap: .45rem;
        border: 0;
        border-radius: 9px;
        background: var(--accent);
        color: #fff;
        font-weight: 800;
        cursor: pointer;
        text-decoration: none;
        white-space: nowrap;
        transition: transform .16s, box-shadow .16s, opacity .16s;
    }
    .parcel-btn {
        min-height: 42px;
        padding: .72rem .95rem;
    }
    .parcel-icon-btn {
        width: 42px;
        height: 42px;
        border: 1px solid var(--line);
        background: #fff;
        color: var(--primary);
    }
    .parcel-btn:hover,
    .parcel-icon-btn:hover {
        transform: translateY(-1px);
        box-shadow: 0 8px 18px rgba(26,92,42,.12);
    }
    .parcel-btn.secondary {
        background: var(--soft);
        color: var(--primary);
        border: 1px solid var(--line);
    }
    .parcel-btn.danger {
        background: #fff;
        color: #b91c1c;
        border: 1px solid rgba(185,28,28,.18);
    }
    .parcel-btn:disabled {
        opacity: .55;
        cursor: not-allowed;
        transform: none;
        box-shadow: none;
    }
    .parcel-tool-strip {
        display: flex;
        gap: .45rem;
        align-items: center;
        justify-content: flex-end;
    }
    .parcel-map-shell {
        position: relative;
        background: #dbe7de;
    }
    #accountParcelMap {
        height: min(78vh, 780px);
        min-height: 640px;
        width: 100%;
        background: #dbe7de;
    }
    .parcel-map-status {
        position: absolute;
        left: 1rem;
        bottom: 1rem;
        z-index: 500;
        max-width: min(460px, calc(100% - 2rem));
        display: inline-flex;
        align-items: center;
        gap: .5rem;
        border: 1px solid rgba(26,92,42,.14);
        border-radius: 999px;
        background: rgba(255,255,255,.94);
        box-shadow: 0 12px 30px rgba(7,26,13,.12);
        color: #374151;
        font-size: .78rem;
        font-weight: 700;
        line-height: 1.35;
        padding: .52rem .78rem;
        backdrop-filter: blur(10px);
    }
    .parcel-map-status i { color: var(--accent); }
    .parcel-results {
        display: none;
        grid-template-columns: repeat(2, minmax(0, 1fr));
        gap: .55rem;
        max-height: 260px;
        overflow: auto;
        padding: .75rem 1rem 1rem;
        border-top: 1px solid var(--line);
        background: #fbfefc;
    }
    .parcel-results.has-results { display: grid; }
    .parcel-result {
        width: 100%;
        text-align: left;
        border: 1px solid var(--line);
        background: #fff;
        border-radius: 10px;
        padding: .72rem .78rem;
        cursor: pointer;
        min-height: 82px;
    }
    .parcel-result:hover {
        border-color: rgba(45,158,75,.45);
        background: var(--soft);
    }
    .parcel-result strong {
        display: block;
        font-size: .84rem;
        color: #17251b;
        margin-bottom: .18rem;
        line-height: 1.3;
    }
    .parcel-result span {
        display: -webkit-box;
        -webkit-line-clamp: 2;
        -webkit-box-orient: vertical;
        overflow: hidden;
        font-size: .74rem;
        color: var(--muted);
        line-height: 1.45;
    }
    .parcel-side-stack {
        display: flex;
        flex-direction: column;
        gap: 1rem;
    }
    .parcel-form-panel { padding: 1rem; }
    .parcel-form-head {
        display: flex;
        justify-content: space-between;
        align-items: flex-start;
        gap: .75rem;
        margin-bottom: .95rem;
    }
    .parcel-kicker {
        font-size: .66rem;
        font-weight: 800;
        text-transform: uppercase;
        letter-spacing: .5px;
        color: var(--accent);
        margin-bottom: .2rem;
    }
    .parcel-form-title {
        font-family: 'Poppins', sans-serif;
        font-weight: 800;
        color: var(--primary);
        line-height: 1.2;
    }
    .parcel-selected {
        background: linear-gradient(180deg, #f7fcf8, #fff);
        border: 1px solid var(--line);
        border-radius: 12px;
        padding: .9rem;
        margin-bottom: .95rem;
        color: #374151;
        font-size: .82rem;
        line-height: 1.55;
    }
    .parcel-selected strong {
        color: var(--primary);
        display: block;
        font-family: 'Poppins', sans-serif;
        margin-bottom: .3rem;
    }
    .parcel-chip-row {
        display: flex;
        flex-wrap: wrap;
        gap: .4rem;
        margin-top: .6rem;
    }
    .parcel-chip {
        display: inline-flex;
        align-items: center;
        gap: .3rem;
        border-radius: 999px;
        background: #fff;
        border: 1px solid var(--line);
        color: var(--primary);
        font-size: .7rem;
        font-weight: 800;
        padding: .26rem .55rem;
    }
    .parcel-field { margin-bottom: .82rem; }
    .parcel-field label {
        display: block;
        font-size: .7rem;
        font-weight: 800;
        color: var(--primary);
        text-transform: uppercase;
        letter-spacing: .45px;
        margin-bottom: .32rem;
    }
    .parcel-actions {
        display: grid;
        grid-template-columns: minmax(0, 1fr) auto;
        gap: .55rem;
        align-items: center;
        margin-top: .95rem;
    }
    .parcel-tip-panel {
        padding: .95rem;
        background: #fbfefc;
    }
    .parcel-tip-title {
        display: flex;
        align-items: center;
        gap: .45rem;
        font-family: 'Poppins', sans-serif;
        font-weight: 800;
        color: var(--primary);
        font-size: .88rem;
        margin-bottom: .65rem;
    }
    .parcel-step-list {
        display: grid;
        gap: .52rem;
        color: #374151;
        font-size: .8rem;
        line-height: 1.5;
    }
    .parcel-step {
        display: grid;
        grid-template-columns: 24px minmax(0, 1fr);
        gap: .55rem;
        align-items: start;
    }
    .parcel-step-num {
        width: 24px;
        height: 24px;
        border-radius: 50%;
        display: grid;
        place-items: center;
        background: var(--soft);
        color: var(--primary);
        font-size: .7rem;
        font-weight: 900;
    }
    .parcel-error {
        border: 1px solid rgba(185,28,28,.2);
        background: #fef2f2;
        color: #991b1b;
        border-radius: 12px;
        padding: .85rem;
        margin-bottom: 1rem;
        font-size: .84rem;
    }
    .leaflet-container {
        font-family: 'Inter', sans-serif;
    }
    .leaflet-popup-content-wrapper {
        border-radius: 10px;
    }
    @media (max-width: 1180px) {
        .parcel-workspace { grid-template-columns: 1fr; }
        #accountParcelMap {
            height: 70vh;
            min-height: 580px;
        }
    }
    @media (max-width: 720px) {
        .parcel-toolbar,
        .parcel-search-box,
        .parcel-actions,
        .parcel-results {
            grid-template-columns: 1fr;
        }
        .parcel-tool-strip {
            justify-content: stretch;
        }
        .parcel-icon-btn,
        .parcel-tool-strip .parcel-btn {
            flex: 1;
        }
        #accountParcelMap {
            height: 62vh;
            min-height: 500px;
        }
    }
</style>

<div class="panel">
    <div class="panel-head">
        <div class="panel-title"><i class="bi bi-map-fill" style="color:var(--accent);"></i> Add Property From Map</div>
        <a class="panel-action" href="{{ route('dashboard.olives-account.properties') }}">Back to properties</a>
    </div>

    @if ($errors->any())
        <div class="parcel-error">
            {{ $errors->first() }}
        </div>
    @endif

    <div class="parcel-workspace">
        <div class="parcel-map-panel">
            <div class="parcel-toolbar">
                <div class="parcel-search-box">
                    <input class="parcel-input" id="parcelSearch" type="search" placeholder="Search town, street, landmark, digital address, or parcel">
                    <button class="parcel-btn" id="parcelSearchBtn" type="button"><i class="bi bi-search"></i> Search</button>
                </div>
                <div class="parcel-tool-strip">
                    <button class="parcel-icon-btn" id="parcelLocateBtn" type="button" title="Use my current location"><i class="bi bi-crosshair"></i></button>
                    <button class="parcel-icon-btn" id="parcelGhanaBtn" type="button" title="Reset map to Ghana"><i class="bi bi-globe-africa"></i></button>
                    <button class="parcel-icon-btn" id="parcelClearBtn" type="button" title="Clear current selection"><i class="bi bi-x-lg"></i></button>
                </div>
            </div>

            <div class="parcel-map-shell">
                <div id="accountParcelMap"></div>
                <div class="parcel-map-status" id="parcelMapStatus">
                    <i class="bi bi-info-circle-fill"></i>
                    <span>Search for a place, choose a footprint, or click the map to drop a pin.</span>
                </div>
            </div>

            <div class="parcel-results" id="parcelResults"></div>
        </div>

        <div class="parcel-side-stack">
            <form class="parcel-form-panel" method="POST" action="{{ route('dashboard.olives-account.properties.map.store') }}" id="parcelRequestForm">
                @csrf
                <div class="parcel-form-head">
                    <div>
                        <div class="parcel-kicker">Selected property</div>
                        <div class="parcel-form-title">Review map selection</div>
                    </div>
                    <span class="parcel-chip" id="selectionModeChip"><i class="bi bi-cursor"></i> Waiting</span>
                </div>

                <div class="parcel-selected" id="selectedParcelSummary">
                    <strong>No parcel selected</strong>
                    Select a mapped footprint for the best boundary data, or click the map to submit a point location.
                </div>

                <div class="parcel-field">
                    <label for="parcel_name">Property label</label>
                    <input class="parcel-input" id="parcel_name" name="parcel_name" value="{{ old('parcel_name') }}" placeholder="House, shop, site, or land name">
                </div>

                <div class="parcel-field">
                    <label for="assembly_code">Municipality or district</label>
                    <select class="parcel-select" id="assembly_code" name="assembly_code">
                        <option value="">Select municipality or district</option>
                        @foreach ($assemblies as $assembly)
                            <option value="{{ $assembly->assembly_code }}" @selected(old('assembly_code') === $assembly->assembly_code)>
                                {{ $assembly->name }}
                            </option>
                        @endforeach
                    </select>
                </div>

                <div class="parcel-field">
                    <label for="address_line">Address</label>
                    <input class="parcel-input" id="address_line" name="address_line" value="{{ old('address_line') }}" placeholder="Digital address, street, town, or landmark">
                </div>

                <div class="parcel-field">
                    <label for="continent">Continent</label>
                    <input class="parcel-input" id="continent" name="continent" value="{{ old('continent') }}" placeholder="Africa, Europe, Asia, North America...">
                </div>

                <div class="parcel-field">
                    <label for="country">Country</label>
                    <input class="parcel-input" id="country" name="country" value="{{ old('country') }}" placeholder="Country">
                </div>

                <div class="parcel-field">
                    <label for="country_code">Country code</label>
                    <input class="parcel-input" id="country_code" name="country_code" value="{{ old('country_code') }}" placeholder="GH, NG, US, GB">
                </div>

                <div class="parcel-field">
                    <label for="administrative_area">State / Region / Province</label>
                    <input class="parcel-input" id="administrative_area" name="administrative_area" value="{{ old('administrative_area') }}" placeholder="Region, state, or province">
                </div>

                <div class="parcel-field">
                    <label for="city">City / Town</label>
                    <input class="parcel-input" id="city" name="city" value="{{ old('city') }}" placeholder="City, town, or locality">
                </div>

                <div class="parcel-field">
                    <label for="district">District / County</label>
                    <input class="parcel-input" id="district" name="district" value="{{ old('district') }}" placeholder="District, county, borough, or municipality">
                </div>

                <div class="parcel-field">
                    <label for="postal_code">Postal code</label>
                    <input class="parcel-input" id="postal_code" name="postal_code" value="{{ old('postal_code') }}" placeholder="Postal or ZIP code">
                </div>

                <div class="parcel-field">
                    <label for="notes">Notes</label>
                    <textarea class="parcel-textarea" id="notes" name="notes" placeholder="Ownership, building description, plot details, or local reference">{{ old('notes') }}</textarea>
                </div>

                <input type="hidden" id="display_name" name="display_name" value="{{ old('display_name') }}">
                <input type="hidden" id="latitude" name="latitude" value="{{ old('latitude') }}">
                <input type="hidden" id="longitude" name="longitude" value="{{ old('longitude') }}">
                <input type="hidden" id="osm_type" name="osm_type" value="{{ old('osm_type') }}">
                <input type="hidden" id="osm_id" name="osm_id" value="{{ old('osm_id') }}">
                <input type="hidden" id="osm_place_id" name="osm_place_id" value="{{ old('osm_place_id') }}">
                <input type="hidden" id="osm_class" name="osm_class" value="{{ old('osm_class') }}">
                <input type="hidden" id="osm_category" name="osm_category" value="{{ old('osm_category') }}">
                <input type="hidden" id="parcel_geojson" name="parcel_geojson" value="{{ old('parcel_geojson') }}">

                <div class="parcel-actions">
                    <button class="parcel-btn" type="submit" id="parcelSubmitBtn" disabled>
                        <i class="bi bi-send-check"></i> Submit property
                    </button>
                    <button class="parcel-btn danger" type="button" id="parcelFormClearBtn">Clear</button>
                </div>
            </form>

            <div class="parcel-tip-panel">
                <div class="parcel-tip-title"><i class="bi bi-lightbulb-fill" style="color:var(--accent);"></i> Selection flow</div>
                <div class="parcel-step-list">
                    <div class="parcel-step">
                        <div class="parcel-step-num">1</div>
                        <div>Search for the nearest town, street, landmark, or digital address.</div>
                    </div>
                    <div class="parcel-step">
                        <div class="parcel-step-num">2</div>
                        <div>Zoom in and choose a highlighted building or land footprint when one appears.</div>
                    </div>
                    <div class="parcel-step">
                        <div class="parcel-step-num">3</div>
                        <div>Use a map click when a footprint is not available, then add notes for validation.</div>
                    </div>
                </div>
            </div>
        </div>
    </div>
</div>

<script>
document.addEventListener('DOMContentLoaded', function () {
    const GHANA_CENTER = [7.9465, -1.0232];
    const map = L.map('accountParcelMap', { scrollWheelZoom: true, zoomControl: false }).setView(GHANA_CENTER, 7);
    L.control.zoom({ position: 'topright' }).addTo(map);

    const selectedSummary = document.getElementById('selectedParcelSummary');
    const selectionModeChip = document.getElementById('selectionModeChip');
    const submitBtn = document.getElementById('parcelSubmitBtn');
    const resultsEl = document.getElementById('parcelResults');
    const statusEl = document.getElementById('parcelMapStatus');
    const searchInput = document.getElementById('parcelSearch');

    const footprintLayer = L.geoJSON(null, {
        style: () => ({ color: '#2d9e4b', weight: 2, fillColor: '#4cc76a', fillOpacity: .18 }),
        onEachFeature: (feature, layer) => {
            layer.on('click', () => selectGeoJsonFeature(feature, layer));
            layer.bindTooltip(featureName(feature), { sticky: true });
        },
    }).addTo(map);

    const selectedLayer = L.geoJSON(null, {
        style: () => ({ color: '#b91c1c', weight: 3, fillColor: '#ef4444', fillOpacity: .18 }),
    }).addTo(map);

    let marker = null;
    let footprintRequestController = null;

    L.tileLayer('https://{s}.tile.openstreetmap.org/{z}/{x}/{y}.png', {
        maxZoom: 19,
        attribution: '&copy; OpenStreetMap contributors',
    }).addTo(map);

    setTimeout(() => map.invalidateSize(), 120);

    const oldLat = document.getElementById('latitude').value;
    const oldLng = document.getElementById('longitude').value;
    if (oldLat && oldLng) {
        setPointSelection({
            lat: Number(oldLat),
            lon: Number(oldLng),
            display_name: document.getElementById('display_name').value || 'Selected property',
            name: document.getElementById('parcel_name').value || 'Selected property',
            geojson: parseJson(document.getElementById('parcel_geojson').value),
        }, true, 'Restored');
    }

    document.getElementById('parcelSearchBtn').addEventListener('click', runSearch);
    searchInput.addEventListener('keydown', function (event) {
        if (event.key === 'Enter') {
            event.preventDefault();
            runSearch();
        }
    });
    document.getElementById('parcelLocateBtn').addEventListener('click', locateUser);
    document.getElementById('parcelGhanaBtn').addEventListener('click', () => {
        map.setView(GHANA_CENTER, 7);
        setStatus('Map reset to Ghana. Search or zoom into your property area.', 'bi-globe-africa');
    });
    document.getElementById('parcelClearBtn').addEventListener('click', clearSelection);
    document.getElementById('parcelFormClearBtn').addEventListener('click', clearSelection);

    map.on('click', event => reverseSelect(event.latlng.lat, event.latlng.lng));
    map.on('moveend', () => {
        const center = map.getCenter();
        if (map.getZoom() >= 16) {
            loadNearbyFootprints(center.lat, center.lng);
        }
    });

    function runSearch() {
        const query = searchInput.value.trim();
        if (!query) {
            searchInput.focus();
            setStatus('Type a place, street, landmark, or digital address first.', 'bi-keyboard');
            return;
        }

        setStatus('Searching OpenStreetMap...', 'bi-search');
        resultsEl.classList.add('has-results');
        resultsEl.innerHTML = '<div class="parcel-selected"><strong>Searching</strong>Looking for matching places and map features.</div>';

        fetch(`https://nominatim.openstreetmap.org/search?format=jsonv2&polygon_geojson=1&addressdetails=1&limit=8&q=${encodeURIComponent(query)}`)
            .then(response => response.json())
            .then(results => renderSearchResults(results))
            .catch(() => {
                resultsEl.innerHTML = '<div class="parcel-selected"><strong>Search unavailable</strong>OpenStreetMap search could not be reached. You can still click the map to select coordinates.</div>';
                setStatus('Search service is unavailable right now.', 'bi-exclamation-triangle-fill');
            });
    }

    function renderSearchResults(results) {
        resultsEl.innerHTML = '';

        if (!Array.isArray(results) || results.length === 0) {
            resultsEl.classList.add('has-results');
            resultsEl.innerHTML = '<div class="parcel-selected"><strong>No matches found</strong>Try a nearby landmark, town name, or click the map directly.</div>';
            setStatus('No matching places found. Try a broader search.', 'bi-info-circle-fill');
            return;
        }

        resultsEl.classList.add('has-results');
        setStatus(`${results.length} place matches found. Choose one to zoom in.`, 'bi-list-check');

        results.forEach(result => {
            const button = document.createElement('button');
            button.type = 'button';
            button.className = 'parcel-result';
            button.innerHTML = `<strong>${escapeHtml(result.name || firstPart(result.display_name))}</strong><span>${escapeHtml(result.display_name || '')}</span>`;
            button.addEventListener('click', () => {
                setPointSelection(result, true, 'Search result');
                loadNearbyFootprints(Number(result.lat), Number(result.lon));
            });
            resultsEl.appendChild(button);
        });
    }

    function locateUser() {
        if (!navigator.geolocation) {
            setStatus('Your browser does not support location lookup.', 'bi-exclamation-triangle-fill');
            return;
        }

        setStatus('Requesting your current location...', 'bi-crosshair');
        navigator.geolocation.getCurrentPosition(position => {
            const lat = position.coords.latitude;
            const lon = position.coords.longitude;
            reverseSelect(lat, lon, 'Current location');
            map.setView([lat, lon], 17);
            loadNearbyFootprints(lat, lon);
        }, () => {
            setStatus('Location permission was denied or unavailable.', 'bi-shield-exclamation');
        }, { enableHighAccuracy: true, timeout: 12000 });
    }

    function reverseSelect(lat, lon, label = 'Map pin') {
        setStatus('Reading the selected map point...', 'bi-geo-alt-fill');
        fetch(`https://nominatim.openstreetmap.org/reverse?format=jsonv2&addressdetails=1&lat=${lat}&lon=${lon}`)
            .then(response => response.json())
            .then(result => setPointSelection({
                ...result,
                lat,
                lon,
                name: result.name || firstPart(result.display_name) || 'Selected coordinates',
            }, true, label))
            .catch(() => setPointSelection({
                lat,
                lon,
                name: 'Selected coordinates',
                display_name: `${lat.toFixed(6)}, ${lon.toFixed(6)}`,
            }, true, label));
    }

    function loadNearbyFootprints(lat, lon) {
        if (footprintRequestController) {
            footprintRequestController.abort();
        }
        footprintRequestController = new AbortController();

        setStatus('Loading nearby building and parcel footprints...', 'bi-bounding-box-circles');
        const query = `
            [out:json][timeout:20];
            (
                way(around:320,${lat},${lon})["building"];
                relation(around:320,${lat},${lon})["building"];
                way(around:320,${lat},${lon})["landuse"];
                relation(around:320,${lat},${lon})["landuse"];
                way(around:320,${lat},${lon})["boundary"="parcel"];
                relation(around:320,${lat},${lon})["boundary"="parcel"];
            );
            out body geom;
        `;

        fetch(`https://overpass-api.de/api/interpreter?data=${encodeURIComponent(query)}`, {
            signal: footprintRequestController.signal,
        })
            .then(response => response.json())
            .then(data => {
                footprintLayer.clearLayers();
                if (typeof osmtogeojson === 'function') {
                    footprintLayer.addData(osmtogeojson(data));
                }

                const count = Object.keys(footprintLayer._layers || {}).length;
                setStatus(count > 0
                    ? `${count} nearby footprint${count === 1 ? '' : 's'} highlighted. Click one to select its boundary.`
                    : 'No nearby footprints found. Click the map point and add notes for validation.',
                    count > 0 ? 'bi-bounding-box' : 'bi-cursor');
            })
            .catch(error => {
                if (error.name !== 'AbortError') {
                    setStatus('Footprint lookup is unavailable. You can still submit a map pin.', 'bi-exclamation-triangle-fill');
                }
            });
    }

    function selectGeoJsonFeature(feature, layer) {
        const tags = feature.properties?.tags || {};
        const center = layer.getBounds ? layer.getBounds().getCenter() : map.getCenter();
        const name = featureName(feature);

        selectedLayer.clearLayers();
        selectedLayer.addData(feature);
        setPointSelection({
            lat: center.lat,
            lon: center.lng,
            name,
            display_name: buildFeatureAddress(tags, center),
            osm_type: feature.properties?.type,
            osm_id: feature.properties?.id,
            class: tags.building ? 'building' : (tags.landuse ? 'landuse' : 'parcel'),
            type: tags.building || tags.landuse || tags.boundary || 'parcel',
            address: {
                country: tags['addr:country'] || '',
                country_code: tags['addr:country'] || '',
                state: tags['addr:state'] || tags['addr:province'] || '',
                city: tags['addr:city'] || tags['addr:town'] || tags['addr:village'] || '',
                county: tags['addr:district'] || tags['addr:county'] || '',
                postcode: tags['addr:postcode'] || '',
            },
            geojson: feature.geometry,
        }, false, 'Footprint');
    }

    function setPointSelection(item, redraw = true, mode = 'Selected') {
        const lat = Number(item.lat);
        const lon = Number(item.lon);
        const geojson = item.geojson || null;
        const name = item.name || firstPart(item.display_name) || 'Selected property';
        const display = item.display_name || `${lat.toFixed(6)}, ${lon.toFixed(6)}`;

        if (Number.isNaN(lat) || Number.isNaN(lon)) {
            return;
        }

        if (redraw) {
            selectedLayer.clearLayers();
            if (geojson) {
                selectedLayer.addData(geojson);
            }
        }

        if (marker) {
            marker.remove();
        }
        marker = L.marker([lat, lon], { draggable: true }).addTo(map).bindPopup(escapeHtml(name));
        marker.on('dragend', event => {
            const position = event.target.getLatLng();
            reverseSelect(position.lat, position.lng, 'Dragged pin');
        });

        map.setView([lat, lon], Math.max(map.getZoom(), 17));

        document.getElementById('parcel_name').value ||= name;
        document.getElementById('display_name').value = display;
        document.getElementById('address_line').value ||= display;
        document.getElementById('latitude').value = lat.toFixed(8);
        document.getElementById('longitude').value = lon.toFixed(8);
        document.getElementById('osm_type').value = item.osm_type || '';
        document.getElementById('osm_id').value = item.osm_id || '';
        document.getElementById('osm_place_id').value = item.place_id || item.osm_place_id || '';
        document.getElementById('osm_class').value = item.class || item.osm_class || '';
        document.getElementById('osm_category').value = item.type || item.osm_category || '';
        document.getElementById('parcel_geojson').value = geojson ? JSON.stringify(geojson) : '';
        applyAddressDetails(item.address || {}, display);

        selectionModeChip.innerHTML = `<i class="bi ${geojson ? 'bi-bounding-box' : 'bi-geo-alt-fill'}"></i> ${escapeHtml(mode)}`;
        selectedSummary.innerHTML = `
            <strong>${escapeHtml(name)}</strong>
            ${escapeHtml(display)}
            <div class="parcel-chip-row">
                <span class="parcel-chip"><i class="bi bi-geo"></i> ${lat.toFixed(6)}, ${lon.toFixed(6)}</span>
                <span class="parcel-chip"><i class="bi ${geojson ? 'bi-check-circle-fill' : 'bi-pin-map-fill'}"></i> ${geojson ? 'Boundary captured' : 'Point location'}</span>
            </div>
        `;
        submitBtn.disabled = false;
        setStatus(geojson ? 'Boundary selected. Review the form and submit.' : 'Point selected. Drag the marker to refine the location.', geojson ? 'bi-check-circle-fill' : 'bi-pin-map-fill');
    }

    function clearSelection() {
        selectedLayer.clearLayers();
        footprintLayer.clearLayers();
        if (marker) {
            marker.remove();
            marker = null;
        }

        ['display_name', 'latitude', 'longitude', 'osm_type', 'osm_id', 'osm_place_id', 'osm_class', 'osm_category', 'parcel_geojson', 'continent', 'country', 'country_code', 'administrative_area', 'city', 'district', 'postal_code'].forEach(id => {
            document.getElementById(id).value = '';
        });

        submitBtn.disabled = true;
        selectionModeChip.innerHTML = '<i class="bi bi-cursor"></i> Waiting';
        selectedSummary.innerHTML = '<strong>No parcel selected</strong>Select a mapped footprint for the best boundary data, or click the map to submit a point location.';
        setStatus('Selection cleared. Search, locate, or click the map again.', 'bi-info-circle-fill');
    }

    function setStatus(message, icon = 'bi-info-circle-fill') {
        statusEl.innerHTML = `<i class="bi ${icon}"></i><span>${escapeHtml(message)}</span>`;
    }

    function featureName(feature) {
        const tags = feature.properties?.tags || {};
        return tags.name || tags['addr:housename'] || tags['addr:housenumber'] || tags.building || tags.landuse || 'Mapped footprint';
    }

    function buildFeatureAddress(tags, center) {
        const parts = [
            tags['addr:housenumber'],
            tags['addr:street'],
            tags['addr:suburb'],
            tags['addr:city'],
            tags['addr:district'],
        ].filter(Boolean);

        return parts.length ? parts.join(', ') : `Mapped footprint near ${center.lat.toFixed(6)}, ${center.lng.toFixed(6)}`;
    }

    function applyAddressDetails(address, fallbackAddress) {
        const countryCode = (address.country_code || address['ISO3166-1:alpha2'] || '').toUpperCase();
        const values = {
            country: address.country || '',
            country_code: countryCode,
            continent: address.continent || inferContinent(countryCode),
            administrative_area: address.state || address.region || address.province || '',
            city: address.city || address.town || address.village || address.municipality || address.hamlet || '',
            district: address.county || address.district || address.borough || address.suburb || '',
            postal_code: address.postcode || '',
            address_line: fallbackAddress || '',
        };

        Object.entries(values).forEach(([id, value]) => {
            const input = document.getElementById(id);
            if (input && !input.value && value) {
                input.value = value;
            }
        });
    }

    function inferContinent(countryCode) {
        if (!countryCode) return '';
        const continents = {
            Africa: 'DZ AO BJ BW BF BI CM CV CF TD KM CG CD CI DJ EG GQ ER SZ ET GA GM GH GN GW KE LS LR LY MG MW ML MR MU MA MZ NA NE NG RW ST SN SC SL SO ZA SS SD TZ TG TN UG EH ZM ZW',
            Europe: 'AL AD AT BY BE BA BG HR CY CZ DK EE FI FR DE GR HU IS IE IT XK LV LI LT LU MT MD MC ME NL MK NO PL PT RO RU SM RS SK SI ES SE CH UA GB VA',
            Asia: 'AF AM AZ BH BD BT BN KH CN GE IN ID IR IQ IL JP JO KZ KW KG LA LB MY MV MN MM NP KP OM PK PS PH QA SA SG KR LK SY TW TJ TH TL TR TM AE UZ VN YE',
            'North America': 'AG BS BB BZ CA CR CU DM DO SV GD GT HT HN JM MX NI PA KN LC VC TT US',
            'South America': 'AR BO BR CL CO EC GY PY PE SR UY VE GF',
            Oceania: 'AU FJ KI MH FM NR NZ PW PG WS SB TO TV VU',
            Antarctica: 'AQ BV TF HM GS',
        };

        return Object.entries(continents).find(([, codes]) => codes.split(' ').includes(countryCode))?.[0] || '';
    }

    function firstPart(value) {
        return (value || '').split(',')[0].trim();
    }

    function parseJson(value) {
        try {
            return value ? JSON.parse(value) : null;
        } catch (error) {
            return null;
        }
    }

    function escapeHtml(value) {
        return String(value || '').replace(/[&<>"']/g, char => ({
            '&': '&amp;',
            '<': '&lt;',
            '>': '&gt;',
            '"': '&quot;',
            "'": '&#039;',
        }[char]));
    }
});
</script>

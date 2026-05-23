<!DOCTYPE html>
<html lang="en">

<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <title>Property On Map</title>
    <!-- Bootstrap Css -->
    <link id="style" href="{{ asset('assets/libs/bootstrap/css/bootstrap.min.css') }}" rel="stylesheet">
    <!-- Style Css -->
    <link href="{{ asset('assets/css/styles.css?t=' . time()) }}" rel="stylesheet">
    <link href="{{ asset('assets/app/map/leaflet/leaflet.css') }}" rel="stylesheet">
    <link href="{{ asset('assets/app/map/leaflet/OverPassLayer.css') }}" rel="stylesheet">
    <link href="{{ asset('assets/app/map/leaflet/leaflet-geoman.css') }}" rel="stylesheet">
    <link href="{{ asset('assets/app/map/leaflet/L.Control.SlideMenu.css') }}" rel="stylesheet">
    <link href="{{ asset('assets/app/map/leaflet/leaflet-geojson-selector.css') }}" rel="stylesheet">
    <link href="{{ asset('assets/app/map/leaflet/search/leaflet-search.css') }}" rel="stylesheet">
    <link href="{{ asset('assets/css/mapsidebar.css') }}" rel="stylesheet">

    <!-- Choices JS -->
    <script src="{{ asset('assets/libs/choices.js/public/assets/scripts/choices.min.js') }}"></script>
    <!-- Choices Css -->
    <link rel="stylesheet" href="{{ asset('assets/libs/choices.js/public/assets/styles/choices.min.css') }}">
</head>

<body>
    <div id="mapwrap"
        style="position:fixed; width:100%; height:100%; top:0px;left:0px;z-index:1000;margin:0;padding:0;"
        data-allocations-url="{{ url('new-property/allocations') }}">

        <div id="toolbar">
            <div class="hamburger">
                <span>Actions</span>
            </div>
            <div id="tourstop">
                <div class="row">
                    <div class="col-sm-12">
                        <div class="card">
                            <div class="card-body">
                                <form name="jobForm" id="job-form" data-action="#">
                                    @csrf

                                    <input type="hidden" name="polygons_url" url="{{ route('properties.polygons') }}">

                                    <div class="row">
                                        <div class="col-md-12 col-lg-12 col-xl-12 mb-3">
                                            <label for="typeId">Job Type</label>
                                            <select class="form-control @error('jobId') is-invalid @enderror"
                                                name="jobId[]" id="choices-multiple-default" multiple>
                                                <option value="">Select Job Type</option>
                                                <option value="Payment Collection">Payment Collection</option>
                                                <option value="Bill Distribution">Bill Distribution</option>
                                                <option value="Data Collection">Data Collection</option>
                                            </select>

                                            @error('jobId')
                                                <span class="invalid-feedback" role="alert">
                                                    {{ $message }}
                                                </span>
                                            @enderror
                                        </div>

                                        <div class="col-md-12 col-lg-12 col-xl-12 mb-3">
                                            <label for="typeId">Agent</label>
                                            <select class="form-control @error('agentId') is-invalid @enderror"
                                                id="agentId" name="agentId">
                                                <option value="">Select Agent</option>
                                                @foreach ($agents as $agent)
                                                    <option value="{{ $agent->id }}">{{ $agent->name }}</option>
                                                @endforeach
                                            </select>

                                            @error('agentId')
                                                <span class="invalid-feedback" role="alert">
                                                    {{ $message }}
                                                </span>
                                            @enderror
                                        </div>
                                    </div>
                                </form>

                                <label>Blocks Selected: <span id="sl-blocks" class="text-bold">0</span></label>

                                <div class="row" style="margin-top:10px">
                                    <div class="col-lg-12">
                                        <button type="submit" class="btn btn-primary" id="btn-save-action"
                                            style="width: 100%;"><i class="icofont icofont-save"></i> Allocate
                                        </button>
                                    </div>
                                </div>
                            </div>
                        </div>
                    </div>
                </div>

                <div class="row">
                    {{-- <div class="col-sm-12" id="block-search-control"></div> --}}
                </div>

                <div class="row">
                    <div class="col-sm-12">
                        <div class="card">
                            <div class="card-body">
                                <div>
                                    <h5>Block Allocation Information</h5>
                                </div>
                                <div class="body" style="padding:0;overflow-y:scroll;height:400px;">
                                    <table id="state-table"
                                        class="table table-bordered table-striped table-hover basic-example dataTable">
                                        <thead>
                                            <tr>
                                                <th>Job Name</th>
                                                <th>Block</th>
                                                <th>Assigned To</th>
                                            </tr>
                                        </thead>
                                        <tbody id="job-body">

                                        </tbody>
                                    </table>
                                </div>
                            </div>
                        </div>
                    </div>
                </div>
            </div>
        </div>
        <div class="map-header-layer" id="map" style="width:100%; height:100%;"></div>
    </div>


    <script src="https://code.jquery.com/jquery-3.6.1.min.js"
        integrity="sha256-o88AwQnZB+VDvE9tvIXrMQaPlFFSUTR+nldQm1LuPXQ=" crossorigin="anonymous"></script>
    <!-- Bootstrap JS -->
    <script src="{{ asset('assets/libs/bootstrap/js/bootstrap.bundle.min.js') }}"></script>
    <script src="{{ asset('assets/app/map/geojsonhint.js') }}"></script>
    <script src="{{ asset('assets/app/map/geojsonUtil.js') }}"></script>
    <script src="{{ asset('assets/app/map/leaflet/leaflet.js') }}"></script>
    <script src="{{ asset('assets/app/map/leaflet/OverPassLayer.bundle.js') }}"></script>
    <script src="{{ asset('assets/app/map/leaflet/Leaflet.Control.Custom.js') }}"></script>
    <script src="{{ asset('assets/app/map/leaflet/leaflet-geoman.min.js') }}"></script>
    <script src="{{ asset('assets/app/map/leaflet/L.Control.SlideMenu.js') }}"></script>
    <script src="{{ asset('assets/app/map/leaflet/easy-button.js') }}"></script>
    <script src="{{ asset('assets/app/map/leaflet/osmtogeojson.js') }}"></script>
    <script src="{{ asset('assets/app/map/leaflet/Leaflet.VectorGrid.bundled.js') }}"></script>
    <script src="{{ asset('assets/app/map/leaflet/leaflet-geojson-selector.min.js') }}"></script>
    <script src="{{ asset('assets/app/map/leaflet/search/leaflet-search.js') }}"></script>

    <script src="{{ asset('assets/app/map/StyleFactory.js') }}"></script>
    <script src="{{ asset('assets/app/map/MapTileProvider.js') }}"></script>
    <script src="{{ asset('assets/app/map/MapController.js?v=' . \Illuminate\Support\Str::random(5)) }}"></script>
    <script src="{{ asset('assets/js/Common.js?v1=' . time()) }}"></script>
    <script src="{{ asset('assets/js/map/allocate.js?v1=' . time()) }}"></script>
    <!-- Internal Choices JS -->
    <script src="{{ asset('assets/js/choices.js') }}"></script>

</body>

</html>

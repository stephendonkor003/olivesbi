$(function () {


    var mapContainer = $("#map");
    var playback = null;
    var playbackControl = null;
    var isMap = false;
    var map = null;
    //playBackDefaults();
    //loadTracks(map);
    // Colors for AwesomeMarkers
    var _colorIdx = 0,
        _colors = [
            'orange',
            'green',
            'blue',
            'purple',
            'darkred',
            'cadetblue',
            'red',
            'darkgreen',
            'darkblue',
            'darkpurple'
        ];

    init();
    function loadMap() {

        map = L.map('map', {
            preferCanvas: true,
            center: [7.95277, -1.03071],
            zoom: 12,
            minZoom: 12,
            maxZoom: 22
        });
        var basemapLayer = L.tileLayer('https://{s}.tile.osm.org/{z}/{x}/{y}.png', {
            attribution: '&copy; <a href="https://osm.org/copyright">OpenStreetMap</a> contributors'
        }).addTo(map);

        // Center map and default zoom level
        //  map.setView([44.61131534, -123.4726739], 9);
        // Adds the background layer to the map
        map.addLayer(basemapLayer);
        L.control.layers(MapController.getBaseMaps()).addTo(map);
    }

    // Setup leaflet map


    function resetMap() {
        map.eachLayer(function (layer) {
            map.removeLayer(layer);
            //reload the map function
        });
        loadMap();
    }

    function _assignColor() {
        return _colors[_colorIdx++ % 10];
    }

    function bboxLeaflet(geojson) {
        var bb = turf.bbox(geojson);
        return [[bb[1], bb[0]], [bb[3], bb[2]]];
    }


    function loadTracks(map) {
        var trackForm = $("#job-form");
        // var payLoad = {
        //     agentId: '55f816a3-3ef5-4569-82c6-e3dab5b1ec77',
        //     startDate: '2021-05-01',
        //     endDate: '2021-06-30',
        //     _token: mapContainer.attr('data-token')
        // };
        var requestUrl = mapContainer.attr('data-url');
        $.ajax({
            type: "POST",
            url: requestUrl,
            accept: 'application/json',
            data: trackForm.serialize(),//JSON.stringify(payLoad),
            // contentType: "application/json; charset=utf-8",
            dataType: "json",
            beforeSend: function (xhr) {
                Common.showSpinner(mapContainer, "Loading Tracks...");
                return true;
            }
        }).done(function (response) {
            Common.hideSpinner(mapContainer);
            if (response.code === "00") {
                console.log(response);
                // alert('Done');
                Common.hideSpinner(mapContainer);
                setupGeojson(response.data);
                // IMap.setupGeojson(map, response.data);
            }
        }).always(function (xhr) {
            Common.hideSpinner(mapContainer);
        }).fail(function (xhr) {
            Common.hideSpinner(mapContainer);
        });
    }

    function setupGeojson(data) {
        try {
            var coordinates = [];
            var timeStamps = [];
            var tracks = data.hasOwnProperty('tracks') ? data.tracks : {};
            var coords = tracks.hasOwnProperty('coordinates') ? tracks.coordinates : [];
            var times = tracks.hasOwnProperty('timestamps') ? tracks.timestamps : [];
            $.each(coords, function (index, cord) {
                coordinates.push([cord.long, cord.lat]);
                console.log(times[index]);
                var unixTimestamp = new Date(times[index]).getTime();//moment(times[index]).unix();//parseInt((new Date(times[index]).getTime() / 1000).toFixed(0));
                timeStamps.push(unixTimestamp);
            });
            console.log(timeStamps);
            if (coordinates.length > 0) {
                var geojson = {
                    "type": "Feature",
                    "geometry": {
                        "type": "MultiPoint",
                        "coordinates": coordinates
                    },
                    "properties": {
                        "title": "Tracking",
                        "path_options": {"color": "red"},
                        "time": timeStamps
                    }
                };
                map.fitBounds(bboxLeaflet(geojson));
                initPlayBack(geojson);
            }
        } catch (e) {
            console.log(e);
        }
    }

    function initDatePickers() {
        $.datetimepicker.setLocale('en');
        var $from = moment(new Date()).format("YYYY-MM-DD");
        var $to = moment(new Date()).format("YYYY-MM-DD");
        // $from += " 00:00";
        //$to += " 23:59";
        //var options={formatTime: 'H:i', formatDate: 'Y-m-d'};
        var options = {format: 'Y-m-d', timepicker: false, theme: 'classic'};
        $("#startDate").val($from);
        $("#endDate").val($to);
        $("#startDate").datetimepicker(options);
        $("#endDate").datetimepicker(options);
    }

    function init() {
        loadMap();
        try {
            $('#toolbar .hamburger').on('click', function () {
                $(this).parent().toggleClass('open');
            });
            $(document).on('click', "#btn-load-action", function (e) {
                e.preventDefault();
                e.stopImmediatePropagation();
                loadTracks(map);
            });
            $(document).on('change', "#agentId", function (e) {
               window.location.reload();
                $('#toolbar .hamburger').trigger('click');
            });
            initDatePickers();
        } catch (e) {
            console.log(e);
        }
    }

    // =====================================================
    // =============== Playback ============================
    // =====================================================
    function initPlayBack(tracks) {

        // map.removeControl(playback);
        //  map.removeControl(playbackControl);
        try {

            // Playback options
            var playbackOptions = {
                // layer and marker options
                layer: {
                    pointToLayer: function (featureData, latlng) {
                        var result = {};

                        if (featureData && featureData.properties && featureData.properties.path_options) {
                            result = featureData.properties.path_options;
                        }

                        if (!result.radius) {
                            result.radius = 5;
                        }

                        return new L.CircleMarker(latlng, result);
                    }
                },

                marker: function () {
                    return {
                        icon: L.AwesomeMarkers.icon({
                            prefix: 'fa',
                            icon: 'bullseye',
                            markerColor: _assignColor()
                        })
                    };
                }
            };
                playback = new L.Playback(map, tracks, null, playbackOptions);
                playbackControl = new L.Playback.Control(playback);
                playbackControl.addTo(map);

        } catch (e) {
            console.log(e);
        }

    }

    function playBackDefaults() {
        var geojson = {
            "type": "Feature",
            "geometry": {
                "type": "MultiPoint",
                "coordinates": []
            },
            "properties": {
                "title": "Tracking",
                "path_options": {"color": "red"},
                "time": []
            }
        };
        // Playback options
        var playbackOptions = {
            // layer and marker options
            layer: {
                pointToLayer: function (featureData, latlng) {
                    var result = {};

                    if (featureData && featureData.properties && featureData.properties.path_options) {
                        result = featureData.properties.path_options;
                    }

                    if (!result.radius) {
                        result.radius = 5;
                    }

                    return new L.CircleMarker(latlng, result);
                }
            },

            marker: function () {
                return {
                    icon: L.AwesomeMarkers.icon({
                        prefix: 'fa',
                        icon: 'bullseye',
                        markerColor: _assignColor()
                    })
                };
            }
        };
        playback = new L.Playback(map, geojson, null, playbackOptions);
        playbackControl = new L.Playback.Control(playback);
        playbackControl.addTo(map);
    }

    window.onbeforeunload = function () {
        localStorage.setItem("name", $('#agentId').val());
        localStorage.setItem("startDate", $('#startDate').val());
        localStorage.setItem("endDate", $('#endDate').val());
    }

    window.onload = function () {
        var name = localStorage.getItem("name");
        var startDate = localStorage.getItem("startDate");
        var endDate = localStorage.getItem("endDate");
        if (name !== null) {
            $('#agentId').val(name);
        }
        if (startDate !== null) {
            $('#startDate').val(startDate)
        }
        if (endDate !== null) {
            $('#endDate').val(endDate)
        }
    }
});

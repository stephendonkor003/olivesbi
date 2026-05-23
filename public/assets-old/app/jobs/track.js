/**
 * Created by degre on 5/16/2021.
 */
/**
 * Created by degre on 5/16/2021.
 */
/**
 * Created by degre on 5/16/2021.
 */


var IMap = function () {
    window.blockLayer = null;
    window.blockGeoJsonLayers = [];
    window.blockIds = [];
    window.vectorGridLayer = null;
    window.vectorGridLayerGroup = L.layerGroup();
    var mapCanvas = 'map';
    var mapContainer = $("#mapwrap");
    window.playBack = null;
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

    function _assignColor() {
        return _colors[_colorIdx++ % 10];
    }

    return {
        init: function () {
            IMap.initMap();
            $("#btn-save-action").on('click', function (e) {
                e.preventDefault();
                e.stopImmediatePropagation();
            });
        },
        initMap: function () {
            const map = L.map(mapCanvas, {
                preferCanvas: true,
                center: [7.95277, -1.03071],
                zoom: 12,
                minZoom: 12,
                maxZoom: 22
            });
            L.tileLayer('https://{s}.tile.osm.org/{z}/{x}/{y}.png', {
                attribution: '&copy; <a href="https://osm.org/copyright">OpenStreetMap</a> contributors'
            }).addTo(map);
            vectorGridLayerGroup.addTo(map);
            L.control.layers(MapController.getBaseMaps()).addTo(map);
            IMap.loadTracks(map);
            $(document).on('change', '#sl-data-type', function (e) {
                var slType = $(this).val();
                //IMap.loadMapData(map, slType);
            });
            $(document).on('click', '#btn-load-action', function (e) {
                e.preventDefault();
                e.stopImmediatePropagation();
                var slType = $("#sl-data-type").val();
                IMap.loadMapData(map, slType);
                $('#toolbar .hamburger').trigger('click');
            });

            $('#toolbar .hamburger').on('click', function () {
                $(this).parent().toggleClass('open');
            });
        },
        setupGeojson: function (map, data) {
            try {
                var coordinates = [];
                var timeStamps = [];
                var tracks = data.hasOwnProperty('tracks') ? data.tracks : {};
                var coords = tracks.hasOwnProperty('coordinates') ? tracks.coordinates : [];
                var times = tracks.hasOwnProperty('timestamps') ? tracks.timestamps : [];
                $.each(coords, function (index, cord) {
                    coordinates.push([cord.long, cord.lat]);
                    console.log(times[index]);
                    var unixTimestamp = moment(times[index]).unix();
                    timeStamps.push(unixTimestamp);
                });
                console.log(timeStamps);
                var geojson = {
                    "type": "Feature",
                    "geometry": {
                        "type": "MultiPoint",
                        "coordinates": coordinates
                    },
                    "properties": {
                        "time": timeStamps
                    }
                };
               IMap.initPlayBack(map, geojson);
            } catch (e) {
                console.log(e);
            }
        },


        loadTracks: function (map) {
            var payLoad = {
                agentId: '55f816a3-3ef5-4569-82c6-e3dab5b1ec77',
                startDate: '2021-05-01',
                endDate: '2021-06-30',
                _token: mapContainer.attr('data-token')
            };
            var requestUrl = mapContainer.attr('data-url');
            $.ajax({
                type: "POST",
                url: requestUrl,
                accept: 'application/json',
                data: JSON.stringify(payLoad),
                contentType: "application/json; charset=utf-8",
                dataType: "json",
                beforeSend: function (xhr) {
                    Common.showSpinner(mapContainer, "Loading Tracks...");
                    return true;
                }
            }).done(function (response) {
                Common.hideSpinner(mapContainer);
                if (response.code === "00") {
                    console.log(response);
                    alert('Done');
                    Common.hideSpinner(mapContainer);
                     IMap.setupGeojson(map, response.data);
                }
            }).always(function (xhr) {
                Common.hideSpinner(mapContainer);
            }).fail(function (xhr) {
                Common.hideSpinner(mapContainer);
            });
        },

        initPlayBack: function (map, tracks) {
            // Playback options
            var playbackOptions = {
                // layer and marker options
                layer: {
                    pointToLayer: function (featureData, latlng) {
                        var result = {};

                        // if (featureData && featureData.properties && featureData.properties.path_options){
                        //     result = featureData.properties.path_options;
                        // }

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
            // if (playBack == null) {
            //     Initialize playback
                playBack = new L.Playback(map, tracks, null, playbackOptions);

                // Initialize custom control
                var control = new L.Playback.Control(playBack);
                control.addTo(map);
                // Add data
                //playBack.addData(tracks);
            // } else {
            //     playBack.clearData();
            //     playBack.addData(tracks);
            // }
        }


    };
}();

$(function () {
    IMap.init();
});
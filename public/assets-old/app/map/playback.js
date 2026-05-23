/**
 * Created by degre on 1/22/2018.
 */
$(function () {
    PLAYBACKMANAGER.initMap();
    PLAYBACKMANAGER.initDatePickers();
    $('#toolbar .hamburger').on('click', function () {
        $(this).parent().toggleClass('open');
    });
    $(document).on("click", "#btn-fire-playback", function (e) {
        e.preventDefault();
        e.stopImmediatePropagation();
        PLAYBACKMANAGER.getRouteList();
    });
});
var PLAYBACKMANAGER = function () {
    window.liveRouteLayer = new L.layerGroup();
    window.markerLayer = new L.featureGroup();
    window.routeSource = new L.layerGroup();
    window.geofenceLayer = new L.featureGroup();
    window.mapLayer = null;

    window.latestMarkers = {};
    window.accuracyCircles = {};
    window.liveRoutes = {};
    window.markerDirections = {};
    window.liveRouteLength = 10;// Traccar.app.getAttributePreference('web.liveRouteLength', 10);
    window.selectZoom = 0;
    window.trackingSocket = null;
    window.liveDirectionMarkers = {};
    window.playBack = null;
    window.timeLine = null;
    return {
        initDatePickers: function () {
            $("#deviceId").selectpicker();
            $.datetimepicker.setLocale('en');
            var $from = moment(new Date()).format("YYYY-MM-DD");
            var $to = $from;
            $from += " 00:00";
            $to += " 23:59";
            $("#from").val($from);
            $("#to").val($to);
            $("#from").datetimepicker({
                formatTime: 'H:i',
                formatDate: 'Y-m-d'
            });
            $("#to").datetimepicker({
                formatTime: 'H:i',
                formatDate: 'Y-m-d'
            });

        },
        initMap: function () {
            //MapController.testMultiArray();
            var type = 'googleRoad';
            var layer;
            switch (type) {
                case 'googleRoad':
                    layer = TileFactory.GoogleRoad();
                    break;
                case 'googleSatelite':
                    layer = TileFactory.GoogleSatelite();
                    break;
                case 'openStreetMap':
                    layer = TileFactory.OpenStreetMap();
                    break;
                default:
                    layer = TileFactory.GoogleRoad();
                    break;
            }
            var lat = StyleFactory.mapDefaultLat;//APPLICATION.getPreference('latitude', StyleFactory.mapDefaultLat);
            var lon = StyleFactory.mapDefaultLon;//APPLICATION.getPreference('longitude', StyleFactory.mapDefaultLon);
            var zoom = StyleFactory.mapDefaultZoom;//APPLICATION.getPreference('zoom', StyleFactory.mapDefaultZoom);
            var maxZoom = StyleFactory.mapMaxZoom; //APPLICATION.getAttributePreference('web.maxZoom', StyleFactory.mapMaxZoom);
            mapLayer = L.map('map-canvas', {
                center: [5.667789958361761, -0.011415481567382814],
                zoom: 10,
                maxZoom: maxZoom,
                minZoom: 8,
                layers: [layer, markerLayer, liveRouteLayer],
            });
            //  return baseMap;

             L.control.layers(PLAYBACKMANAGER.getBaseMaps(), PLAYBACKMANAGER.getOverlayMaps()).addTo(mapLayer);


        },
        getBaseMaps: function () {
            return {
                "Google Road": TileFactory.GoogleRoad(),
                "Google Satelite": TileFactory.GoogleSatelite(),
                "Open Street": TileFactory.OpenStreetMap(),
                "Arc Gis": TileFactory.ArcGis(),
                "Arc Gis Imagery": TileFactory.ArcGisImagery(),
                "Wikimedia": TileFactory.WikimediaMap()
            };
        },
        getOverlayMaps: function () {
            return {
                "Trucks": markerLayer,
                "Route": liveRouteLayer,
                "Geofences": geofenceLayer
            }
        },
        getPlayBackOptions: function () {
            // =====================================================
            // =============== Playback ============================
            // =====================================================
            //
            var shipIcon = L.icon({
                iconUrl: 'ship-icon.png',
                iconSize: [7, 20], // size of the icon
                shadowSize: [0, 0], // size of the shadow
                iconAnchor: [3.5, 10], // point of the icon which will correspond to marker's location
                shadowAnchor: [0, 0], // the same for the shadow
                popupAnchor: [0, -10] // point from which the popup should open relative to the iconAnchor
            });
            // Playback options
            var playbackOptions = {
                playControl: true,
                dateControl: true,
                 sliderControl: true,
                orientIcons: true,
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

                marker: {
                    getPopup: function (featureData) {
                        var result = '';

                        if (featureData && featureData.properties && featureData.properties.title) {
                            result = featureData.properties.title;
                        }

                        return result;
                    },
                    icon: PLAYBACKMANAGER.getMarkerImageIcon()


            // icon: L.AwesomeMarkers.icon({
            //     prefix: 'fa',
            //     icon: 'truck',
            //     markerColor: 'green'
            // }),
        }
            //     playControl: true,
            //     dateControl: true,
            //     sliderControl: true
            //
            // marker: function (featureData) {
            //     return {
            //         icon: shipIcon,
            //         // getPopup: function (feature) {
            //         //     return feature.properties.title;
            //         // }
            //     };
            // }
        }
            ;

            return playbackOptions;
        },
        setPlayBackTimeLine: function (startTime, endTime) {
            // Get start/end times
            //var startTime = new Date(demoTracks[0].properties.time[0]);
            //var endTime = new Date(demoTracks[0].properties.time[demoTracks[0].properties.time.length - 1]);

            // Create a DataSet with data
            var timelineData = new vis.DataSet([{start: startTime, end: endTime, content: 'Vehicle Playback'}]);

            // Set timeline options
            var timelineOptions = {
                "width": "100%",
                "height": "120px",
                "style": "box",
                "axisOnTop": true,
                "showCustomTime": true
            };

            // Setup timeline
            timeLine = new vis.Timeline(document.getElementById('timeline'), timelineData, timelineOptions);

            // Set custom time marker (blue)
            timeLine.setCustomTime(startTime);
        },
        loadPlayBack: function (devicePositions) {
            // console.log(devicePositions);
            var playBackCordinates = [];
            var playBackTimes = [];
            if (devicePositions) {
                for (i = 0; i < devicePositions.length; i++) {
                    var position = devicePositions[i];
                    playBackCordinates.push([position.longitude, position.latitude]);
                    var date = new Date(position.fixTime);
                    var timestamp = date.getTime();//moment(position.deviceTime).format("X");
                   // console.log(timestamp);
                    playBackTimes.push(timestamp);

                }
                if (playBack != null) {
                    mapLayer.remove();
                    PLAYBACKMANAGER.initMap();
                    $("#timeline").empty();
                }
                if (playBackTimes.length > 0) {
                    var startTime = playBackTimes[0];
                    var endTime = playBackTimes[playBackTimes.length - 1];
                    PLAYBACKMANAGER.setPlayBackTimeLine(startTime, endTime);
                    playBack = new L.Playback(mapLayer, PLAYBACKMANAGER.getPlayBack(playBackCordinates, playBackTimes), onPlaybackTimeChange, PLAYBACKMANAGER.getPlayBackOptions());
                    // Set timeline time change event, so cursor is set after moving custom time (blue)
                    timeLine.on('timechange', onCustomTimeChange);
                    // A callback so timeline is set after changing playback time
                    function onPlaybackTimeChange(ms) {
                        timeLine.setCustomTime(new Date(ms));
                    }

                    //
                    function onCustomTimeChange(properties) {
                        if (!playBack.isPlaying()) {
                            playBack.setCursor(properties.time.getTime());
                        }
                    }

                    PLAYBACKMANAGER.zoomToMarker();
                }
            }
        },
        zoomToMarker: function () {
            mapLayer.eachLayer(function (layer) {
                if (layer instanceof L.Marker) {
                    console.log(layer.getLatLng());
                    mapLayer.panTo(layer.getLatLng());
                }
            });
        },
        getMarkerImageIcon: function () {
            return L.icon({
                iconUrl: APPLICATION.getAssetUrl() + 'img/truck.png',
                shadowUrl: 'leaf-shadow.png',

                iconSize: [40, 64], // size of the icon
                shadowSize: [50, 64], // size of the shadow
                iconAnchor: [22, 94], // point of the icon which will correspond to marker's location
                shadowAnchor: [4, 62],  // the same for the shadow
                popupAnchor: [-3, -76] // point from which the popup should open relative to the iconAnchor
            });
        },
        getPlayBack: function (cordinates, times) {
            return {
                "type": "Feature",
                "geometry": {
                    "type": "MultiPoint",
                    "coordinates": cordinates
                },
                "properties": {
                    "time": times
                }
            };
        },

        filterDevicePostions: function (positions, id) {
            return positions.filter(
                function (position) {
                    return position.deviceId == id;
                }
            );
        },
        loadDevicePlayBack: function (playBackCordinates, playBackTimes) {
                if (playBack != null) {
                    mapLayer.remove();
                    PLAYBACKMANAGER.initMap();
                    $("#timeline").empty();
                }
                if (playBackTimes.length > 0) {
                    var startTime = playBackTimes[0];
                    var endTime = playBackTimes[playBackTimes.length - 1];
                    PLAYBACKMANAGER.setPlayBackTimeLine(startTime, endTime);
                    playBack = new L.Playback(mapLayer, PLAYBACKMANAGER.getPlayBack(playBackCordinates, playBackTimes), onPlaybackTimeChange, PLAYBACKMANAGER.getPlayBackOptions());
                    // Set timeline time change event, so cursor is set after moving custom time (blue)
                    timeLine.on('timechange', onCustomTimeChange);
                    // A callback so timeline is set after changing playback time
                    function onPlaybackTimeChange(ms) {
                        timeLine.setCustomTime(new Date(ms));
                    }

                    //
                    function onCustomTimeChange(properties) {
                        if (!playBack.isPlaying()) {
                            playBack.setCursor(properties.time.getTime());
                        }
                    }

                    PLAYBACKMANAGER.zoomToMarker();
                }

        },
        getRouteList: function () {
            var deviceId = $("#deviceId").val();
            var from = $("#from").val();
            var to = $("#to").val();
            $.post(APPLICATION.getBaseUrl() + "/tracking/playback/positions", {
                deviceId: deviceId,
                from: from,
                to: to
            }, function (response) {
                console.log(response);
                PLAYBACKMANAGER.loadDevicePlayBack(response.cordinates,response.timestamps);
            //PLAYBACKMANAGER.loadPlayBack(devicePositions);
            }, 'json');

        },
    };
}();
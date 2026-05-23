/**
 * Created by degre on 3/19/2018.
 */
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
    $(document).on("click", ".lnk-route", function (e) {
        e.preventDefault();
        e.stopImmediatePropagation();
        var posId = $(this).attr("data-position-id");
        var position = markerPositions[posId];
        console.log(position);
       var latlng = L.latLng(position.latitude,position.longitude);
        movingMarker.moveTo(latlng,1000);
        mapLayer.panTo(latlng);
        PLAYBACKMANAGER.showPositionInfo(position);
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
    window.movingMarker = null;
    window.routePolyLine = null;
    window.markerPositions = {};
   // window.playBackRoutes = [];
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
                zoom: 2,
                maxZoom: maxZoom,
                minZoom: 2,
                layers: [layer],
            });
            //  return baseMap;

            L.control.layers(PLAYBACKMANAGER.getBaseMaps()).addTo(mapLayer);


        },
        getBaseMaps: function () {
            return {
                "Google Road": TileFactory.GoogleRoad(),
                "Google Satelite": TileFactory.GoogleSatelite(),
                "Google Hybrid": TileFactory.GoogleHybrid(),
                "Google Terrain": TileFactory.GoogleTerrain(),
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
        convertMS: function (ms) {
            var d, h, m, s;
            s = Math.floor(ms / 1000);
            m = Math.floor(s / 60);
            s = s % 60;
            h = Math.floor(m / 60);
            m = m % 60;
            d = Math.floor(h / 24);
            h = h % 24;
            return {d: d, h: h, m: m, s: s};
        },
        calculateTime: function (prevPosition, nextPosition) {
            var prevDate = new Date(prevPosition.fixTime);
            var prevTime = prevDate.getTime();
            var nextDate = new Date(nextPosition.fixTime);
            var nextTime = nextDate.getTime();
            return Math.abs(nextTime - prevTime);
        },
        setLocationAddress: function (index) {
            if (index < playBackRoutes.length) {
                var position = playBackRoutes[index];
                var latitude = position.latitude;
                var longitude = position.longitude;
                var address = position.address;
                var locationInfo = PLAYBACKMANAGER.getLocationAddress(latitude, longitude);
                locationInfo.done(function (response) {
                    if (response.results[0]) {
                        $("#" + position.id).text(response.results[0].formatted_address);
                    }
                    address = "<Not Set>";
                    $("#" + position.id).text(address);
                    index++;
                    setInterval(function () {
                        PLAYBACKMANAGER.setLocationAddress(index);
                    }, 8000);

                });
            }
        },

        getTableRow: function (position, duration) {
            var latitude = position.latitude;
            var longitude = position.longitude;
            var address = (position.address == null) ? "Location Unknown" : position.address;
            var linkId = "lnk-" + position.id;
            var state = (position.attributes.motion == true) ? "Movement" : "Idle";
            var timeTaken =FormatFactory.convertMilliseconds(duration,"hh:mm:ss");

            var row = '<tr>';
            row += '<td id="' + position.id + '">' + address + "</td>";
            row += '<td><a href="#"  data-position-id="' + position.id + '"  id="' + linkId + '"  class="lnk-route">' + state + '</a></td>';
            row += '<td>' + timeTaken.clock + "</td>";
            row += "</tr>";
            $('#' + linkId).data('position', position);
            return row;
        },
        loadPlayBack: function (devicePositions) {
            // console.log(devicePositions);
            var playBackCordinates = [];
            var playBackTimes = [];
            var index = 0;
            var tblRows = "";
            markerPositions = {};
            if (devicePositions) {
                for (i = 0; i < devicePositions.length; i++) {
                    var position = devicePositions[i];
                    if (position.attributes.ignition == true) {
                        index = (i + 1);
                        if (index >= devicePositions.length) {
                            index = i;
                        }
                        //  var posIndex = position.latitude + "_" + position.longitude;
                        markerPositions[position.id] = position;
                      //  playBackRoutes.push(position);
                        playBackCordinates.push([position.latitude, position.longitude]);
                        //var date = new Date(position.fixTime);
                        //var timestamp = date.getTime();
                        var nextPosition = devicePositions[index];
                        var timestamp = PLAYBACKMANAGER.calculateTime(position, nextPosition);
                        playBackTimes.push(timestamp);
                        tblRows += PLAYBACKMANAGER.getTableRow(position, timestamp);
                        //PLAYBACKMANAGER.setLocationAddress(position);
                    }

                }
                if (routePolyLine != null) {
                    mapLayer.remove();
                    PLAYBACKMANAGER.initMap();
                }
              //  console.log(playBackTimes);
                if (playBackTimes.length > 0) {
                    // PLAYBACKMANAGER.setLocationAddress(0);
                    //  movingMarker = L.Marker.movingMarker(playBackCordinates,
                    // playBackTimes, {autostart: true}).addTo(mapLayer);


                    mapLayer.addLayer(L.marker(playBackCordinates[0]));
                    mapLayer.addLayer(L.marker(playBackCordinates[playBackCordinates.length - 1]));
                    routePolyLine = L.polyline(playBackCordinates, {color: 'blue'});
                    routePolyLine.addTo(mapLayer);
                   // routePolyLine.snakeIn();//#007DEF
                    routePolyLine.setText('\u25BA', {
                        repeat: true,
                        offset: 6,
                        attributes: {
                            fill: 'red',
                            'font-weight': 'bold',
                            'font-size': '24'
                        }
                    });
                    mapLayer.fitBounds(routePolyLine.getBounds());
                    movingMarker = L.Marker.movingMarker(playBackCordinates, playBackTimes, {
                        icon: PLAYBACKMANAGER.getMarkerIcon(),
                        autostart: false,
                        //  loop:true
                    });
                    mapLayer.addLayer(movingMarker);
                    movingMarker.on('end', function () {
                       // alert("destination reached");
                    });
                    movingMarker.on('start', function () {
                       // alert("started");
                    });
                   //PLAYBACKMANAGER.bindClickedEvent();
                    PLAYBACKMANAGER.initSlideMenu(tblRows);
                    movingMarker.start();
                    PLAYBACKMANAGER.addControlButtons();
                }
            }
        },
        getMarkerIcon: function () {
            var AwesomeOptions = {
                icon: "fa-truck",
                markerColor: "green",
                prefix: 'fa'
                //spin:true
            };
            return L.AwesomeMarkers.icon(AwesomeOptions);
        },
        showPositionInfo: function (devicePosition) {
            var table = "<table>";
            //   var devicePosition = position[0];
            var ignition = (devicePosition.attributes.ignition == true) ? "On" : "Of";
            //table += '<tr><td><i class="fa fa-truck"></i> Vehicle ID </td><td>' + device.name+ '</td></tr>';
            table += '<tr><td><i class="ion-ios-speedometer-outline"></i> Distance</td><td>' + FormatFactory.formatDistanceValue(devicePosition.attributes.distance, "km") + '</td></tr>';
            table += '<tr><td><i class="ion-ios-speedometer-outline"></i> Total Distance</td><td>' + FormatFactory.formatDistanceValue(devicePosition.attributes.totalDistance, "km") + '</td></tr>';
            table += '<tr><td> <i class="fa fa-tachometer"></i> Speed</td><td>' + FormatFactory.formatSpeedValue(devicePosition.speed, "kmh", "") + '</td></tr>';
            table += '<tr><td><i class="ion-ios-cog"></i> Ignition</td><td>' + ignition + '</td></tr>';
            table += '<tr><td><i class="fa fa-battery-half"></i> Battery</td><td>' + devicePosition.attributes.battery + 'V</td></tr>';
            table += '<tr><td><i class="ion-power"></i> Power </td><td>' + devicePosition.attributes.power + '</td></tr>';
            table += '<tr><td> <i class="fa fa-road"></i> Motion</td><td>' + devicePosition.attributes.motion + '</td></tr>';
            // table += '<tr><td> <i class="fa fa-location-arrow"></i> Location Address</td><td>' + devicePosition.address+ '</td></tr>';

            table += "</table>";
            if (movingMarker._popup != undefined) {
                movingMarker.unbindPopup();
            }
            movingMarker.closePopup();
            movingMarker.bindPopup(table);
            movingMarker.openPopup();
            var popup = L.popup()
                .setLatLng(movingMarker.getLatLng())
                .setContent(popupContent)
                .openOn(mapLayer);

        },
        addControlButtons:function () {
            var toggle = L.easyButton({
                states: [{
                    stateName: 'pause',
                    icon: 'fa-play',
                    title: 'pause playback',
                    onClick: function(control) {
                        movingMarker.resume();
                        control.state('resume');
                    }
                }, {
                    icon: 'fa-pause',
                    stateName: 'resume',
                    onClick: function(control) {
                        movingMarker.pause();
                        control.state('pause');
                    },
                    title: 'resume playback '
                }]
            });
            toggle.addTo(mapLayer);
            var arrowMarkers = L.easyButton({
                states: [{
                    stateName: 'hide-arrows',
                    icon: 'fa-eye-slash',
                    title: 'hide direction arrow markers',
                    onClick: function(control) {
                        routePolyLine.setText('', {
                            repeat:false,
                            offset: 6,
                            attributes: {
                                fill: 'red',
                                'font-weight': 'bold',
                                'font-size': '24'
                            }
                        });
                        control.state('show-arrows');
                    }
                }, {
                    icon: 'fa-eye',
                    stateName: 'show-arrows',
                    onClick: function(control) {
                        routePolyLine.setText('\u25BA', {
                            repeat: true,
                            offset: 6,
                            attributes: {
                                fill: 'red',
                                'font-weight': 'bold',
                                'font-size': '24'
                            }
                        });
                        control.state('hide-arrows');
                    },
                    title: 'show direction arrow markers '
                }]
            });
           arrowMarkers.addTo(mapLayer);
        },
        bindClickedEvent: function () {
            mapLayer.eachLayer(function (layer) {
                layer.on("click", PLAYBACKMANAGER.onMarkerClicked);
            });
        },
        onMarkerClicked: function (e) {
            var posIndex = e.latlng.lat + "_" + e.latlng.lng;
            var position = markerPositions[posIndex];
            console.log(position);
            var popupContent = "Position Information Not Available";
            if (position) {
                popupContent = PLAYBACKMANAGER.showPositionInfo(position);
            }
            var popup = L.popup()
                .setLatLng(e.latlng)
                .setContent(popupContent)
                .openOn(mapLayer);


            // alert("hi. you clicked the marker at " + e.latlng.lat + " " + e.target.id);

        },
        zoomToMarker: function () {
            mapLayer.eachLayer(function (layer) {
                if (layer instanceof L.Marker) {
                    //console.log(layer.getLatLng());
                    mapLayer.panTo(layer.getLatLng());
                }
            });
        },


        filterDevicePostions: function (positions, id) {
            return positions.filter(
                function (position) {
                    return position.deviceId == id;
                }
            );
        },
        loadDevicePlayBack: function (playBackCordinates, playBackTimes) {
            if (movingMarker != null) {
                mapLayer.remove();
                PLAYBACKMANAGER.initMap();
            }
            if (playBackTimes.length > 0) {
                movingMarker = L.Marker.movingMarker(playBackCordinates,
                    playBackTimes).addTo(mapLayer);
                var polyLine = L.polyline(playBackCordinates, {color: 'red'});
                polyLine.addTo(mapLayer);
                mapLayer.fitBounds(polyLine.getBounds());
                //PLAYBACKMANAGER.zoomToMarker();
                movingMarker.start();
                // alert(movingMarker.isRunning());
            }


        },
        getRouteList: function () {
            var routeListUrl = APPLICATION.getBaseUrl() + "/api/reports/route";//"/tracking/playback/positions";
            var deviceId = $("#deviceId").val();
            var from = $("#from").val();
            var to = $("#to").val();
            $.post(routeListUrl, {
                deviceId: deviceId,
                from: from,
                to: to
            }, function (response) {
                console.log(response);
                // PLAYBACKMANAGER.loadDevicePlayBack(response.cordinates,response.timestamps);
                PLAYBACKMANAGER.loadPlayBack(response);
            }, 'json');

        },
        initSlideMenu: function (rows) {
            var table = "<table class='table table-bordered table-responsive' style='width:100%'  id='tblRoute'>";
            table += "<thead><tr><th>Address</th><th>State</th><th>Duration</th></tr></thead>";
            table += "<tbody>" + rows + "</tbody>";
            table += "</table>";
            // right
            var slideMenu = L.control.slideMenu('', {
                position: 'topright',
                menuposition: 'topright',
                width: '30%',
                height: '400px',
                delay: '50'
            }).addTo(mapLayer);
            slideMenu.setContents(table);
        },
        getLocationAddress: function (latitude, longitude) {
            var apiKey = 'AIzaSyAHc_0NhgXfeeeP0EiVfvMdrKn9AXQslE0';
            var url = 'https://maps.googleapis.com/maps/api/geocode/json?latlng=' + latitude + ',' + longitude + '&sensor=false&key=' + apiKey;
            return $.ajax({
                type: "GET",
                url: url,
                dataType: 'json'
            });
        },
    };
}();
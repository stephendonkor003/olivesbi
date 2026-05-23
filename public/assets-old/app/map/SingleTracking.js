/**
 * Created by degre on 1/22/2018.
 */
/**
 * Created by degre on 12/5/2017.
 */

$(function () {
    MapController.initMap();
});
var MapController = function () {

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
    return {
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
                minZoom: 8,
                layers: [layer, markerLayer, liveRouteLayer],
            });
            //  return baseMap;

            L.control.layers(MapController.getBaseMaps(), MapController.getOverlayMaps()).addTo(mapLayer);


            // MapController.onSessionStart("", function () {
            //     MapController.realTimeTracking();
            // });
            MapController.initSession(function () {
                MapController.realTimeTracking();
            });
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
        disableAdminUIs: function (isAdmin) {
            if (!isAdmin) {
                $(".admin-ui-group").hide();
            }
        },
        initSession: function (callback) {
            $.get(APPLICATION.getBaseUrl() + "/api/user", function (response) {
                if (response.token !== null) {
                    MapController.disableAdminUIs(response.admin);
                    var sessionUrl = APPLICATION.getSessionURL() + "?token=" + response.token;
                    // $.get(sessionUrl,callback,'json');
                    APPLICATION.fireAjaxRequest("GET", sessionUrl, callback);
                } else {
                    window.location.reload();
                }
            }, "json");
        },
        onSessionStart: function (token, callback) {
            //  'GET', url + '/api/session?token=' + token
            var settings = {
                "async": true,
                "crossDomain": true,
                "url": APPLICATION.getSessionURL(),
                "method": "POST",
                contentType: "application/x-www-form-urlencoded",
                xhrFields: {
                    withCredentials: true
                },
                data: {
                    email: "",
                    password: ""
                }
            };
            $.ajax(settings).done(function (response) {
                // console.log(response);
                callback(response);
            });
        },
        forceCloseSocket: function () {
            // Close the connection, if open.
            if (trackingSocket != null) {
                if (trackingSocket.readyState === WebSocket.OPEN) {
                    trackingSocket.close();
                }
            }
        },
        realTimeTracking: function () {
            MapController.forceCloseSocket();
            trackingSocket = new WebSocket(APPLICATION.getSocketUrl());

            trackingSocket.onclose = function () {
                //APPLICATION.onError(StringConstants.errorSocket);

                $.ajax({
                    url: APPLICATION.getBaseUrl() + '/api/devices',
                    type: 'GET',
                    dataType: 'json',
                }).done(function (devices) {
                    //this.updateDevices(devices);
                }).fail(function (response) {
                    if (response.status === 401) {
                        window.location.replace(APPLICATION.getBaseUrl() + "/auth/logout");
                    }
                });

                $.ajax({
                    url: APPLICATION.getBaseUrl() + '/api/positions',
                    headers: {
                        Accept: 'application/json'
                    }
                }).done(function (positions) {
                    //  this.updatePositions(positions);

                }).fail(function (response) {
                    window.location.reload();
                    // APPLICATION.onError(response.responseText);
                });

                setTimeout(function () {
                    MapController.realTimeTracking();
                }, StyleFactory.reconnectTimeout);
            };

            trackingSocket.onmessage = function (event) {
                // console.log(event);
                var data = JSON.parse(event.data);

                if (data.devices) {
                    //alert('devices loaded');
                    // console.log("Devices Occurred"+data.devices);
                    //BaseMap.updateDevices(data.devices);
                    //  DeviceManager.initDeviceDataTable(data.devices);
                }
                if (data.positions) {
                    var deviceId = $("#map-canvas").attr('data-device-id');
                    var positions = MapController.filterDevicePositions(data.positions, deviceId);
                    //console.log(positions);
                    for (i = 0; i < positions.length; i++) {
                        MapController.updateLiveRoute(positions[i]);
                    }

                    // BaseMap.updateLatest(data.positions);
                    // self.updatePositions(data.positions, first);
                    //first = false;
                }
                if (data.events) {
                    //self.updateEvents(data.events);
                    //DeviceManager.updateEvents(data.events);
                    // console.log("Event Occurred"+data.events);
                }
            };
        },

        updateLiveRoute: function (position) {
            //console.log(position);
            var deviceId = $("#map-canvas").attr('data-device-id');
            DataManager.getDevice(deviceId, function (device) {
              //  if (position.deviceId == deviceId) {
                    var marker = latestMarkers[position.deviceId];
                    var polyLine = liveRoutes[position.deviceId];
                    var latlng = L.latLng(position.latitude, position.longitude, position.altitude);
                    var directionMarker = liveDirectionMarkers[position.deviceId];
                    if (!device) {
                        return;
                    }
                    if (!marker) {
                        //var deviceColor = MapController.getDeviceColor(device);

                        marker = L.marker(latlng, {
                            icon: MapController.setMarkerIcon(position, device),//MapController.getMarkerIcon(deviceColor, device.category),
                            title: device.name
                            // rotationAngle: 45
                        });
                        marker.id = device.id;
                        var dirlatlng = {lat: position.latitude, lon: position.longitude};
                        directionMarker = MapController.setDirectionMarker(dirlatlng, dirlatlng, 0);
                        //MapController.centerLeafletMapOnMarker(mapLayer, marker);
                        marker.bindPopup(device.name, {autoClose: false}).openPopup();
                        latestMarkers[position.deviceId] = marker;
                        liveDirectionMarkers[position.deviceId] = directionMarker;
                        markerDirections[position.deviceId] = [latlng];
                        markerLayer.addLayer(marker);
                        var poly = L.polyline([], {
                            color: 'green'
                        });
                        poly.addLatLng(latlng);
                        liveRoutes[position.deviceId] = poly;
                        liveRouteLayer.addLayer(poly).addTo(mapLayer);
                        MapController.centerMapOnMarker(marker);
                        MapController.bindMarkerClickEvent();
                        MapController.showGeofenceMap(device.id, mapLayer);
                    } else {
                        markerDirections[position.deviceId].push(latlng);
                        var prevLatln = marker.getLatLng();

                        marker.setLatLng(latlng).update();
                        marker.setIcon(MapController.setMarkerIcon(position, device));
                        if (true) {
                            polyLine.addLatLng(latlng);
                        }
                        mapLayer.panTo(latlng);
                        //MapController.updateDirectionMarker(position.deviceId);
                        var rotation = position.attributes.course;// * 180 / Math.PI;
                        //   mapLayer.removeLayer(directionMarker);
                        directionMarker = MapController.setDirectionMarker({
                            lat: prevLatln.lat,
                            lon: prevLatln.lng
                        }, {lat: latlng.lat, lon: latlng.lng}, rotation);
                        liveDirectionMarkers[position.deviceId] = directionMarker;
                        //marker.setRotationAngle(rotation);
                    }
                //}
            });
        },
        filterDevicePositions: function (positions, id) {
            return positions.filter(
                function (position) {
                    return position.deviceId == id;
                }
            );
        },
        updateDirectionMarker: function (deviceId) {
            var deviceDirection = markerDirections[deviceId];
            firstLatLng = deviceDirection[0];
            lastLatLng = deviceDirection[deviceDirection.length - 1];
            var marker = latestMarkers[deviceId];
            var heading = MapController.computeHeading(lastLatLng.lat, lastLatLng.lng, firstLatLng.lat, firstLatLng.lng);
            // marker.setRotationAngle(heading);

            var slope = ((lastLatLng.lng - firstLatLng.lng) / (lastLatLng.lat - firstLatLng.lat));
            var angle = Math.atan(slope);

            var rotation = angle * 180 / Math.PI;
            marker.setRotationAngle(rotation);
        },

        setMarkerIcon: function (position, device) {
            var ignition = position.attributes.ignition;
            var motion = position.attributes.motion;
            var color = "blue";
            if (position.protocol == "starlink") {
                if (position.attributes.alarm == "ignitionOnFuelLevel" || position.attributes.alarm == "ignitionOnFuelLevelPercent") {
                    ignition = true;
                } else {
                    ignition = false;
                }
            }
            if (!device) {
                color = "blue";
            } else if (device.status == "" || device.status == "offline" || device.status == "unknown") {
                color = "blue";
            }
            else if (ignition == true && motion == true) {
                color = 'green';
            } else if (ignition == true && motion == false) {
                color = 'orange';
            } else if (ignition == false) {
                color = 'red';
            }

            var AwesomeOptions = {
                icon: 'truck',
                markerColor: color,
                prefix: 'fa'
                //spin:true
            };
            return L.AwesomeMarkers.icon(AwesomeOptions);
        },
        setDirectionMarker: function (previousLatLng, nextLanLng, course) {
            var img = new Image();
            img.src = APPLICATION.getAssetUrl() + 'img/arrow.png';
            var options = {
                label: 'Moving ' + FormatFactory.courseFormatterHuman(course),
                labelFlag: false,
                labelColor: 'black',
                img: img,
                course: course,
                angle: course
            };

            // use angeMaker plugin
            var angleMarker = L.angleMarker(nextLanLng, options);
            var angle = 0;

            // get angele between A(previousPoint) and B(nextPoint)
            angle = angleMarker.getAngle(previousLatLng, nextLanLng);

            // set angele A -> B
            angleMarker.setHeading(angle);
            mapLayer.addLayer(angleMarker);
        },
        markerOnClick: function (e) {
            DataManager.getDevice(e.target.id, function (device) {
                console.log(device);
                //alert("hi. you clicked the marker at " + device.name);
                var popup = L.popup()
                    .setLatLng(e.latlng)
                    .setContent(MapController.getDevicePopupTable(device, e.target))
                    .openOn(mapLayer);


            });
            //alert("hi. you clicked the marker at " + e.latlng +" "+e.target.id);

        },
        getMarkerFromLayer: function (layerGroup, deviceId) {
            layerGroup.eachLayer(function (layer) {
                if (layer.id === deviceId) {
                    return layer;
                }
            })
        },
        bindMarkerClickEvent: function () {
            markerLayer.eachLayer(function (layer) {
                layer.on("click", MapController.markerOnClick);
            });
        },
        centerMapOnMarker: function (marker) {
            if (marker) {
               // var latLngs = [marker.getLatLng()];
               //  var markerBounds = L.latLngBounds(latLngs);
               //  mapLayer.fitBounds(markerBounds);
                mapLayer.setView(marker.getLatLng(), 13);
                mapLayer.panTo(marker.getLatLng());
            }
        },
        getDevicePopupTable: function (device, marker) {
            DataManager.getDevicePosition(device.positionId, function (position) {
                console.log(position);
                var table = "<table>";
                var devicePosition = position[0];
                var ignition = (devicePosition.attributes.ignition == true) ? "On" : "Of";
                if (position.protocol == "starlink") {
                    if (position.attributes.alarm == "ignitionOnFuelLevel" || position.attributes.alarm == "ignitionOnFuelLevelPercent") {
                        ignition = "On";
                    } else {
                        ignition = "Off";
                    }
                }
                table += '<tr><td><i class="ion-ios-speedometer-outline"></i> Distance</td><td>' + FormatFactory.formatDistanceValue(devicePosition.attributes.distance, "km") + '</td></tr>';
                table += '<tr><td><i class="ion-ios-speedometer-outline"></i> Total Distance</td><td>' + FormatFactory.formatDistanceValue(devicePosition.attributes.totalDistance, "km") + '</td></tr>';
                table += '<tr><td> <i class="fa fa-tachometer"></i> Speed</td><td>' + FormatFactory.formatSpeedValue(devicePosition.speed, "kmh", "") + '</td></tr>';
                table += '<tr><td><i class="ion-ios-cog"></i> Ignition</td><td>' + ignition + '</td></tr>';
                table += '<tr><td><i class="fa fa-battery-half"></i> Battery</td><td>' + devicePosition.attributes.battery + 'V</td></tr>';
                table += '<tr><td><i class="ion-power"></i> Power </td><td>' + devicePosition.attributes.power + '</td></tr>';
                table += '<tr><td> <i class="fa fa-road"></i> Motion</td><td>' + devicePosition.attributes.motion + '</td></tr>';
                // table += '<tr><td> <i class="fa fa-location-arrow"></i> Location Address</td><td>' + devicePosition.address+ '</td></tr>';

                table += "</table>";
                if (marker._popup != undefined) {
                    marker.unbindPopup();
                }
                marker.closePopup();
                marker.bindPopup(table);
                marker.openPopup();

            });

        },
        // centerMapOnMarker: function (deviceId) {
        //     var marker = latestMarkers[deviceId];
        //     if (marker) {
        //         var latLngs = [marker.getLatLng()];
        //         var markerBounds = L.latLngBounds(latLngs);
        //         mapLayer.fitBounds(markerBounds);
        //        // mapLayer.setView(marker.getLatLng(), 16);
        //         //mapLayer.panTo(marker.getLatLng());
        //     }
        // },
        showGeofenceMap: function (deviceId, map) {
            return $.get(APPLICATION.getBaseUrl() + "/api/geofences", function (geofences) {
                    $.get(APPLICATION.getBaseUrl() + "/api/geofences/" + deviceId, function (deviceGeofences) {

                        var deviceGeofenceIds = $.map(deviceGeofences, function (gf) {
                            return gf.id;
                        });
                        console.log(deviceGeofenceIds);
                        geofences.forEach(function (geofence) {
                            if ($.inArray(geofence.id, deviceGeofenceIds) !== -1) {
                                if (!MapController.isCircle(geofence.area)) {

                                    var geojson = Terraformer.WKT.parse(geofence.area);

                                    // Create a geojson layer with our new geojson object
                                    var layer = L.geoJson(geojson, {}).addTo(map);
                                    layer.bindPopup(geofence.name, {autoClose: false}).openPopup();
                                    // get a geojson bounding box xmin, ymin, xmax, ymax
                                    //  var bounds = geojson.bbox();

                                    // fit the map tp the bounds of the fire.
                                    //  map.fitBounds([ [bounds[1], bounds[0]], [bounds[3], bounds[2]] ]);
                                    geofenceLayer.addLayer(layer);
                                } else {
                                    var splitter = geofence.area.split("(");
                                    var cordinates = splitter[1].split(',');
                                    var latLon = cordinates[0].split(" ");
                                    var radius = cordinates[1].replace(")", "");
                                    var circle = L.circle([latLon[0], latLon[1]], {radius: radius});
                                    circle.bindPopup(geofence.name, {autoClose: false}).openPopup();
                                    geofenceLayer.addLayer(circle).addTo(map);

                                    //   console.log(circle);
                                    // console.log(radius);
                                }
                            }
                        });

                    }, "json");

                }
                , 'json');
        },
        isCircle: function (area) {
            var circleArea = "CIRCLE";
            return area.indexOf(circleArea) !== -1;
        },
        computeHeading: function (lat1, long1, lat2, long2) {
            // Converts from degrees to radians.
            Math.radians = function (degrees) {
                return degrees * Math.PI / 180;
            };
            // Converts from radians to degrees.
            Math.degrees = function (radians) {
                return radians * 180 / Math.PI;
            };
            var rlat1 = Math.radians(lat1);
            var rlat2 = Math.radians(lat2);
            var dlong = Math.radians(long2 - long1);
            var y = Math.cos(rlat2) * Math.sin(dlong);
            var x = Math.cos(rlat1) * Math.sin(rlat2) - Math.sin(rlat1) * Math.cos(rlat2) * Math.cos(dlong);
            var heading = Math.round(Math.degrees(Math.atan2(y, x)) + 360, 4) % 360;
            return heading;
        },
        testMultiArray: function () {
            var myArray = {};
            for (var i = 0; i < 10; i++) {
                myArray[i] = [i + 1, i + 2, i + 3];
            }
            myArray[0].push(157);
            console.log(myArray);
        },
    }


}();
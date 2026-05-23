/**
 * Created by degre on 12/1/2017.
 */
var BaseMap = new function () {
    window.liveRouteLayer = new L.layerGroup();
    window.markerLayer = new L.LayerGroup();
    window.routeSource = new L.layerGroup();
    window.geofenceLayer = new L.featureGroup();
    window.mapLayer = null;

    window.latestMarkers = {};
    window.reportMarkers = {};
    window.accuracyCircles = {};
    window.liveRoutes = {};
    window.liveRouteLength = 10;// Traccar.app.getAttributePreference('web.liveRouteLength', 10);
    window.selectZoom = 0;
    this.getBaseMaps = function () {
        return {
            "Google Road": TileFactory.GoogleRoad(),
            "Google Satelite": TileFactory.GoogleSatelite(),
            "Open Street": TileFactory.OpenStreetMap(),
            "Arc Gis": TileFactory.ArcGis(),
            "Arc Gis Imagery": TileFactory.ArcGisImagery(),
            "Wikimedia": TileFactory.WikimediaMap()
        };
    };
    this.getOverlayMaps = function () {
        return {
            "Trucks": this.getMarkersLayer(),
            "Route": this.getLiveRouteLayer(),
            "Geofences": this.getGeofenceLayer()
        }
    };
    this.layout = 'fit';

    this.getMap = function () {
        return mapLayer;
    };

    this.getMarkersLayer = function () {
        return markerLayer;
    };
    this.getGeofenceLayer = function () {
        return geofenceLayer;
    };
    this.getLiveRouteLayer = function () {
        return liveRouteLayer;
    };
    this.getRouteSource = function () {
        return routeSource;
    };

    this.initMap = function () {
        var server, layer, type, bingKey, lat, lon, zoom, maxZoom;

        server = DataManager.getServer();

        type = 'googleRoad';//APPLICATION.getPreference('map', null);
        //bingKey = server.get('bingKey');

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
        lat = StyleFactory.mapDefaultLat;//APPLICATION.getPreference('latitude', StyleFactory.mapDefaultLat);
        lon = StyleFactory.mapDefaultLon;//APPLICATION.getPreference('longitude', StyleFactory.mapDefaultLon);
        zoom = StyleFactory.mapDefaultZoom;//APPLICATION.getPreference('zoom', StyleFactory.mapDefaultZoom);
        maxZoom = StyleFactory.mapMaxZoom; //APPLICATION.getAttributePreference('web.maxZoom', StyleFactory.mapMaxZoom);


        mapLayer = L.map('map-canvas', {
            center: [lat, lon],
            'zoom': zoom,
            'maxZoom': maxZoom,
            layers: [layer, this.getMarkersLayer(), this.getLiveRouteLayer()],
        });
        //  return baseMap;

        L.control.layers(this.getBaseMaps(), this.getOverlayMaps()).addTo(mapLayer);
       BaseMap.realTimeTracking();
        //
        //     this.map.on('pointermove', function (e) {
        //         //To do
        //     });
        //
        // this.map.on('click', function (e) {
        //     //To do add map click event
        // }, this);
    };
    this.centerLeafletMapOnMarker = function (marker) {
        var latLngs = [marker.getLatLng()];
        var markerBounds = L.latLngBounds(latLngs);
        this.map.fitBounds(markerBounds)
    };
    this.realTimeTracking = function () {
        var self = this, protocol, pathname, socket;
        //protocol = window.location.protocol === 'https:' ? 'wss:' : 'ws:';
        // pathname = window.location.pathname.substring(0, window.location.pathname.lastIndexOf('/') + 1);

        socket = new WebSocket(APPLICATION.getSocketUrl());

        socket.onclose = function () {
            APPLICATION.onError(StringConstants.errorSocket);

            $.ajax({
                url: APPLICATION.getBaseUrl() + '/api/devices',
                type: 'GET',
                dataType: 'json',
            }).done(function (devices) {
                this.updateDevices(devices);
            }).fail(function (response) {
                if (response.status === 401) {
                    window.location.reload();
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
                APPLICATION.onError(response.responseText);
            });

            setTimeout(function () {
                this.realTimeTracking();
            }, StyleFactory.reconnectTimeout);
        };

        socket.onmessage = function (event) {
            var data = JSON.parse(event.data);

            if (data.devices) {
                BaseMap.updateDevices(data.devices);
            }
            if (data.positions) {
                BaseMap.updateLatest(data.positions);
                // self.updatePositions(data.positions, first);
                //first = false;
            }
            if (data.events) {
                //self.updateEvents(data.events);
            }
        };
    };

    this.updateDevices = function (devices) {
        DeviceManager.refreshDeviceDataTable(devices);
        this.updateDeviceMarkers(devices);
    };
    this.updateDeviceMarkers = function (devices) {
        var i, device, deviceId, marker, style;

        if (!$.isArray(devices)) {
            data = [devices];
        }

        for (i = 0; i < devices.length; i++) {
            device = devices[i];
            deviceId = device.deviceId;

            if ($.inArray(deviceId, latestMarkers)!==-1) {
                marker = latestMarkers[deviceId];
                // style = marker.getStyle();
                if (marker.options.icon.options.icon !== device.category) {
                    this.updateDeviceMarker(marker, this.getDeviceColor(device), device.category);
                }
                // if (style.getText().getText() !== device.get('name')) {
                //     style.getText().setText(device.get('name'));
                //     marker.changed();
                // }
            }
        }
    };
    this.getDeviceColor = function (device) {
        switch (device.status) {
            case 'online':
                return StyleFactory.mapColorOnline;
            case 'offline':
                return StyleFactory.mapColorOffline;
            default:
                return StyleFactory.mapColorUnknown;
        }
    };

    this.getMarkerIcon = function (color, category) {
        var AwesomeOptions = {
            icon: category,
            markerColor: color,
            prefix: 'fa',
            //spin:true
        };
        return L.AwesomeMarkers.icon(AwesomeOptions);
    };
    this.getMarkerFromLayer = function (layerGroup, deviceId) {
        layerGroup.eachLayer(function (layer) {
            if (layer.id === deviceId) {
                return layer;
            }
        });
    };
    this.updateLatest = function (positions) {
        var i, position, device, deviceStore;

        if (!$.isArray(positions)) {
            positions = [positions];
        }

        DataManager.getDevices(function (devices) {
            for (i = 0; i < positions.length; i++) {
                position = positions[i];
                device = DataManager.findDeviceById(devices, position.deviceId)

                if (device) {
                    //  this.updateAccuracy(position, device);
                    BaseMap.updateLatestMarker(position, device);
                    BaseMap.updateLiveRoute(position, device);
                }
            }
        });


    };


    this.updateLatestMarker = function (position, device) {
        var latlng, deviceId, marker, style;
        // geometry = new ol.geom.Point(ol.proj.fromLonLat([
        //     position.get('longitude'),
        //     position.get('latitude')
        // ]));

        latlng = L.latLng(position.latitude, position.longitude);

        deviceId = position.deviceId;
        if ($.inArray(deviceId,latestMarkers)!==-1) {
            marker = this.latestMarkers[deviceId];
            // style = marker.getStyle();
            // if (style.getImage().angle !== position.get('course')) {
            //     this.rotateMarker(style, position.get('course'));
            // }
            marker.setLatLng(latlng).update();
            //  marker.setGeometry(geometry);
        } else {

            marker = L.marker(latlng, {
                icon: BaseMap.getMarkerIcon(this.getDeviceColor(device), device.category),
                title: device.name
                // rotationAngle: 45
            });
            marker.id = device.id;
            marker.bindPopup(device.name, {autoClose: false}).openPopup();
            latestMarkers[deviceId] = marker;
            if (this.isDeviceVisible(device)) {
                markerLayer.addLayer(marker);
            }
        }

        // if (marker === this.selectedMarker && this.lookupReference('deviceFollowButton').pressed) {
        //     this.getView().getMapView().setCenter(marker.getGeometry().getCoordinates());
        // }
        if (marker === this.selectedMarker) {
            BaseMap.getMap().setCenter(latlng);
        }
    };

    this.updateLiveRoute = function (position, device) {
        var deviceId, liveLine, liveCoordinates, lastLiveCoordinates, newCoordinates;
        deviceId = position.deviceId;
        if ($.inArray(deviceId,liveRoutes)!==-1) {
            liveCoordinates = this.liveRoutes[deviceId].getLatLngs();//.getGeometry().getCoordinates();
            lastLiveCoordinates = liveCoordinates[liveCoordinates.length - 1];
            newCoordinates = L.latLng(position.latitude, position.longitude);
            if (lastLiveCoordinates[0] === newCoordinates[0] &&
                lastLiveCoordinates[1] === newCoordinates[1]) {
                return;
            }
            if (liveCoordinates.length >= this.liveRouteLength) {
                liveCoordinates.shift();
            }
            liveCoordinates.push(newCoordinates);
            this.liveRoutes[deviceId].setLatLngs(liveCoordinates);//getGeometry().setCoordinates(liveCoordinates);
        } else {
            // liveLine = new ol.Feature({
            //     geometry: new ol.geom.LineString([
            //         ol.proj.fromLonLat([
            //             position.get('longitude'),
            //             position.get('latitude')
            //         ])
            //     ])
            // });
            // liveLine.setStyle(this.getRouteStyle(deviceId));
            // liveLine.setId(deviceId);
            var latlng = L.latLng(position.latitude, position.longitude);
            liveLine = L.polyline([], {
                color: 'green'
            });
            liveLine.id = deviceId;
            liveLine.addLatLng(latlng);

            liveRoutes[deviceId] = liveLine;
            if (this.isDeviceVisible(device)) {
                //var routeLayer = BaseMap.getLiveRouteLayer();
                liveRouteLayer.addLayer(liveLine).addTo(BaseMap.getMap());
            }
        }
    };


    this.updateDeviceMarker = function (marker, color, category) {
        var markerIcon = this.getMarkerIcon(color, category);
        marker.setIcon(markerIcon).update();
        //  var angle = 360 - (Math.atan2(diffLat, diffLng)*57.295779513082)
    };


    this.selectDevice = function (device, center) {
        this.selectMarker(this.latestMarkers[device.get('id')], center);
    };


    this.zoomToAllPositions = function (data) {
        var i, point, minx, miny, maxx, maxy;
        for (i = 0; i < data.length; i++) {
            var position = data[i];
            point = L.latLng(position.latitude, position.longitude);

            if (i === 0) {
                minx = maxx = point[0];
                miny = maxy = point[1];
            } else {
                minx = Math.min(point[0], minx);
                miny = Math.min(point[1], miny);
                maxx = Math.max(point[0], maxx);
                maxy = Math.max(point[1], maxy);
            }
        }
        if (minx !== maxx || miny !== maxy) {
            BaseMap.getMap().fitBounds([minx, miny, maxx, maxy]);
        } else if (point) {
            this.getView().getMapView().fit(new ol.geom.Point(point));
        }
    };


    this.isDeviceVisible = function (device) {
        return true;//Ext.getStore('VisibleDevices').contains(device);
    };


};
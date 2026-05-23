/**
 * Created by degre on 12/5/2017.
 */


var MapController = function () {

    var mapDiv = $("#map");
    var mapContainer = $("#app-map-container");
    window.boundaryLayer = null;
    window.assemblyLayer = null;
    window.divisionLayer=null;
    window.workingLayer = null;
    window.markerLayer = new L.featureGroup();
    window.routeSource = new L.layerGroup();
    window.geofenceLayer = new L.featureGroup();
    window.mapLayer = null;
    window.boundaryMap = null;
    window.geoJsonLayers = [];

    window.selectZoom = 0;
    window.mapDefaultLat = 1.283333;
    window.mapDefaultLon = 103.833333;
    window.mapDefaultZoom = 4;
    window.mapMaxZoom = 18;
    return {
        initMap: function (mapCanvas) {
            //MapController.testMultiArray();
            var type = 'openStreetMap';
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
                case 'osmBuilding':
                    layer = TileFactory.osmBuilding();
                    break;
                case 'googleTraffic':
                    layer = TileFactory.googleTraffic();
                    break;
                case 'arcGis':
                    layer = TileFactory.ArcGis();
                    break;
                case 'arcGisImagery':
                    layer = TileFactory.ArcGisImagery();
                    break;
                default:
                    layer = TileFactory.GoogleRoad();
                    break;
            }
            var lat = mapDefaultLat;//APPLICATION.getPreference('latitude', StyleFactory.mapDefaultLat);
            var lon = mapDefaultLon;//APPLICATION.getPreference('longitude', StyleFactory.mapDefaultLon);
            var zoom = mapDefaultZoom;//APPLICATION.getPreference('zoom', StyleFactory.mapDefaultZoom);
            var maxZoom = mapMaxZoom; //APPLICATION.getAttributePreference('web.maxZoom', StyleFactory.mapMaxZoom);
            mapLayer = L.map(mapCanvas, {
                center: [7.97220, -0.20894],
                zoom: 2,
                maxZoom: 18,
                minZoom: 8,
                layers: [layer, markerLayer, liveRouteLayer],
            });
            //  return baseMap;

            L.control.layers(MapController.getBaseMaps(), MapController.getOverlayMaps()).addTo(mapLayer);
            // var osmb = new OSMBuildings(mapLayer).load('https://{s}.data.osmbuildings.org/0.2/anonymous/tile/{z}/{x}/{y}.json');
        },
        initBoundaryMap: function (mapCanvas) {
            try {
                const map = L.map(mapCanvas).setView([7.97220, -0.20894], 13);
                L.tileLayer('https://{s}.tile.osm.org/{z}/{x}/{y}.png', {
                    attribution: '&copy; <a href="https://osm.org/copyright">OpenStreetMap</a> contributors'
                }).addTo(map);
                var geocoder = L.Control.geocoder({
                    defaultMarkGeocode: false
                })
                    .on('markgeocode', function (e) {
                        var bbox = e.geocode.bbox;
                        var poly = L.polygon([
                            bbox.getSouthEast(),
                            bbox.getNorthEast(),
                            bbox.getNorthWest(),
                            bbox.getSouthWest()
                        ]).addTo(map);
                        map.fitBounds(poly.getBounds());
                    })
                    .addTo(map);
            } catch (e) {
                console.log(e);
            }
        },
        initGeoSearchControl: function (map) {
            var search = new GeoSearch.GeoSearchControl({
                provider: new GeoSearch.OpenStreetMapProvider(),
                style: 'bar',
                showMarker: true, // optional: true|false  - default true
                showPopup: false, // optional: true|false  - default false
                marker: {
                    // optional: L.Marker    - default L.Icon.Default
                    icon: new L.Icon.Default(),
                    draggable: false,
                },
                popupFormat: ({query, result}) => result.label, // optional: function    - default returns result label
                maxMarkers: 1, // optional: number      - default 1
                retainZoomLevel: false, // optional: true|false  - default false
                animateZoom: true, // optional: true|false  - default true
                autoClose: false, // optional: true|false  - default false
                searchLabel: 'Enter address', // optional: string      - default 'Enter address'
                keepResult: false, // optional: true|false  - default false
            });
            map.addControl(search);
            map.on('geosearch/showlocation', function (e) {
                console.log(e);
            });
        },
        initGeoman: function (mapCanvas, boundaryType) {//
            //7.97220, -0.20894
            const map = L.map(mapCanvas).setView([7.95277, -1.03071], 12);
            L.tileLayer('https://{s}.tile.osm.org/{z}/{x}/{y}.png', {
                attribution: '&copy; <a href="https://osm.org/copyright">OpenStreetMap</a> contributors'
            }).addTo(map);
            L.control.layers(MapController.getBaseMaps()).addTo(map);
            map.pm.addControls({
                drawMarker: true,
                drawPolygon: true,
                editPolygon: true,
                drawPolyline: true,
                editMode: true,
                deleteLayer: true
            });

            //$(".leaflet-pm-toolbar").eq(1).append('<button  id="btn-geocode-save" class="btn btn-icon leaflet-buttons-control-button text-center btn-sm" type="button" title="Save" style="z-index:9999;"><i class="fa fa-save"></i></button>');

            //MapController.initGeoSearchControl(map);
            //MapController.initGeocoder(map);
            switch (boundaryType) {
                case Common.constants().ASSEMBLY:
                    var assembly = $("#app-map-container").data('assembly');
                    MapController.initGeocodeControls(map);
                    MapController.loadBoundary(map, assembly.boundary,true,false);
                    break;
                case Common.constants().DIVISION:
                    MapController.loadAssemblyLayer(map);
                    var division = $(".app-map-container").data('division');
                    MapController.loadBoundary(map, division.geofence,true,false);
                    break;
                case Common.constants().BLOCK:
                    MapController.loadDivisionLayer(map);
                    var block = $(".block-map-container").data('block');
                    MapController.loadBoundary(map,block.geofence,true,true);
                    break;
            }
            L.easyButton('fa-save', function (btn, map) {
              //  alert('here');
                if (workingLayer != null) {
                    Common.confirmAction("Confirm Saving Geo Data",function(){
                        MapController.saveBoundary(workingLayer.toGeoJSON(), boundaryType);
                    },null);
                }
            }).addTo(map);
            $(document).on('click', '#btn-geocode-search', function (e) {
                e.preventDefault();
                MapController.searchNominatim(map);
            });

            // $(document).on('click', '#btn-geocode-save', function (e) {
            //     e.preventDefault();
            //     if (boundaryLayer != null) {
            //         MapController.saveBoundary(boundaryLayer.toGeoJSON(), boundaryType);
            //     }
            // });

        },
        loadAssemblyLayer: function (map) {
            try {
                var boundaryGeoJson = null;
                var boundary = Common.getAssembly().boundary;
                if (boundary != null) {
                    try {
                        boundaryGeoJson = JSON.parse(boundary);
                    } catch (e) {
                        boundaryGeoJson = null;
                    }
                    if (boundaryGeoJson != null) {
                        if (boundaryGeoJson.hasOwnProperty("type")) {
                            if (assemblyLayer != null) {
                                assemblyLayer.clearLayers();
                            }
                            assemblyLayer = L.geoJSON(boundaryGeoJson, {
                                style: function (feature) {
                                    return {
                                        //  fillColor: 'white',
                                        weight: 2,
                                        opacity: 1,
                                        color: 'red',  //Outline color
                                        fillOpacity: 0.0
                                    };
                                }
                            });//.addTo(map);
                            assemblyLayer.addTo(map);
                            map.fitBounds(assemblyLayer.getBounds());

                        }
                    }
                }
            } catch (e) {
                console.log(e);
            }
        },
        loadDivisionLayer: function (map) {
            try {
               // console.log($(".block-map-container").data("division"));
                var boundaryGeoJson = null;
                var boundary = $(".block-map-container").data("division").geofence;
                if (boundary != null) {
                    try {
                        boundaryGeoJson = JSON.parse(boundary);
                    } catch (e) {
                        boundaryGeoJson = null;
                    }
                    if (boundaryGeoJson != null) {
                        if (boundaryGeoJson.hasOwnProperty("type")) {
                            if (divisionLayer != null) {
                                divisionLayer.clearLayers();
                            }
                            divisionLayer = L.geoJSON(boundaryGeoJson, {
                                style: function (feature) {
                                    return {
                                        //  fillColor: 'white',
                                        weight: 2,
                                        opacity: 1,
                                        color: 'red',  //Outline color
                                        fillOpacity: 0.0
                                    };
                                }
                            });//.addTo(map);
                           divisionLayer.addTo(map);
                            map.fitBounds(divisionLayer.getBounds());
                        }
                    }
                }
            } catch (e) {
                console.log(e);
            }
        },
        getBaseMaps:

            function () {
                return {
                    "Google Road": TileFactory.GoogleRoad(),
                    "Google Satelite": TileFactory.GoogleSatelite(),
                    "Google Traffic": TileFactory.googleTraffic(),
                    "Open Street": TileFactory.OpenStreetMap(),
                    "Arc Gis": TileFactory.ArcGis(),
                    "Arc Gis Imagery": TileFactory.ArcGisImagery(),
                    "Wikimedia": TileFactory.WikimediaMap(),
                    "OSM Building": TileFactory.osmBuilding()
                };
            }

        ,
        getOverlayMaps: function () {
            return {
                "Trucks": markerLayer,
                "Route": liveRouteLayer,
                "Geofences": geofenceLayer
            }
        }
        ,

        initGeocodeControls: function (map) {
            /*  L.control.custom({
                  position: 'topleft',
                  content: '        <button  id="btn-geocode-save" class="btn btn-icon leaflet-buttons-control-button btn-sm" type="button" title="Save"><i class="fa fa-save"></i></button>',
                  classes: ''
                  // style:
                  //     {
                  //         position: 'absolute',
                  //         left: '50px',
                  //         top: '0px'
                  //         // width: '500px'
                  //     },
              })
                  .addTo(map);
                  */
            var content = '<div class="input-group">' +
                '    <input type="text" class="form-control input-sm" placeholder="Enter Address" id="input-geocode-search" value="Adenta,Accra">' +
                '    <span class="input-group-btn">' +
                '        <button  id="btn-geocode-search" class="btn btn-primary btn-sm" type="button" title="Search An Address"><i class="fa fa-search"></i> Search</button>' +
                '    </span>' +
                '</div>';
            L.control.slideMenu(content, {
                position: 'topright',
                width: '600px',
                height: '50px',
                menuposition: 'topright',
                icon: 'fa-search'

            }).addTo(map);
        }
        ,

        toOverPassCords: function (boundaryCordinates) {
            var defered = $.Deferred();
            var coords = [];
            if (boundaryCordinates.length <= 0) {
                return coords.join(' ');
                // defered.resolve(coords.join(' '));
                // return defered.promise();
            }
            boundaryCordinates.forEach(function (latlng) {
                var point = latlng[1] + " " + latlng[0];
                coords.push(point)
            });
            return MapController.replace(coords.join(' '), ",", " ");
            // return coordinates;
            //defered.resolve(coordinates);
            // return defered.promise();
        }
        ,

        loadGeoJsonData: function (geoJsonData, map, showBlocks, opic) {
            if (workingLayer != null) {
                geoJsonLayers = [];
                workingLayer.clearLayers();
            }
            workingLayer = L.geoJson(geoJsonData, {
                style: function (feature) {
                    return {
                        //  fillColor: 'white',
                        weight: 2,
                        opacity: 1,
                        color: 'blue',  //Outline color
                        fillOpacity: opic ? 0.1 : 0.0
                    };
                },
                pointToLayer: (feature, latlng) => {
                    if (feature.properties.radius) {
                        return new L.Circle(latlng, feature.properties.radius);
                    } else {
                        return new L.Marker(latlng);
                    }
                },
                onEachFeature: (feature, layer) => {
                     /*if(feature.properties.hasOwnProperty('name')){
                         layer.bindPopup(feature.properties.name);
                     }
                     */
                    layer.bindPopup(JSON.stringify(feature.properties));
                },
            });
            workingLayer.addTo(map);
            // workingLayer.pm.enable({
            //     allowSelfIntersection: false,
            // });
            map.fitBounds(workingLayer.getBounds());
            workingLayer.on('pm:edit', function (e) {
                console.log(e);
                var layer = e.layer;
                console.log(layer);
            });

            //MapController.fireDrawable(map);
            /*var json = polygon.toGeoJSON();
            L.extend(json.properties, polygon.properties)*/

        }
        ,
        onLayerRemove: function (Layer) {
            workingLayer.eachLayer(function (layer) {
                if (layer.feature.properties.id === Layer.feature.properties.id) {
                    workingLayer.removeLayer(layer);
                }
            });
        },
        fireDrawable: function (map) {
            //console.log(map);
            map.on('pm:edit', function (e) {
                console.log(e);
            });
            map.on('pm:remove', function (e) {
                MapController.onLayerRemove(e.layer);
            });
           /* map.on('layerremove', function (e) {
                //console.log(e);
              //  console.log(e.layer._leaflet_id);
            });
            */
            map.on('pm:dragstart', function (e) {
                console.log(e);
            });
            map.on('pm:create', function (e) {
                //console.log(e);
                if (workingLayer == null) {
                    workingLayer = L.geoJSON();
                }
                var lng, lat, coords = [];
                var layer = e.layer;
                if (layer instanceof L.Polygon) {
                    // structure the geojson object
                    var geojson = {};
                    geojson['type'] = 'Feature';
                    geojson['properties'] = {id:123};// Common.generateUniqueId()};
                    geojson['geometry'] = {};
                    geojson['geometry']['type'] = "Polygon";
                    // export the coordinates from the layer
                    var latlngs = layer.getLatLngs();
                    if (!L.LineUtil.isFlat(latlngs)) {
                        latlngs = latlngs[0];
                    }
                    latlngs.forEach(function (latlng, idx) {
                        coords.push([latlng.lng, latlng.lat])
                    });
                    // push the coordinates to the json geometry
                    geojson['geometry']['coordinates'] = [coords];
                    // Finally, show the poly as a geojson object in the console
                    // console.log(JSON.stringify(geojson));
                    L.extend(geojson.properties, layer.properties)
                    workingLayer.addData(geojson);
                    console.log(workingLayer.toGeoJSON());

                } else if (layer instanceof L.Circle) {
                    var geojson = {};
                    geojson['type'] = 'Feature';
                    geojson['properties'] = {id:123};// Common.generateUniqueId()};
                    geojson['geometry'] = {};
                    geojson['geometry']['type'] = "Point";
                    coords = [];
                    var latlng = layer.getLatLng();
                    geojson['geometry']['coordinates'] = [latlng.lng, latlng.lat];
                    ///console.log(JSON.stringify(geojson));
                    workingLayer.addData(geojson);
                }
                // boundaryLayer.addLayer(layer);
                /* var geojson = e.layer.toGeoJSON();
                 var wkt = Terraformer.WKT.convert(geojson.geometry);
                 console.log(wkt);
                 alert(wkt);*/
            });
        },
        loadBoundary: function (map, boundary,opic,loadBlocks) {
            var boundaryGeoJson = null;
            if (boundary !== null) {
                try {
                    boundaryGeoJson = JSON.parse(boundary);
                } catch (e) {
                    boundaryGeoJson = null;
                }
                if (boundaryGeoJson !== null) {
                    if (boundaryGeoJson.hasOwnProperty("type")) {
                        if (boundaryGeoJson.features.length > 0) {
                            MapController.loadGeoJsonData(boundaryGeoJson, map, loadBlocks,opic);
                        if(loadBlocks && (boundaryGeoJson.features.length===1)){
                            MapController.fetchBuildingsViaOverpassAPI(map, boundaryGeoJson.features[0].geometry.coordinates[0]);// coords.join(' '));
                        }

                        } else {
                            MapController.fireDrawable(map);
                        }
                    } else {
                        MapController.fireDrawable(map);
                    }
                }else{
                    MapController.fireDrawable(map);
                }
            }else{
                MapController.fireDrawable(map);
            }
        }
        ,
        searchNominatim: function (map) {
            var qry = $("#input-geocode-search").val();
            $.ajax({
                type: "GET",
                url: "https://nominatim.openstreetmap.org/search.php?q=" + encodeURIComponent(qry) + "&format=geojson&polygon_geojson=1&country=Ghana",
                dataType: "json",
                beforeSend: function () {
                    Common.showSpinner($("#map"), "Loading " + qry + " Boundary Coordinates...");
                    return true;
                }
            }).done(function (response) {
                Common.hideSpinner($("#map"));
                try {
                    if (response.hasOwnProperty('features')) {
                        if (response.features.length > 0) {
                            MapController.loadGeoJsonData(response, map, true);
                            // console.log();
                            //  var boundaryCordinates = response.features[0].geometry.coordinates[0];
                            // MapController.fetchBuildingsViaOverpassAPI(map, boundaryCordinates);
                        }
                    }


                } catch (e) {
                    console.log(e);
                }
            }).always(function (xhr) {
                Common.hideSpinner($("#map"));
            }).fail(function (xhr) {

            });
        }
        ,

        saveBoundary: function (boundary, type) {
            var requestUrl = null;
            var payLoad = {};
            var user = Common.getLoggedInUser();
            switch (type) {
                case Common.constants().ASSEMBLY:
                    var assembly = $("#app-map-container").data('assembly');
                    requestUrl = Common.endpoints().EP_ASSEMBLIES + "/" + assembly.id + "/geofence";
                    payLoad = {boundary: boundary, updatedBy: user.id};
                    break;
                case Common.constants().DIVISION:
                    var division = $(".app-map-container").data('division');
                    requestUrl = Common.endpoints().EP_DIVISIONS + "/" + division.id + "/geofence";
                    payLoad = {geofence: boundary, updatedBy: user.id}
                    break;
                case Common.constants().BLOCK:
                    var block = $(".block-map-container").data('block');
                     requestUrl=Common.endpoints().EP_BLOCKS + "/" + block.id + "/geofence";
                    payLoad = {geofence: boundary, updatedBy: user.id}
                    break;
            }

            $.ajax({
                type: "PUT",
                url: requestUrl,
                data: JSON.stringify(payLoad),
                contentType: "application/json; charset=utf-8",
                dataType: "json",
                headers: {
                    'Accept': 'application/json',
                    // 'Content-Type': 'text/plain',
                    'group-id': user.groupId
                },
                beforeSend: function (xhr) {
                    //xhr.setRequestHeader('group-id', assembly.code);

                    Common.showSpinner($(".map-header"), "Saving Boundary Cordinates...");
                    return true;
                }
            }).done(function (response) {
                Common.hideSpinner($(".map-header"));
                try {
                    if (response.code === "00") {
                        Common.showMessage(response.message);
                    }

                } catch (e) {
                    console.log(e);
                }
            }).always(function (xhr) {
                Common.hideSpinner($(".map-header"));
            }).fail(function (xhr) {

            });
        }
        ,
        replace: function (str, find, replace_with) {
            while (str.indexOf(find) !== -1) {
                from = str.indexOf(find);
                to = from + find.length;
                str = str.substr(0, from) + replace_with + str.substr(to, str.length - to);
            }
            return str;
        }
        ,
        getBuildings: function (map, boundaryCoords) {
            var coordinates = MapController.toOverPassCords(boundaryCoords);// MapController.replace(boundaryCoordinates, ",", " ");
            $.ajax({
                type: "GET",
                url: 'https://overpass-api.de/api/interpreter?data=[out:json][timeout:30];(way[building=yes](poly: "' + coordinates + '");node(w););out geom;>;',
                dataType: 'json',
                beforeSend: function () {
                    Common.showSpinner($("#map"), "Loading Building Polygons....");
                    return true;
                }
            }).done(function (response) {
                Common.hideSpinner($("#map"));
                var result = osmtogeojson(response);
                boundaryLayer.addData(result);
                // L.geoJson(result, null).addTo(map);
            }).always(function (xhr) {
                Common.hideSpinner($("#map"));
            }).fail(function (xhr) {
                Common.hideSpinner($("#map"));
                alert(xhr.responseText);
            });
        }
        ,
        fetchBuildingsViaOverpassAPI: function (map, boundaryCoordinates) {
            var coordinates = MapController.toOverPassCords(boundaryCoordinates);// MapController.replace(boundaryCoordinates, ",", " ");
            var mapUI=$(".map-header-layer");
            var options = {
                debug: false,
                minZoom: 15,
                endPoint: 'https://overpass-api.de/api/',
                query: '(way[building=yes](poly: "' + coordinates + '");node(w););out geom;>;',
                loadedBounds: [],
                // markerIcon: L.Icon(),
                timeout: 30 * 1000, // Milliseconds
                retryOnTimeout: false,
                noInitialRequest: false,
                minZoomIndicatorEnabled: true,
                minZoomIndicatorOptions: {
                    position: 'topright',
                    minZoomMessageNoLayer: 'No layer assigned',
                    minZoomMessage: 'Current zoom level: CURRENTZOOM - All data at level: MINZOOMLEVEL'
                },
                beforeRequest: function () {
                    Common.showSpinner(mapUI, "Loading Building Polygons....");
                    return true;
                },
                afterRequest: function () {
                    Common.hideSpinner(mapUI);
                },
                onSuccess: function (data) {
                    var result = osmtogeojson(data);
                    L.geoJson(result, null).addTo(map);
                    workingLayer.addData(result);
                    console.log(workingLayer.toGeoJSON());
                },
                onError: function (xhr) {
                    Common.hideSpinner(mapUI);
                },
                onTimeout: function (xhr) {
                    Common.hideSpinner(mapUI);
                },
            };

            var opl = new L.OverPassLayer(options);
            map.addLayer(opl);
        }
        ,
        toWKT: function (layer) {
            var lng, lat, coords = [];
            if (layer instanceof L.Polygon || layer instanceof L.Polyline) {
                var latlngs = layer.getLatLngs();
                for (var i = 0; i < latlngs.length; i++) {
                    latlngs[i]
                    coords.push(latlngs[i].lng + " " + latlngs[i].lat);
                    if (i === 0) {
                        lng = latlngs[i].lng;
                        lat = latlngs[i].lat;
                    }
                }
                ;
                if (layer instanceof L.Polygon) {
                    return "POLYGON((" + coords.join(",") + "," + lng + " " + lat + "))";
                } else if (layer instanceof L.Polyline) {
                    return "LINESTRING(" + coords.join(",") + ")";
                }
            } else if (layer instanceof L.Marker) {
                return "POINT(" + layer.getLatLng().lng + " " + layer.getLatLng().lat + ")";
            }
        }

    }


}
();
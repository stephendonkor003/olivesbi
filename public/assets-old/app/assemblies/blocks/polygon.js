/**
 * Created by degre on 5/7/2021.
 */
/**
 * Created by degre on 1/16/2021.
 */

var BLK = function () {
    window.boundaryLayer = null;
    window.assemblyLayer = null;
    window.divisionLayer = null;
    window.blockLayer = null;
    window.blockGeoJsonLayers = [];
    var mapCanvas = 'map';
    var mapContainer = $("#app-map-container");
    return {
        init: function () {
            //DVN.initMap();
            //MapController.initGeoman('map', Common.constants().ASSEMBLY);
            BLK.initMap();

        },
        getBlock: function () {
            return $("#app-map-container").data('block');
        },
        initMap: function () {
            const map = L.map(mapCanvas).setView([7.95277, -1.03071], 18);
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
            BLK.initGeocodeControls(map);
            BLK.loadBlock(map);
            /// BLK.loadBoundary(map, BLK.getBlock().geofence1, true, false);
            $(document).on('click', '#btn-geocode-search', function (e) {
                e.preventDefault();
                MapController.searchNominatim(map);
            });
            L.easyButton('icofont icofont-save', function (btn, map) {
                if (workingLayer !== null) {
                    Common.confirmAction("Confirm Saving Geo Data", function () {
                        BLK.saveBoundary(BLK.getBlock().id, workingLayer.toGeoJSON());
                    }, null);
                }
            }).addTo(map);
        },
        initGeocodeControls: function (map) {
            var content = '<div class="input-group">' +
                '    <input type="text" class="form-control input-sm" placeholder="Enter Address" id="input-geocode-search" value="Adenta,Accra">' +
                '    <span class="input-group-btn">' +
                '        <button  id="btn-geocode-search" class="btn btn-primary btn-sm" type="button" title="Search An Address"><i class="icofont icofont-search"></i> Search</button>' +
                '    </span>' +
                '</div>';
            L.control.slideMenu(content, {
                position: 'topright',
                width: '600px',
                height: '50px',
                menuposition: 'topright',
                icon: 'fa-search'

            }).addTo(map);
        },
        loadBoundary: function (map, boundary, opic, loadBlocks) {
            var boundaryGeoJson = null;
            var hasGeoJson = false;
            if (boundary !== null) {
                try {
                    console.log(boundary);
                    boundaryGeoJson = GJsonUtil.fixGeoJson(boundary);
                } catch (e) {
                    console.log(e);
                    boundaryGeoJson = null;
                }

                if (boundaryGeoJson !== null) {
                    if (boundaryGeoJson.hasOwnProperty("type")) {
                        if (boundaryGeoJson.features.length > 0) {
                            hasGeoJson = true;
                            MapController.loadGeoJsonData(boundaryGeoJson, map, loadBlocks, opic);
                            if (loadBlocks && (boundaryGeoJson.features.length === 1)) {
                                MapController.fetchBuildingsViaOverpassAPI(map, boundaryGeoJson.features[0].geometry.coordinates[0]);// coords.join(' '));
                            }
                        }
                    }
                }

            }
            if (!hasGeoJson) {
                MapController.fireDrawable(map);
            }
        },
        saveBoundary: function (id, boundary) {
            var requestUrl = mapContainer.attr('data-boundary-action');
            var payLoad = {blockId: id, boundary: boundary, _token: mapContainer.attr('data-token')};
            $.ajax({
                type: "POST",
                url: requestUrl,
                data: JSON.stringify(payLoad),
                contentType: "application/json; charset=utf-8",
                dataType: "json",
                beforeSend: function (xhr) {
                    Common.showSpinner(mapContainer, "Saving Boundary Cordinates...");
                    return true;
                }
            }).done(function (response) {
                Common.hideSpinner(mapContainer);
                try {
                    if (response.code === "00") {
                        Common.showMessage(response.message);
                    }
                } catch (e) {
                    console.log(e);
                }
            }).always(function (xhr) {
                Common.hideSpinner(mapContainer);
            }).fail(function (xhr) {

            });
        },

        createGeojson: function (polyJson) {
            var polygon = polyJson.hasOwnProperty('building') ? polyJson.building : {};
            var type = polygon.hasOwnProperty('type') ? polygon.type : 'Polygon';
            var coordinates = polygon.hasOwnProperty('coordinates') ? polygon.coordinates : [];
            var buildingId = polygon.hasOwnProperty('buildingId') ? polygon.buildingId : null;
            var polyProps = {blockId: polyJson.blockId, buildingId: buildingId, id: polyJson.id};
            var geojson = {};
            geojson['type'] = 'Feature';
            geojson['properties'] = polyProps;// Common.generateUniqueId()};
            geojson['geometry'] = {};
            geojson['geometry']['type'] = polygon.type;
            geojson['geometry']['coordinates'] = polygon.coordinates;
            return geojson;
        },
        setupGeojson: function (map, blockBoundary, polygons) {
            //console.log(blockBoundary);
            var boundaryGeoJson = null;
            if (blockBoundary !== null || blockBoundary.length > 0) {
                try {
                    boundaryGeoJson = GJsonUtil.fixGeoJson(blockBoundary);
                } catch (e) {
                    boundaryGeoJson = null;
                }
            }

            //console.log(boundaryGeoJson);
            $.each(polygons, function (index, polygon) {
                var geojson = BLK.createGeojson(polygon);
                boundaryGeoJson.features.push(geojson);
            });
            if (boundaryGeoJson.features.length > 0) {
                BLK.loadPolygonGeoJsonData(boundaryGeoJson, map, true);
            }
            //  var coord=boundaryGeoJson.features[0].geometry.coordinates[0][0];
            // var lalo = L.GeoJSON.coordsToLatLng(coord);
            //   map.setView(lalo, 18);
            //  console.log(boundaryGeoJson);
        },
        loadBlock: function (map) {
            var requestUrl = mapContainer.attr('data-block-url');
            $.ajax({
                type: "GET",
                url: requestUrl,
                dataType: "json",
                beforeSend: function (xhr) {
                    Common.showSpinner(mapContainer, "Loading Block...");
                    return true;
                }
            }).done(function (response) {
                Common.hideSpinner(mapContainer);
                try {
                    if (response.code === "00") {
                        var blockBoundary = response.data.geofence1;
                        BLK.loadPolygons(map, blockBoundary);
                    }
                } catch (e) {
                    console.log(e);
                }
            }).always(function (xhr) {
                Common.hideSpinner(mapContainer);
            }).fail(function (xhr) {

            });
        },
        loadPolygons: function (map, blockBoundary) {
            var requestUrl = mapContainer.attr('data-polygon-url');
            $.ajax({
                type: "GET",
                url: requestUrl,
                dataType: "json",
                beforeSend: function (xhr) {
                    Common.showSpinner(mapContainer, "Loading Polgyons...");
                    return true;
                }
            }).done(function (response) {
                Common.hideSpinner(mapContainer);
                try {
                    if (response.code === "00") {
                        BLK.setupGeojson(map, blockBoundary, response.data);
                    }
                } catch (e) {
                    console.log(e);
                }
            }).always(function (xhr) {
                Common.hideSpinner(mapContainer);
            }).fail(function (xhr) {

            });
        },
        getColor: function (d) {
            return (d % 2 === 0) ? '#BD0026' : '#FD8D3C';
        },
        loadPolygonGeoJsonData: function (geoJsonData, map, opic) {
            if (blockLayer != null) {
                blockGeoJsonLayers = [];
                blockLayer.clearLayers();
            }
            blockLayer = L.geoJson(geoJsonData, {
                style: function (feature) {
                    return {
                        fillColor: BLK.getColor(feature.properties.id),
                        weight: 2,
                        opacity: 1,
                        color: 'white',
                        dashArray: '3',
                        fillOpacity: 0.7
                    };
                },
                pointToLayer: function (feature, latlng) {
                    if (feature.properties.radius) {
                        return new L.Circle(latlng, feature.properties.radius);
                    } else {
                        return new L.Marker(latlng);
                    }
                },
                onEachFeature: function (feature, layer) {
                    /*if(feature.properties.hasOwnProperty('name')){
                     layer.bindPopup(feature.properties.name);
                     }
                     */
                    layer.bindPopup(JSON.stringify(feature.properties));
                },
            });

            blockLayer.addTo(map);
            // blockLayer.pm.enable({
            //     allowSelfIntersection: false
            // });
            map.fitBounds(blockLayer.getBounds());
            map.setMaxZoom(21);
            blockLayer.on('pm:edit', function (e) {
                console.log(e);
                var layer = e.layer;
                console.log(layer);
            });

            BLK.initGeoEditor(map);
            /*var json = polygon.toGeoJSON();
             L.extend(json.properties, polygon.properties)*/

        },
        initGeoEditor: function (map) {
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
                if (blockLayer == null) {
                    blockLayer = L.geoJSON();
                }
                var lng, lat, coords = [];
                var layer = e.layer;
                if (layer instanceof L.Polygon) {
                    // structure the geojson object
                    var geojson = {};
                    geojson['type'] = 'Feature';
                    geojson['properties'] = {id: 123};// Common.generateUniqueId()};
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
                    blockLayer.addData(geojson);
                    console.log(blockLayer.toGeoJSON());

                } else if (layer instanceof L.Circle) {
                    var geojson = {};
                    geojson['type'] = 'Feature';
                    geojson['properties'] = {id: 123};// Common.generateUniqueId()};
                    geojson['geometry'] = {};
                    geojson['geometry']['type'] = "Point";
                    coords = [];
                    var latlng = layer.getLatLng();
                    geojson['geometry']['coordinates'] = [latlng.lng, latlng.lat];
                    ///console.log(JSON.stringify(geojson));
                    blockLayer.addData(geojson);
                }
                // boundaryLayer.addLayer(layer);
                /* var geojson = e.layer.toGeoJSON();
                 var wkt = Terraformer.WKT.convert(geojson.geometry);
                 console.log(wkt);
                 alert(wkt);*/
            });
        },

    };
}();

$(function () {
    BLK.init();
});
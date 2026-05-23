/**
 * Created by degre on 5/16/2021.
 */
/**
 * Created by degre on 5/16/2021.
 */


var IMap = function () {
    window.blockLayer = null;
    window.blockGeoJsonLayers = [];
    window.vectorGridLayer = null;
    window.vectorGridLayerGroup = L.layerGroup();
    var mapCanvas = 'map';
    var mapContainer = $("#mapwrap");
    var highlight = null;
    return {
        init: function () {
            IMap.initMap();
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
            IMap.loadMapData(map, 'blkply');
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
        setupGeojson: function (map, geoJsonData) {
            try {
                IMap.loadMapGeoJsonData(geoJsonData, map, true);
                // if (geoJsonData.features.length > 0) {
                //
                // }
            } catch (e) {
                console.log(e);
            }
        },

        initMapTypeMenu: function (map) {
            var slType = '<select id="sl-map-type" name="sl-map-type" class="form-control">';
            slType += '<option value="dcol">Data Collection</option>';
            slType += '<option value="bill">Bill Distribution</option>';
            slType += '<option value="paym">Payments</option>';
            slType += '</select>';
            var slDataType = '<select id="sl-data-type" name="sl-data-type" class="form-control">';
            slDataType += '<option value="blkply">Blocks And Polygons</option>';
            slDataType += '<option value="ply">Only Polygons</option>';
            slDataType += '</select>';
            // L.control.custom({
            //     position: 'topright',
            //     content: content
            // }).addTo(map);
            var content = '<br/><div class="row">' +
                '<div class="col-sm-6 form-group"><label>Map Data Type</label>' + slType + '</div>' +
                '<div class="col-sm-6 form-group"><label>Map Structure Type</label>' + slDataType + '</div>' +
                '</div>';
            L.control.slideMenu(content, {
                position: 'topright',
                width: '600px',
                height: '150px',
                menuposition: 'topright',
                icon: 'fa-map'

            }).addTo(map);
        },
        loadMapData: function (map, structureType) {
            var requestUrl = mapContainer.attr('data-url');
            if (structureType === 'ply') {
                requestUrl = mapContainer.attr('data-polygons-url');
            }
            $.ajax({
                type: "GET",
                url: requestUrl,
                dataType: "json",
                beforeSend: function (xhr) {
                    Common.showSpinner(mapContainer, "Loading Map Data...");
                    return true;
                }
            }).done(function (response) {
                Common.hideSpinner(mapContainer);
                if (response.code === "00") {
                    IMap.setupGeojson(map, response.data);
                }
            }).always(function (xhr) {
                Common.hideSpinner(mapContainer);
            }).fail(function (xhr) {

            });
        },
        getColor: function (props) {
            var dataType = $("#sl-map-type").val();
            var color = 'colr_' + dataType;
            if (props.hasOwnProperty('colr_blck')) {
                return "#" + props.colr_blck;
            } else {
                return "#" + props[color];
            }
        },
        getOpacity: function (prop) {
            if (prop.hasOwnProperty('colr_opac')) {
                return prop.colr_opac;
            }
            return 1;
        },
        clearHighlight: function () {
            if (highlight) {
                vectorGrid.resetFeatureStyle(highlight);
            }
            highlight = null;
        },
        bboxLeaflet: function (geojson) {
            var bb = turf.bbox(geojson);
            return [[bb[1], bb[0]], [bb[3], bb[2]]];
        },
        clearLayers: function (map) {
            var osmUrl = 'http://{s}.tile.osm.org/{z}/{x}/{y}.png'

            map.eachLayer(function (layer) {
                if (osmUrl !== layer._url) {
                    map.removeLayer(layer)
                }
                ;
            });
        },
        loadMapGeoJsonData: function (geoJsonData, map, opic) {
            Common.showSpinner(mapContainer, "Initializing...");
            vectorGridLayerGroup.clearLayers();
            L.vectorGrid.slicer(geoJsonData, {
                rendererFactory: L.canvas.tile, //L.svg.tile,
                maxZoom: 22,
                indexMaxZoom: 5,
                vectorTileLayerStyles: {
                    sliced: function (properties, zoom) {
                        return {
                            fillColor: IMap.getColor(properties),
                            stroke: true,
                            fill: true,
                            weight: 2,
                            //  opacity: IMap.getOpacity(properties),
                            color: 'white',
                            dashArray: '3',
                            fillOpacity: 0.7
                        };
                    }
                },
                interactive: true,
                getFeatureId: function (f) {
                    return f.properties.id;
                }
            }).on('click', function (e) {
                var properties = e.layer.properties;
                IMap.popUpOnClick(e, map);
                // L.popup()
                //     .setContent(JSON.stringify(properties))
                //     .setLatLng(e.latlng)
                //     .openOn(map);
                //
                // IMap.clearHighlight();
                // highlight = properties.id;
                // var style = {
                //     fillColor: IMap.getColor(properties),
                //     fillOpacity: 0.5,
                //     fillOpacity: 1,
                //     stroke: true,
                //     fill: true,
                //     color: 'red',
                //     opacity: 1,
                //     weight: 2
                // };
                //
                // vectorGrid.setFeatureStyle(properties.id, style);
            })//.on('load', IMap.onMapLoad)
                .addTo(vectorGridLayerGroup);
            map.fitBounds(IMap.bboxLeaflet(geoJsonData));
            Common.hideSpinner(mapContainer);

        },
        polygonInfoTable: function (polygons) {
            var tblBody = $("#tbl-body");
            tblBody.empty();
            $.each(polygons, function (index, ply) {
                // Create a hidden element
                tblBody.append('<tr><td>' + ply.entityOwnerID + '</td><td>' + ply.accountNo + '</td><td>' + ply.entityOwnerName + '</td><td>' + ply.entityOwnerPhone + '</td><td>' + ply.entityType + '</td><td>' + ply.infoType + '</td><td>' + ply.invoiceNo + '</td><td>' + ply.amtDue + '</td><td>' + ply.amtPaid + '</td></tr>')
            });
            if (polygons.length > 0) {
                $('#toolbar .hamburger').trigger('click');
            }
        },
        popUpOnClick: function (event, map) {
            var polygon = event.layer;
            var properties = polygon.properties;
            var table = "<table>";
            if (properties.hasOwnProperty('name')) {
                table += '<tr><td><i class="icofont icofont-info-circle"></i> ID </td><td>' + properties.id + '</td></tr>';
                table += '<tr><td><i class="icofont icofont-info-circle"></i> Name</td><td>' + properties.name + '</td></tr>';
                table += "</table>";
                L.popup()
                    .setContent(table)
                    .setLatLng(event.latlng)
                    .openOn(map);
            } else {
                var dataType = $("#sl-map-type").val();
                var requestUrl = mapContainer.attr('data-info-url') + "/" + dataType + "/" + properties.id;
                $.get(requestUrl, function (response) {
                        if (response.code === "00") {
                            try {
                                var data = response.data;
                                var prrCount = data.hasOwnProperty('prrCount') ? data.prrCount : 0;
                                var busCount = data.hasOwnProperty('busCount') ? data.busCount : 0;
                                var prrSum = data.hasOwnProperty('prrSum') ? data.prrSum : 0.0;
                                var busSum = data.hasOwnProperty('busSum') ? data.busSum : 0.00;
                                table += '<tr><td><i class="icofont icofont-info-circle"></i> Property Rate Total </td><td>' + prrCount + '</td></tr>';
                                table += '<tr><td><i class="icofont icofont-info-circle"></i> Property Rate Amount</td><td>' + prrSum + '</td></tr>';
                                table += '<tr><td><i class="icofont icofont-info-circle"></i> BoP Total </td><td>' + busCount + '</td></tr>';
                                table += '<tr><td><i class="icofont icofont-info-circle"></i> Bop Amount</td><td>' + busSum + '</td></tr>';
                                table += "</table>";
                                L.popup()
                                    .setContent(table)
                                    .setLatLng(event.latlng)
                                    .openOn(map);
                            } catch (e) {
                                console.log(e);
                            }
                        }
                    }
                    , 'json');
                requestUrl = mapContainer.attr('data-detail-url') + "/" + dataType + "/" + properties.id;
                $.get(requestUrl, function (response) {
                        if (response.code === "00") {
                            try {
                                IMap.polygonInfoTable(response.data);
                            } catch (e) {
                                console.log(e);
                            }
                        }
                    }
                    , 'json');
            }
        },
        onMapLoad: function (e) {
            setTimeout(function () {
                    Common.showSpinner(mapContainer, "Initializing...");
                    // Remove the old grid layer from the map
                    //IMap.vectorGrid.remove();
                    // Stop listening to the load event
                    e.target.off('load', IMap.onMapLoad);
                    // Save the new graphics layer into the member variable
                    IMap.vectorGrid = e.target;
                    Common.hideSpinner(mapContainer);
                },
                250);
        },
        addGeojsonData: function (map, data) {
            map.fitBounds(IMap.bboxLeaflet(data));
            var vectorGridLayer = L.vectorGrid.slicer(data, {
                rendererFactory: L.svg.tile,
                maxZoom: 20,
                interactive: true,
                vectorTileLayerStyles: {
                    sliced: function (properties, zoom) {
                        return {
                            fillColor: "yellow",
                            fillOpacity: 0.5,
                            stroke: true,
                            fill: true,
                            color: 'blue',
                            weight: 0.7,
                        }
                    }
                },
            })
                .addTo(map);
        }


    };
}();

$(function () {
    IMap.init();
});
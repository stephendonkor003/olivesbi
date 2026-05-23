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
    var jobForm = $("#job-form");
    return {
        init: function () {
            IMap.validateForm();
            IMap.initMap();
            $("#btn-save-action").on('click', function (e) {
                e.preventDefault();
                e.stopImmediatePropagation();
                IMap.allocateJob();
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
            IMap.loadMapData(map);
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


        loadMapData: function (map) {
            var requestUrl = mapContainer.attr('data-url');
            $.ajax({
                type: "GET",
                url: requestUrl,
                dataType: "json",
                beforeSend: function (xhr) {
                    Common.showSpinner(mapContainer, "Loading Blocks...");
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
            return "#" + props.colr_blck;
        },
        getOpacity: function (prop) {
            if (prop.hasOwnProperty('colr_opac')) {
                return prop.colr_opac;
            }
            return 1;
        },

        bboxLeaflet: function (geojson) {
            var bb = turf.bbox(geojson);
            return [[bb[1], bb[0]], [bb[3], bb[2]]];
        },
        blockInfoTable: function (blocks) {
            var tblBody = $("#job-body");
            tblBody.empty();
            $.each(blocks, function (index, block) {
                // Create a hidden element
                tblBody.append('<tr><td>' + block.jobName + '</td><td>' + block.block + '</td><td>' + block.assignedTo + '</td></tr>')
            });
            if (blocks.length > 0) {
                $('#toolbar .hamburger').trigger('click');
            }
        },
        removeIdList: function () {
            // Remove added elements
            $('input[name="blockId\[\]"]', jobForm).remove();
        },
        resetForm: function () {
            IMap.removeIdList();
            blockIds = [];
            jobForm.clearForm();
            jobForm.resetForm();
            $("#sl-blocks").text(blockIds.length);
        },
        validateForm: function () {
            Common.validateForm(jobForm, {
                jobId: 'required',
                agentId: 'required'
            }, {});
        },
        allocateJob: function () {
            if (blockIds.length > 0) {
                $.each(blockIds, function (index, blockId) {
                    // Create a hidden element
                    jobForm.append(
                        $('<input>')
                            .attr('type', 'hidden')
                            .attr('name', 'blockId[]')
                            .val(blockId)
                    );
                });

                var ajaxOptions = {
                    type: 'POST',
                    url: jobForm.attr('data-action'),
                    data: jobForm.serialize(),
                    dataType: "json",
                    beforeSend: function (xhr) {
                        if (jobForm.valid()) {
                            Common.showSpinner(jobForm, "Allocating Job...");
                            return true;
                        } else {
                            return false;
                        }

                    }
                };
                $.ajax(ajaxOptions).done(function (response) {
                    Common.hideSpinner(jobForm);
                    if (response.status === "ok" && response.code === "00") {
                        Common.showMessage(response.message);
                        IMap.resetForm();
                        window.location.reload();
                    } else {
                        Common.onError(response.message);
                    }
                }).always(function (xhr) {
                    Common.hideSpinner(jobForm);
                }).fail(function (xhr, textStatus, error) {
                    Common.hideSpinner(jobForm);
                    if (textStatus !== "canceled") {
                        Common.onError("Sorry An Error Occured Whiles Processing The Request. Kindly Try Again Later.");
                    }
                });
            } else {
                Common.onError('Please kindly select at least one business before you continue.')
            }
        },
        getFeatureStyle: function (feature) {
            return {
                fillColor: IMap.getColor(feature.properties),
                weight: 2,
                opacity: IMap.getOpacity(feature.properties),
                color: 'white',
                dashArray: '3',
                fillOpacity: 0.7
            };
        },
        loadMapGeoJsonData: function (geoJsonData, map, opic) {
            blockLayer = L.geoJson(geoJsonData, {
                style: function (feature) {
                    return {
                        fillColor: IMap.getColor(feature.properties),
                        weight: 2,
                        opacity: IMap.getOpacity(feature.properties),
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
                    layer.on('click', function (e) {
                        // e = event
                        IMap.popUpOnClick(e, map);

                    });
                    // layer.bindPopup(JSON.stringify(feature.properties));
                },
            });

            blockLayer.addTo(map);

            try {
                var geoList = new L.Control.GeoJSONSelector(blockLayer, {
                    zoomToLayer: true,
                    activeListFromLayer: false,
                    activeLayerFromList: false,
                    listOnlyVisibleLayers: true,
                    collapsed: false,
                    multiple: true,
                    style: function (feature) {
                        return {
                            fillColor: IMap.getColor(feature.properties),
                            weight: 2,
                            opacity: IMap.getOpacity(feature.properties),
                            color: 'white',
                            dashArray: '3',
                            fillOpacity: 0.7
                        };
                    },
                    activeStyle: function (feature) {
                        return {
                            fillColor: IMap.getColor(feature.properties),
                            weight: 2,
                            opacity: IMap.getOpacity(feature.properties),
                            color: 'white',
                            dashArray: '3',
                            fillOpacity: 0.7
                        };
                    }
                }).addTo(map);
                $('.geojson-list').css('display','none');
                geoList.on('selector:change', function (e) {
                    var selectedItemClass = e.layers[0].itemList.attributes[0].nodeValue;
                    if (selectedItemClass === 'geojson-list-item selected') {
                        blockIds.push(e.layers[0].feature.properties.id);
                    } else {
                        e.layers[0].setStyle(IMap.getFeatureStyle(e.layers[0].feature));
                        var Id = e.layers[0].feature.properties.id;
                        var filtered = blockIds.filter(function (value, index, arr) {
                            return value !== Id;
                        });
                        blockIds = filtered;
                    }

                    $("#sl-blocks").text(blockIds.length);

                    var jsonObj = $.parseJSON(JSON.stringify(e.layers[0].feature.properties));
                    var html = 'Selection:<br /><table border="1">';
                    $.each(jsonObj, function (key, value) {
                        html += '<tr>';
                        html += '<td>' + key.replace(":", " ") + '</td>';
                        html += '<td>' + value + '</td>';
                        html += '</tr>';
                    });
                    html += '</table>';

                    $('.selection').html(html);
                    var requestUrl = mapContainer.attr('data-allocations-url') + '/' + e.layers[0].feature.properties.id;
                    $.get(requestUrl, function (response) {
                            if (response.code === "00") {
                                try {
                                    IMap.blockInfoTable(response.data);
                                } catch (e) {
                                    console.log(e);
                                }
                            }
                        }
                        , 'json');
                });


                map.addControl(function () {
                    var c = new L.Control({position: 'bottomright'});
                    c.onAdd = function (map) {
                        return L.DomUtil.create('pre', 'selection');
                    };
                    return c;
                }());
                var searchControl = new L.Control.Search({
                    layer: blockLayer,
                    propertyName: 'name',
                    marker: false,
                    container:'block-search-control',
                    collapsed:false,
                   // position:'topleft',

                    moveToLocation: function(latlng, title, map) {
                        //map.fitBounds( latlng.layer.getBounds() );
                        var zoom = map.getBoundsZoom(latlng.layer.getBounds());
                        map.setView(latlng, zoom); // access the zoom
                    }
                });
                map.addControl(searchControl);  //inizialize search control
               $("#searchtext9").css("width", "80%");
            } catch (e) {

            }
        },
        popUpOnClick: function (event, map) {
            var polygon = event.target.feature;
            var properties = polygon.properties;
            var table = "<table>";
            table += '<tr><td><i class="icofont icofont-info-circle"></i> ID </td><td>' + properties.id + '</td></tr>';
            table += '<tr><td><i class="icofont icofont-info-circle"></i> Name</td><td>' + properties.name + '</td></tr>';
            table += "</table>";
            L.popup()
                .setContent(table)
                .setLatLng(event.latlng)
                .openOn(map);

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

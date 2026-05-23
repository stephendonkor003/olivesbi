/**
 * Created by degre on 5/16/2021.
 */


var IMap = function () {
    window.blockLayer = null;
    window.blockGeoJsonLayers = [];
    var mapCanvas = 'map';
    var mapContainer = $("#mapwrap");
    return {
        init: function () {
            IMap.initMap();
        },
        initMap: function () {
            const map = L.map(mapCanvas, {
                preferCanvas: true
            }).setView([7.95277, -1.03071], 18);
            L.tileLayer('https://{s}.tile.osm.org/{z}/{x}/{y}.png', {
                attribution: '&copy; <a href="https://osm.org/copyright">OpenStreetMap</a> contributors'
            }).addTo(map);
            L.control.layers(MapController.getBaseMaps()).addTo(map);
           // IMap.loadMapData(map, 'blkply');
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
                if (geoJsonData.features.length > 0) {
                    IMap.loadMapGeoJsonData(geoJsonData, map, true);
                }
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
        loadMapGeoJsonData: function (geoJsonData, map, opic) {
            if (blockLayer != null) {
                blockGeoJsonLayers = [];
                blockLayer.clearLayers();
            }
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

            // IMap.initGeoEditor(map);


        }


    };
}();

$(function () {
    IMap.init();
});
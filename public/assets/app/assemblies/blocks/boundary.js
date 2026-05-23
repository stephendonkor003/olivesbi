/**
 * Created by degre on 1/16/2021.
 */

var BLK = function () {
    window.boundaryLayer = null;
    window.assemblyLayer = null;
    window.divisionLayer = null;
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
           BLK.initGeocodeControls(map);
           BLK.loadBoundary(map,BLK.getBlock().geofence1, true, false);
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
        }

    };
}();

$(function () {
   BLK.init();
});
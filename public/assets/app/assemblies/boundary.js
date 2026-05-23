
var ASM = function () {
    window.boundaryLayer = null;
    window.assemblyLayer = null;
    window.divisionLayer = null;
    var mapCanvas = 'map';
    var mapContainer = $("#app-map-container");
    return {
        init: function () {
            //ASM.initMap();
            //MapController.initGeoman('map', Common.constants().ASSEMBLY);
            ASM.initMap();
        },
        getAssembly: function () {
            return $("#app-map-container").data('assembly');
        },
        initMap: function () {
            const map = L.map(mapCanvas).setView([7.95277, -1.03071], 8);

             L.tileLayer('https://tile.openstreetmap.org/{z}/{x}/{y}.png', {
                attribution: '&copy; <a href="https://www.openstreetmap.org/copyright">OpenStreetMap</a> contributors'
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

            ASM.initGeocodeControls(map);
            //ASM.loadAssemblyBoundary(map, ASM.getAssembly().boundary, true, false)

            // Event listener for when the user completes a drawing
            map.on('pm:create', function (event) {
                const workingLayer = event.layer;

                if (workingLayer !== null) { 
                    const geoJsonData = workingLayer.toGeoJSON();
                    const polygonCoordinates = geoJsonData.geometry.coordinates;
                    const geo_coordinate = JSON.stringify(polygonCoordinates);

                    $("input[name='geo_coordinate']").val(geo_coordinate);
                } else {
                    alert("Invalid input")
                }
            });

            $(document).on('click', '#btn-geocode-search', function (e) {
                e.preventDefault();
                MapController.searchNominatim(map);
            });
            L.easyButton('icofont icofont-save', function (btn, map) {
                if (workingLayer !== null) {
                    Common.confirmAction("Confirm Saving Geo Data", function () {
                        ASM.saveBoundary(ASM.getAssembly().id, workingLayer.toGeoJSON());
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
        loadAssemblyBoundary: function (map, boundary, opic, loadBlocks) {
            var boundaryGeoJson = null;
            var hasGeoJson = false;
            if (boundary !== null) {
                try {
                    console.log(boundary);
                    boundaryGeoJson = boundary;
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
            var payLoad = {assemblyId: id, boundary: boundary, _token: mapContainer.attr('data-token')};
            $.ajax({
                type: "POST",
                url: requestUrl,
                data: payLoad,
                // contentType: "application/json; charset=utf-8",
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
    ASM.init();
});
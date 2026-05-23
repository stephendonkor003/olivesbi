/**
 * Created by degre on 12/6/2017.
 */

$(function () {
    ReportMap.initReportMap();
    $('body').on('shown.bs.modal', function (e) {
        setTimeout(function () {
            reportMap.invalidateSize();
        }, 500);
    });

});
var ReportMap = function () {
    window.reportMarkers = {};
    window.reportRoute = [];
    window.reportMarkerLayer = new L.featureGroup();
    window.reportRouteLayer = new L.featureGroup();
    window.reportMap = null;

    return {
        initReportMap: function () {


            var basemapLayer = TileFactory.GoogleRoad(); //new L.TileLayer('http://{s}.tiles.mapbox.com/v3/github.map-xgq2svrz/{z}/{x}/{y}.png');


            reportMap = L.map('report-map', {
                center: [5.667789958361761, -0.011415481567382814],
                zoom: 2,
                maxZoom: 18,
                //minZoom: 8,
                layers: [basemapLayer]//, reportMarkerLayer, reportRouteLayer],
            });

        },
        getPlayBackOptions: function () {
            // =====================================================
            // =============== Playback ============================
            // =====================================================
            //
            var shipIcon = L.icon({
                iconUrl: 'ship-icon.png',
                iconSize: [7, 20], // size of the icon
                shadowSize: [0, 0], // size of the shadow
                iconAnchor: [3.5, 10], // point of the icon which will correspond to marker's location
                shadowAnchor: [0, 0], // the same for the shadow
                popupAnchor: [0, -10] // point from which the popup should open relative to the iconAnchor
            });
            // Playback options
            var playbackOptions = {
               playControl: true,
                dateControl: true,
                sliderControl: true,
                orientIcons: true,
                //     playControl: true,
                //     dateControl: true,
                //     sliderControl: true
                //
                // marker: function (featureData) {
                //     return {
                //         icon: shipIcon,
                //         // getPopup: function (feature) {
                //         //     return feature.properties.title;
                //         // }
                //     };
                // }
            };

            return playbackOptions;
        },
        loadPlayBack:function (data,deviceIds) {
            if (data) {
                var devicePositions = [];
                deviceIds.forEach(function (deviceId) {
                    devicePositions.push(ReportMap.filterDevicePostions(data, deviceId));
                });
                var playBackCordinates=[];
                var playBackTimes=[];
                data = devicePositions;

                for (i = 0; i < data[0].length; i++) {
                    var position = data[0][i];
                    playBackCordinates.push([position.longitude,position.latitude]);
                    var date=new Date(position.deviceTime);
                    var timestamp=date.getTime();//moment(position.deviceTime).format("X");
                    playBackTimes.push(timestamp);

                }
                $('#report-map-modal').modal("show");

                var playback = new L.Playback(reportMap,ReportMap.getPlayBack(playBackCordinates,playBackTimes), null, ReportMap.getPlayBackOptions());

            }
        },

        loadReport: function (data, deviceIds) {
            if (data) {
                var devicePositions = [];
                deviceIds.forEach(function (deviceId) {
                    devicePositions.push(ReportMap.filterDevicePostions(data, deviceId));
                });
                // this.addReportMarkers(store, data);
                // routeSource = this.getView().getRouteSource();
//console.log(devicePositions);
                reportRoute = [];
                reportMarkers = {};
                reportMarkerLayer.clearLayers();
                reportRouteLayer.clearLayers();
                data = devicePositions;

               // var wayPoints=[];
                var recordsTotal = data[0].length;
                for (i = 0; i < data[0].length; i++) {
                    //recordsTotal--;
                    // console.log(data[0]);
                    var position = data[0][i];
                   var marker = reportMarkers[position.deviceId];
                   var polyLine = reportRoute[position.deviceId];
                  var latlng = L.latLng(position.latitude, position.longitude);
                     // playBackCordinates.push([position.longitude,position.latitude]);
                    var date=new Date(position.deviceTime);
                       var timestamp=date.getTime();//moment(position.deviceTime).format("X");


                     //  playBackTimes.push(timestamp);
                    if (!marker) {
                        marker = L.marker(latlng);
                        reportMarkers[position.deviceId] = marker;
                        reportMarkerLayer.addLayer(marker);
                        var poly = L.polyline([], {
                            color: 'green'
                        });
                        poly.addLatLng(latlng);
                        reportRoute[position.deviceId] = poly;
                        reportRouteLayer.addLayer(poly).addTo(reportMap);
                        reportMap.fitBounds(reportMarkerLayer.getBounds());
                        //console.log(reportMap);
                    } else {
                        //marker.setLatLng(latlng).update();
                        polyLine.addLatLng(latlng);

                        // reportMap.fitBounds(reportRouteLayer.getBounds());
                        //alert('here');
                    }


                }
               // console.log(wayPoints);

                // Initialize playback
               // console.log(playBackTimes);
               $('#report-map-modal').modal("show");

                //var playback = new L.Playback(reportMap,ReportMap.getPlayBack(playBackCordinates,playBackTimes), null, ReportMap.getPlayBackOptions());


            }
        },
        getPlayBack: function (cordinates, times) {
            return {
                "type": "Feature",
                "geometry": {
                    "type": "MultiPoint",
                    "coordinates": cordinates
                },
                "properties": {
                    "time": times
                }
            };
        },
        addReportMarker: function (position) {
            var geometry, marker, style, latlng = L.latLng(position.latitude, position.longitude);

            //geometry = new ol.geom.Point(point);
            marker = L.marker(latlng);
            // marker.set('record', position);
            // style = this.getReportMarker(position.get('deviceId'), position.get('course'));
            // marker.setStyle(style);
            reportLayer.addLayer(marker);
            return marker;
        },

        addReportMarkers: function (deviceId, data) {
            var i;
            this.clearReport();
            for (i = 0; i < data.length; i++) {
                // if (store.showMarkers) {
                this.reportMarkers[deviceId] = ReportMap.addReportMarker(data[i]);
                //}
            }
            this.zoomToAllPositions(data);
        },
        filterDevicePostions: function (positions, id) {
            return positions.filter(
                function (position) {
                    return position.deviceId == id;
                }
            );
        },
    };
}();
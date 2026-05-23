/**
 * Created by degre on 12/1/2017.
 */
var MarkerController = function () {
    window.latestMarkers = {};
    window.reportMarkers = {};
    window.accuracyCircles = {};
    window.liveRoutes = {};
    window.liveRouteLength = 10;// Traccar.app.getAttributePreference('web.liveRouteLength', 10);
    window.selectZoom = 0;//Traccar.app.getAttributePreference('web.selectZoom', 0);
   // this.map = BaseMap.getMap();

    return {
        init: function () {

        },

        removeDevice: function (devices) {
            var i, deviceId, markersLayerGroup;
            if (!Ext.isArray(devices)) {
                data = [data];
            }

            markersLayerGroup = BaseMap.getMarkersLayer();

            for (i = 0; i < devices.length; i++) {
                deviceId = devices[i].id;
                if (this.latestMarkers[deviceId]) {
                    if (MarkerController.getMarkerFromLayer(markersLayerGroup, this.latestMarkers[deviceId].getId())) {
                        markersLayerGroup.removeLayer(this.latestMarkers[deviceId]);
                    }
                    delete this.latestMarkers[deviceId];
                }
                // if (this.accuracyCircles[deviceId]) {
                //     if (markersSource.getFeatureById(this.accuracyCircles[deviceId].getId())) {
                //         markersSource.removeFeature(this.accuracyCircles[deviceId]);
                //     }
                //     delete this.accuracyCircles[deviceId];
                // }
                if (this.liveRoutes[deviceId]) {
                    if (this.getMarkerFromLayer(markersLayerGroup, this.liveRoutes[deviceId].id)) {
                        markersLayerGroup.removeLayer(this.liveRoutes[deviceId]);

                    }
                    delete this.liveRoutes[deviceId];
                }
            }
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
        }
        //};
    }
}();
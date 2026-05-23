/**
 * Created by degre on 12/1/2017.
 */

var TileFactory = function () {


    return {
        GoogleRoad: function () {
            return L.tileLayer('https://mt0.google.com/vt/lyrs=m&hl=en&x={x}&y={y}&z={z}&s=Ga', {
                attribution: '',
                maxZoom: StyleFactory.mapMaxZoom,
                minZoom: 2,
                maxNativeZoom: StyleFactory.mapMaxZoom

            });
        },
        GoogleSatelite: function () {
            return L.tileLayer('https://mt0.google.com/vt/lyrs=s&hl=en&x={x}&y={y}&z={z}&s=Ga', {
                attribution: '',
                maxZoom: StyleFactory.mapMaxZoom,
                minZoom: 2,
                maxNativeZoom: StyleFactory.mapMaxZoom
            });
        },
        GoogleHybrid: function () {
            return L.tileLayer('http://{s}.google.com/vt/lyrs=s,h&x={x}&y={y}&z={z}', {
                maxZoom: StyleFactory.mapMaxZoom,
                minZoom: 2,
                maxNativeZoom: StyleFactory.mapMaxZoom,
                subdomains: ['mt0', 'mt1', 'mt2', 'mt3']
            });
        },
        GoogleTerrain: function () {
            return L.tileLayer('http://{s}.google.com/vt/lyrs=p&x={x}&y={y}&z={z}', {
                maxZoom: StyleFactory.mapMaxZoom,
                minZoom: 2,
                maxNativeZoom: StyleFactory.mapMaxZoom,
                subdomains: ['mt0', 'mt1', 'mt2', 'mt3']
            });
        },

        OpenStreetMap: function () {
            return L.tileLayer('http://{s}.tile.openstreetmap.org/{z}/{x}/{y}.png', {
                attribution: 'Map data &copy; <a href="http://openstreetmap.org">OpenStreetMap</a>',
                maxZoom: StyleFactory.mapMaxZoom,
                minZoom: 2,
                maxNativeZoom: StyleFactory.mapMaxZoom

            });
        },
        openCycleMap:function(){
            return L.tileLayer('http://{s}.tile.opencyclemap.org/{z}/{x}/{y}.png', {
                attribution: 'Map data &copy; <a href="http://opencyclemap.org">OpenCycleMap</a>',
                maxZoom: StyleFactory.mapMaxZoom,
                minZoom: 2,
                maxNativeZoom: StyleFactory.mapMaxZoom

            });
        },
        osmBuilding:function(){
            // return new L.TileLayer('https://api.mapbox.com/styles/v1/osmbuildings/cjt9gq35s09051fo7urho3m0f/tiles/256/{z}/{x}/{y}@2x?access_token=pk.eyJ1Ijoib3NtYnVpbGRpbmdzIiwiYSI6IjNldU0tNDAifQ.c5EU_3V8b87xO24tuWil0w', {
            //     attribution: '© Map <a href="https://mapbox.com">Mapbox</a>',
            //     maxZoom: 18,
            //     maxNativeZoom: 20
            // });
         return  L.tileLayer('https://{s}.tiles.mapbox.com/v3/pk.eyJ1Ijoib3NtYnVpbGRpbmdzIiwiYSI6IjNldU0tNDAifQ.c5EU_3V8b87xO24tuWil0w/{z}/{x}/{y}.png', {
                attribution: '© Map tiles <a href="https://mapbox.com">Mapbox</a>',
                maxZoom: 18,
                maxNativeZoom: 20
            });
        },
        ArcGis: function () {
            return L.tileLayer('https://services.arcgisonline.com/ArcGIS/rest/services/World_Topo_Map/MapServer/tile/{z}/{y}/{x}.png', {
                attribution: '',
                maxZoom: StyleFactory.mapMaxZoom,
                minZoom: 2,
                maxNativeZoom: StyleFactory.mapMaxZoom
            });
        },
        ArcGisImagery: function () {
            var mapLink =
                '<a href="http://www.esri.com/">Esri</a>';
            var wholink =
                'i-cubed, USDA, USGS, AEX, GeoEye, Getmapping, Aerogrid, IGN, IGP, UPR-EGP, and the GIS User Community';
           return  L.tileLayer(
                'http://server.arcgisonline.com/ArcGIS/rest/services/World_Imagery/MapServer/tile/{z}/{y}/{x}', {
                    attribution: '&copy; '+mapLink+', '+wholink,
                    maxZoom: 18,
                });

        },
        WikimediaMap: function () {
            return L.tileLayer('https://maps.wikimedia.org/osm-intl/{z}/{x}/{y}.png', {
                attribution: '',
                maxZoom: StyleFactory.mapMaxZoom,
                minZoom: 2,
                maxNativeZoom: StyleFactory.mapMaxZoom
            });
        },
        googleTraffic:function () {
           return L.tileLayer('https://{s}.google.com/vt/lyrs=m@221097413,traffic&x={x}&y={y}&z={z}', {
                maxZoom: 20,
                minZoom: 2,
                subdomains: ['mt0', 'mt1', 'mt2', 'mt3'],
            });
        }
    };
}();
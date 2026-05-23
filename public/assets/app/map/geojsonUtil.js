/**
 * Created by degre on 4/24/2021.
 */

var GJsonUtil = function () {

    function rewind(gj, outer) {
        switch ((gj && gj.type) || null) {
            case 'FeatureCollection':
                gj.features = gj.features.map(curryOuter(rewind, outer));
                return gj;
            case 'Feature':
                gj.geometry = rewind(gj.geometry, outer);
                return gj;
            case 'Polygon':
            case 'MultiPolygon':
                return correct(gj, outer);
            default:
                return gj;
        }
    }

    function curryOuter(a, b) {
        return function (_) {
            return a(_, b);
        };
    }

    function correct(_, outer) {
        if (_.type === 'Polygon') {
            _.coordinates = correctRings(_.coordinates, outer);
        } else if (_.type === 'MultiPolygon') {
            _.coordinates = _.coordinates.map(curryOuter(correctRings, outer));
        }
        return _;
    }

    function correctRings(_, outer) {
        outer = !!outer;
        _[0] = wind(_[0], !outer);
        for (var i = 1; i < _.length; i++) {
            _[i] = wind(_[i], outer);
        }
        return _;
    }

    function wind(_, dir) {
        return cw(_) === dir ? _ : _.reverse();
    }

    function cw(_) {
        return ringArea(_) >= 0;
    }

    function geometry(_) {
        if (_.type === 'Polygon') return polygonArea(_.coordinates);
        else if (_.type === 'MultiPolygon') {
            var area = 0;
            for (var i = 0; i < _.coordinates.length; i++) {
                area += polygonArea(_.coordinates[i]);
            }
            return area;
        } else {
            return null;
        }
    }

    function polygonArea(coords) {
        var area = 0;
        if (coords && coords.length > 0) {
            area += Math.abs(ringArea(coords[0]));
            for (var i = 1; i < coords.length; i++) {
                area -= Math.abs(ringArea(coords[i]));
            }
        }
        return area;
    }

    function ringArea(coords) {
        var area = 0;

        if (coords.length > 2) {
            var p1, p2;
            for (var i = 0; i < coords.length - 1; i++) {
                p1 = coords[i];
                p2 = coords[i + 1];
                area += rad(p2[0] - p1[0]) * (2 + Math.sin(rad(p1[1])) + Math.sin(rad(p2[1])));
            }

            area = area * 6378137 * 6378137 / 2;
        }

        return area;
    }

    function rad(_) {
        return _ * Math.PI / 180;
    }

    return {

        fixGeoJson: function (geoJson) {
            // console.log(geojsonhint.hint(jQuery(this).val()));
            var geojsonData = null;
            var fixedGeoJSON = false;
            var errorArray = geojsonhint.hint(geoJson);
            if (errorArray.length > 0 && errorArray[0].message !== 'Polygons and MultiPolygons should follow the right-hand rule') {
                fixedGeoJSON = false;
                var msg = geojsonhint.hint(jQuery(this).val())[0].message;
            } else {
                fixedGeoJSON = true;
                var geojson = JSON.parse(geoJson);
                geojsonData = rewind(geojson, true);
                //jQuery('.geojson-after').val(JSON.stringify(geojson, null, 2));
            }
            return fixedGeoJSON ? geojsonData : geoJson;
        }
    };
}();
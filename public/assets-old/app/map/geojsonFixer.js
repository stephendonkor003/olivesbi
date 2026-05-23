/**
 * Created by degre on 4/24/2021.
 */

var GeoJsonUtil = function () {

    return {
        rewind: function (gj, outer) {
            switch ((gj && gj.type) || null) {
                case 'FeatureCollection':
                    gj.features = gj.features.map(GeoJsonUtil.curryOuter(GeoJsonUtil.rewind, outer));
                    return gj;
                case 'Feature':
                    gj.geometry = GeoJsonUtil.rewind(gj.geometry, outer);
                    return gj;
                case 'Polygon':
                case 'MultiPolygon':
                    return GeoJsonUtil.correct(gj, outer);
                default:
                    return gj;
            }
        },
        curryOuter: function (a, b) {
            return function (_) {
                return a(_, b);
            };
        },
        correct: function (_, outer) {
            if (_.type === 'Polygon') {
                _.coordinates = GeoJsonUtil.correctRings(_.coordinates, outer);
            } else if (_.type === 'MultiPolygon') {
                _.coordinates = _.coordinates.map(GeoJsonUtil.curryOuter(GeoJsonUtil.correctRings, outer));
            }
            return _;
        },
        correctRings: function (_, outer) {
            outer = !!outer;
            _[0] = GeoJsonUtil.wind(_[0], !outer);
            for (var i = 1; i < _.length; i++) {
                _[i] = GeoJsonUtil.wind(_[i], outer);
            }
            return _;
        }
        ,
        wind: function (_, dir) {
            return GeoJsonUtil.cw(_) === dir ? _ : _.reverse();
        }
        ,
        cw: function (_) {
            return GeoJsonUtil.ringArea(_) >= 0;
        },
        geometry: function (_) {
            if (_.type === 'Polygon') return GeoJsonUtil.polygonArea(_.coordinates);
            else if (_.type === 'MultiPolygon') {
                var area = 0;
                for (var i = 0; i < _.coordinates.length; i++) {
                    area += GeoJsonUtil.polygonArea(_.coordinates[i]);
                }
                return area;
            } else {
                return null;
            }
        },

        polygonArea: function (coords) {
            var area = 0;
            if (coords && coords.length > 0) {
                area += Math.abs(GeoJsonUtil.ringArea(coords[0]));
                for (var i = 1; i < coords.length; i++) {
                    area -= Math.abs(GeoJsonUtil.ringArea(coords[i]));
                }
            }
            return area;
        },

        ringArea: function (coords) {
            var area = 0;

            if (coords.length > 2) {
                var p1, p2;
                for (var i = 0; i < coords.length - 1; i++) {
                    p1 = coords[i];
                    p2 = coords[i + 1];
                    area += GeoJsonUtil.rad(p2[0] - p1[0]) * (2 + Math.sin(GeoJsonUtil.rad(p1[1])) + Math.sin(GeoJsonUtil.rad(p2[1])));
                }

                area = area * 6378137 * 6378137 / 2;
            }

            return area;
        }
        ,
        rad: function (_) {
            return _ * Math.PI / 180;
        }
    };
}();
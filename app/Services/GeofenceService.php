<?php

namespace App\Services;

class GeofenceService
{
    /**
     * Validate if a block's geofence is contained within a division's geofence
     *
     * @param  array|string  $blockGeofence  The block's geofence (GeoJSON FeatureCollection)
     * @param  array|string  $divisionGeofence  The division's geofence (GeoJSON FeatureCollection)
     * @return bool True if block is contained within division
     */
    public function isBlockContainedInDivision($blockGeofence, $divisionGeofence): bool
    {
        // Parse geofences if they are strings
        if (is_string($blockGeofence)) {
            $blockGeofence = json_decode($blockGeofence, true);
        }
        if (is_string($divisionGeofence)) {
            $divisionGeofence = json_decode($divisionGeofence, true);
        }

        // Validate JSON structure
        if (! $this->isValidGeoJSON($blockGeofence) || ! $this->isValidGeoJSON($divisionGeofence)) {
            return false;
        }

        // Extract coordinates from GeoJSON
        $blockCoordinates = $this->extractCoordinates($blockGeofence);
        $divisionCoordinates = $this->extractCoordinates($divisionGeofence);

        if (empty($blockCoordinates) || empty($divisionCoordinates)) {
            return false;
        }

        // Check if all block polygons are contained in at least one division polygon
        foreach ($blockCoordinates as $blockPolygon) {
            $isContained = false;

            foreach ($divisionCoordinates as $divisionPolygon) {
                if ($this->isPolygonContainedInPolygon($blockPolygon, $divisionPolygon)) {
                    $isContained = true;
                    break;
                }
            }

            if (! $isContained) {
                return false;
            }
        }

        return true;
    }

    /**
     * Validate if GeoJSON structure is valid
     *
     * @param  array  $geojson
     */
    private function isValidGeoJSON($geojson): bool
    {
        if (! is_array($geojson)) {
            return false;
        }

        if (! isset($geojson['type']) || $geojson['type'] !== 'FeatureCollection') {
            return false;
        }

        if (! isset($geojson['features']) || ! is_array($geojson['features'])) {
            return false;
        }

        return true;
    }

    /**
     * Extract coordinates from GeoJSON FeatureCollection
     *
     * @param  array  $geojson
     * @return array Array of polygon coordinate arrays
     */
    private function extractCoordinates($geojson): array
    {
        $polygons = [];

        foreach ($geojson['features'] as $feature) {
            if (! isset($feature['geometry']) || ! isset($feature['geometry']['coordinates'])) {
                continue;
            }

            $geometry = $feature['geometry'];
            $type = $geometry['type'] ?? null;

            if ($type === 'MultiPolygon') {
                // MultiPolygon: array of polygons
                foreach ($geometry['coordinates'] as $polygon) {
                    if (! empty($polygon[0])) {
                        $polygons[] = $polygon[0]; // Get outer ring
                    }
                }
            } elseif ($type === 'Polygon') {
                // Single Polygon: array of rings
                if (! empty($geometry['coordinates'][0])) {
                    $polygons[] = $geometry['coordinates'][0]; // Get outer ring
                }
            }
        }

        return $polygons;
    }

    /**
     * Check if polygon A is contained within polygon B
     * All vertices of polygon A must be inside polygon B
     *
     * @param  array  $polygonA  The polygon to check (array of [lng, lat] coordinates)
     * @param  array  $polygonB  The containing polygon (array of [lng, lat] coordinates)
     */
    private function isPolygonContainedInPolygon($polygonA, $polygonB): bool
    {
        // Check if all vertices of polygon A are inside polygon B
        foreach ($polygonA as $point) {
            if (! $this->isPointInPolygon($point, $polygonB)) {
                return false;
            }
        }

        return true;
    }

    /**
     * Check if a point is inside a polygon using ray casting algorithm
     *
     * @param  array  $point  [longitude, latitude]
     * @param  array  $polygon  Array of [longitude, latitude] coordinates
     */
    private function isPointInPolygon($point, $polygon): bool
    {
        $x = $point[0]; // longitude
        $y = $point[1]; // latitude

        $inside = false;
        $vertexCount = count($polygon);

        for ($i = 0, $j = $vertexCount - 1; $i < $vertexCount; $j = $i++) {
            $xi = $polygon[$i][0];
            $yi = $polygon[$i][1];
            $xj = $polygon[$j][0];
            $yj = $polygon[$j][1];

            // Ray casting algorithm
            $intersect = (($yi > $y) != ($yj > $y))
                && ($x < ($xj - $xi) * ($y - $yi) / ($yj - $yi) + $xi);

            if ($intersect) {
                $inside = ! $inside;
            }
        }

        return $inside;
    }

    /**
     * Get human-readable error message for validation failure
     */
    public function getValidationErrorMessage(): string
    {
        return 'The block geofence must be fully contained within the selected division\'s geofence boundary.';
    }
}

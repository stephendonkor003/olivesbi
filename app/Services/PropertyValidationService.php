<?php

namespace App\Services;

use App\Models\Assembly;
use App\Models\Block;
use App\Models\Division;
use App\Models\ValuationList;

class PropertyValidationService
{
    protected $geofenceService;

    public function __construct()
    {
        $this->geofenceService = new GeofenceService;
    }

    /**
     * Validate that a rated property belongs to the correct administrative areas
     *
     * @return array ['valid' => bool, 'errors' => array]
     */
    public function validateRatedPropertyAssignment(
        ?string $valuationNumber,
        string $assemblyCode,
        string $divisionId,
        string $blockId,
        float $latitude,
        float $longitude
    ): array {
        $errors = [];

        // Check if valuation number exists
        if (empty($valuationNumber)) {
            $errors[] = 'Valuation number is required for rated properties.';

            return ['valid' => false, 'errors' => $errors];
        }

        // Fetch the valuation record
        $valuationProperty = ValuationList::where('Valuation_Number', $valuationNumber)->first();

        if (! $valuationProperty) {
            $errors[] = 'The selected valuation number does not exist in the database.';

            return ['valid' => false, 'errors' => $errors];
        }

        // Validate assembly match
        if (! $this->validateAssemblyMatch($valuationProperty, $assemblyCode)) {
            $errors[] = 'The selected property does not belong to the selected assembly.';
        }

        // Validate that coordinates fall within division geofence
        if (! $this->validateCoordinatesInDivision($latitude, $longitude, $divisionId)) {
            $errors[] = 'The property coordinates do not fall within the selected division boundary.';
        }

        // Validate that coordinates fall within block geofence
        if (! $this->validateCoordinatesInBlock($latitude, $longitude, $blockId)) {
            $errors[] = 'The property coordinates do not fall within the selected block boundary.';
        }

        return [
            'valid' => empty($errors),
            'errors' => $errors,
        ];
    }

    /**
     * Validate that coordinates fall within a block's geofence
     */
    public function validateCoordinatesInBlock(float $latitude, float $longitude, string $blockId): bool
    {
        $block = Block::find($blockId);

        if (! $block || ! $block->geofence) {
            return false;
        }

        return $this->isPointInGeofence($latitude, $longitude, $block->geofence);
    }

    /**
     * Validate that coordinates fall within a division's geofence
     */
    public function validateCoordinatesInDivision(float $latitude, float $longitude, string $divisionId): bool
    {
        $division = Division::find($divisionId);

        if (! $division || ! $division->geofence) {
            return false;
        }

        return $this->isPointInGeofence($latitude, $longitude, $division->geofence);
    }

    /**
     * Check if a point (latitude, longitude) falls within a geofence
     *
     * @param  array|string  $geofence  GeoJSON FeatureCollection
     */
    private function isPointInGeofence(float $latitude, float $longitude, $geofence): bool
    {
        // Parse geofence if it's a string
        if (is_string($geofence)) {
            $geofence = json_decode($geofence, true);
        }

        // Basic validation of GeoJSON structure
        if (! is_array($geofence) || ! isset($geofence['type']) || $geofence['type'] !== 'FeatureCollection') {
            return false;
        }

        if (! isset($geofence['features']) || ! is_array($geofence['features'])) {
            return false;
        }

        // Extract polygon coordinates
        $polygons = $this->extractPolygonCoordinates($geofence);

        if (empty($polygons)) {
            return false;
        }

        // Check if point is in any of the polygons
        $point = [$longitude, $latitude]; // GeoJSON uses [lng, lat] format

        foreach ($polygons as $polygon) {
            if ($this->isPointInPolygon($point, $polygon)) {
                return true;
            }
        }

        return false;
    }

    /**
     * Extract polygon coordinates from GeoJSON
     *
     * @param  array  $geojson
     */
    private function extractPolygonCoordinates($geojson): array
    {
        $polygons = [];

        if (! isset($geojson['features']) || ! is_array($geojson['features'])) {
            return $polygons;
        }

        foreach ($geojson['features'] as $feature) {
            if (! isset($feature['geometry']) || ! isset($feature['geometry']['coordinates'])) {
                continue;
            }

            $geometry = $feature['geometry'];
            $type = $geometry['type'] ?? null;

            if ($type === 'MultiPolygon') {
                foreach ($geometry['coordinates'] as $polygon) {
                    if (! empty($polygon[0])) {
                        $polygons[] = $polygon[0]; // Get outer ring
                    }
                }
            } elseif ($type === 'Polygon') {
                if (! empty($geometry['coordinates'][0])) {
                    $polygons[] = $geometry['coordinates'][0]; // Get outer ring
                }
            }
        }

        return $polygons;
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

            $intersect = (($yi > $y) != ($yj > $y))
                && ($x < ($xj - $xi) * ($y - $yi) / ($yj - $yi) + $xi);

            if ($intersect) {
                $inside = ! $inside;
            }
        }

        return $inside;
    }

    /**
     * Validate that the valuation property belongs to the selected assembly
     *
     * @param  ValuationList  $valuationProperty
     */
    private function validateAssemblyMatch($valuationProperty, string $assemblyCode): bool
    {
        // Get assembly name from code
        $assembly = Assembly::where('assembly_code', $assemblyCode)->first();

        if (! $assembly) {
            return false;
        }

        // Check if valuation property's assembly name matches
        if (isset($valuationProperty->assembly_name)) {
            return stripos($valuationProperty->assembly_name, $assembly->name) !== false;
        }

        return false;
    }

    /**
     * Get validation error message for property assignment
     */
    public function getValidationErrorMessage(array $errors): string
    {
        if (empty($errors)) {
            return '';
        }

        return implode(' ', $errors);
    }
}

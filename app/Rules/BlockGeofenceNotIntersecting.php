<?php

namespace App\Rules;

use App\Models\Block;
use Closure;
use Illuminate\Contracts\Validation\ValidationRule;

class BlockGeofenceNotIntersecting implements ValidationRule
{
    protected $divisionId;

    protected $excludeBlockId;

    public function __construct($divisionId, $excludeBlockId = null)
    {
        $this->divisionId = $divisionId;
        $this->excludeBlockId = $excludeBlockId;
    }

    /**
     * Run the validation rule.
     *
     * @param  \Closure(string): \Illuminate\Translation\PotentiallyTranslatedString  $fail
     */
    public function validate(string $attribute, mixed $value, Closure $fail): void
    {
        try {
            // Decode the geofence
            $newGeofence = json_decode($value, true);

            if (! $newGeofence) {
                $fail('Invalid geofence format.');

                return;
            }

            // Extract coordinates from the new geofence
            $newCoordinates = $this->extractCoordinates($newGeofence);
            if (! $newCoordinates) {
                $fail('Invalid geofence format - could not extract coordinates.');

                return;
            }

            // Get existing blocks for the same division
            $existingBlocks = Block::where('division_id', $this->divisionId)
                ->when($this->excludeBlockId, function ($query) {
                    $query->where('id', '!=', $this->excludeBlockId);
                })
                ->whereNotNull('geofence')
                ->get();

            foreach ($existingBlocks as $block) {
                // Extract coordinates from existing block
                $existingCoordinates = $this->extractCoordinates($block->geofence);

                if (! $existingCoordinates) {
                    // Skip blocks with invalid geofence format
                    continue;
                }

                if ($this->polygonsIntersect($newCoordinates, $existingCoordinates)) {
                    $fail("The geofence intersects with an existing block: {$block->name}");

                    return;
                }
            }
        } catch (\Exception $e) {
            $fail('Error validating geofence: '.$e->getMessage());
        }
    }

    /**
     * Extract coordinates from various geofence formats
     */
    private function extractCoordinates($geofence)
    {
        if (! is_array($geofence)) {
            return null;
        }

        // Format 1: FeatureCollection with features array
        if (isset($geofence['features'][0]['geometry']['coordinates'])) {
            return $geofence['features'][0]['geometry']['coordinates'];
        }

        // Format 2: Direct geometry object
        if (isset($geofence['geometry']['coordinates'])) {
            return $geofence['geometry']['coordinates'];
        }

        // Format 3: Direct coordinates array
        if (isset($geofence['coordinates'])) {
            return $geofence['coordinates'];
        }

        // Format 4: Array of coordinates (direct)
        if (isset($geofence[0]) && is_array($geofence[0])) {
            return $geofence;
        }

        return null;
    }

    /**
     * Check if two polygons intersect using a simple bounding box and point-in-polygon algorithm
     */
    private function polygonsIntersect($polygon1, $polygon2): bool
    {
        // polygon1 and polygon2 are in MultiPolygon format: [[[[lon, lat], [lon, lat], ...]]]
        // or Polygon format: [[[lon, lat], [lon, lat], ...]]
        // Extract the exterior ring (first ring of first polygon)

        // Handle MultiPolygon: [[[[lon, lat]]]] -> [[[lon, lat]]] -> [[lon, lat]]
        // Handle Polygon: [[[lon, lat]]] -> [[lon, lat]]
        $poly1 = $polygon1;
        if (isset($polygon1[0][0][0]) && is_array($polygon1[0][0][0])) {
            // MultiPolygon format
            $poly1 = $polygon1[0][0];
        } elseif (isset($polygon1[0][0]) && is_array($polygon1[0][0])) {
            // Polygon format
            $poly1 = $polygon1[0];
        }

        $poly2 = $polygon2;
        if (isset($polygon2[0][0][0]) && is_array($polygon2[0][0][0])) {
            // MultiPolygon format
            $poly2 = $polygon2[0][0];
        } elseif (isset($polygon2[0][0]) && is_array($polygon2[0][0])) {
            // Polygon format
            $poly2 = $polygon2[0];
        }

        // Check if any point of polygon1 is inside polygon2
        foreach ($poly1 as $point) {
            if ($this->pointInPolygon($point, $poly2)) {
                return true;
            }
        }

        // Check if any point of polygon2 is inside polygon1
        foreach ($poly2 as $point) {
            if ($this->pointInPolygon($point, $poly1)) {
                return true;
            }
        }

        // Check if any edges intersect
        if ($this->edgesIntersect($poly1, $poly2)) {
            return true;
        }

        return false;
    }

    /**
     * Check if a point is inside a polygon using ray casting algorithm
     */
    private function pointInPolygon($point, $polygon): bool
    {
        $x = $point[0];
        $y = $point[1];
        $inside = false;

        $n = count($polygon);
        for ($i = 0, $j = $n - 1; $i < $n; $j = $i++) {
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
     * Check if any edges of the two polygons intersect
     */
    private function edgesIntersect($poly1, $poly2): bool
    {
        $n1 = count($poly1);
        $n2 = count($poly2);

        for ($i = 0; $i < $n1; $i++) {
            $p1 = $poly1[$i];
            $p2 = $poly1[($i + 1) % $n1];

            for ($j = 0; $j < $n2; $j++) {
                $p3 = $poly2[$j];
                $p4 = $poly2[($j + 1) % $n2];

                if ($this->linesIntersect($p1, $p2, $p3, $p4)) {
                    return true;
                }
            }
        }

        return false;
    }

    /**
     * Check if two line segments intersect
     */
    private function linesIntersect($p1, $p2, $p3, $p4): bool
    {
        $d1 = $this->direction($p3, $p4, $p1);
        $d2 = $this->direction($p3, $p4, $p2);
        $d3 = $this->direction($p1, $p2, $p3);
        $d4 = $this->direction($p1, $p2, $p4);

        if ((($d1 > 0 && $d2 < 0) || ($d1 < 0 && $d2 > 0)) &&
            (($d3 > 0 && $d4 < 0) || ($d3 < 0 && $d4 > 0))) {
            return true;
        }

        if ($d1 == 0 && $this->onSegment($p3, $p4, $p1)) {
            return true;
        }
        if ($d2 == 0 && $this->onSegment($p3, $p4, $p2)) {
            return true;
        }
        if ($d3 == 0 && $this->onSegment($p1, $p2, $p3)) {
            return true;
        }
        if ($d4 == 0 && $this->onSegment($p1, $p2, $p4)) {
            return true;
        }

        return false;
    }

    /**
     * Calculate the direction of point p3 relative to line p1-p2
     */
    private function direction($p1, $p2, $p3): float
    {
        return ($p3[1] - $p1[1]) * ($p2[0] - $p1[0]) - ($p2[1] - $p1[1]) * ($p3[0] - $p1[0]);
    }

    /**
     * Check if point p is on segment p1-p2
     */
    private function onSegment($p1, $p2, $p): bool
    {
        return min($p1[0], $p2[0]) <= $p[0] && $p[0] <= max($p1[0], $p2[0]) &&
               min($p1[1], $p2[1]) <= $p[1] && $p[1] <= max($p1[1], $p2[1]);
    }
}

<?php

namespace App\Imports\Block;

use App\Models\PolygonBlock;
use Illuminate\Validation\ValidationException;
use Maatwebsite\Excel\Concerns\Importable;
use Maatwebsite\Excel\Concerns\RegistersEventListeners;
use Maatwebsite\Excel\Concerns\SkipsEmptyRows;
use Maatwebsite\Excel\Concerns\ToModel;
use Maatwebsite\Excel\Concerns\WithEvents;
use Maatwebsite\Excel\Concerns\WithHeadingRow;
use Maatwebsite\Excel\Concerns\WithValidation;
use Maatwebsite\Excel\Events\BeforeImport;

class BlockImport implements SkipsEmptyRows, ToModel, WithEvents, WithHeadingRow, WithValidation
{
    use Importable, RegistersEventListeners;

    protected $created_by;

    public function __construct($created_by)
    {
        $this->created_by = $created_by;
    }

    public function model(array $row)
    {
        $name = 'ABNMA'.'/'.$row['block_number'] ?? null;
        $polygon = $row['multipolygon'] ?? null;
        $block_number = $row['block_number'] ?? null;

        if (! empty($name) && ! empty($polygon)) {
            return new PolygonBlock([
                'name' => $name,
                'block_number' => $block_number,
                'boundary' => $this->convertWKTtoGeoJSON($polygon),
            ]);
        }
    }

    public function rules(): array
    {
        return [];
    }

    public static function beforeImport(BeforeImport $event)
    {
        if ($event->getReader()->getActiveSheet()->getHighestRow() <= 1) {
            throw ValidationException::withMessages([
                'file' => 'You cannot upload an empty excel sheet.',
            ]);
        }
    }

    private function convertWKTtoGeoJSON($wkt)
    {
        // Remove "MULTIPOLYGON(((" and ")))"
        $wkt = str_replace(['MULTIPOLYGON(((', ')))'], '', $wkt);

        // Extract individual polygons within the multipolygon
        $polygons = explode(')),((', $wkt);

        $multiCoordinates = [];

        foreach ($polygons as $polygon) {
            // Extract coordinate pairs for each polygon
            preg_match_all('/-?\d+\.\d+ -?\d+\.\d+/', $polygon, $matches);

            $coordinates = [];
            foreach ($matches[0] as $pair) {
                [$lon, $lat] = explode(' ', trim($pair));
                $coordinates[] = [(float) $lon, (float) $lat];
            }

            // Add polygon coordinates to the multipolygon array
            $multiCoordinates[] = [$coordinates];
        }

        return json_encode([
            'type' => 'MultiPolygon',
            'coordinates' => $multiCoordinates,
        ]);
    }
}

<?php

namespace App\Imports\Building;

use App\Models\Building;
use Illuminate\Validation\ValidationException;
use Maatwebsite\Excel\Concerns\Importable;
use Maatwebsite\Excel\Concerns\RegistersEventListeners;
use Maatwebsite\Excel\Concerns\SkipsEmptyRows;
use Maatwebsite\Excel\Concerns\ToModel;
use Maatwebsite\Excel\Concerns\WithEvents;
use Maatwebsite\Excel\Concerns\WithHeadingRow;
use Maatwebsite\Excel\Concerns\WithValidation;
use Maatwebsite\Excel\Events\BeforeImport;

class BuildingImport implements SkipsEmptyRows, ToModel, WithEvents, WithHeadingRow, WithValidation
{
    use Importable, RegistersEventListeners;

    protected $created_by;

    public function __construct($created_by)
    {
        $this->created_by = $created_by;
    }

    public function model(array $row)
    {
        $name = $row['assembly_code'].'/'.$row['division_number_int'].'/'.$row['block_int'];
        $polygon = $row['polygon'] ?? null;
        $block_id = $row['block_int'] ?? null;

        if (! empty($name) && ! empty($polygon)) {
            return new Building([
                'name' => $name,
                'block_id' => $block_id,
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
        // Remove "POLYGON((" and "))"
        $wkt = str_replace(['POLYGON((', '))'], '', $wkt);

        // Extract coordinate pairs
        preg_match_all('/-?\d+\.\d+ -?\d+\.\d+/', $wkt, $matches);

        $coordinates = [];
        foreach ($matches[0] as $pair) {
            [$lon, $lat] = explode(' ', trim($pair));
            $coordinates[] = [(float) $lon, (float) $lat];
        }

        return json_encode([
            'type' => 'Polygon',
            'coordinates' => [$coordinates],
        ]);
    }
}

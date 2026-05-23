<?php

namespace App\Imports\Property;

use App\Models\Assembly;
use App\Models\Block;
use App\Models\Division;
use App\Models\Property;
use App\Models\PropertyUser;
use App\Models\Zone;
use Illuminate\Validation\ValidationException;
use Maatwebsite\Excel\Concerns\Importable;
use Maatwebsite\Excel\Concerns\RegistersEventListeners;
use Maatwebsite\Excel\Concerns\SkipsEmptyRows;
use Maatwebsite\Excel\Concerns\ToModel;
use Maatwebsite\Excel\Concerns\WithEvents;
use Maatwebsite\Excel\Concerns\WithHeadingRow;
use Maatwebsite\Excel\Concerns\WithValidation;
use Maatwebsite\Excel\Events\BeforeImport;

class PropertiesImport implements SkipsEmptyRows, ToModel, WithEvents, WithHeadingRow, WithValidation
{
    use Importable, RegistersEventListeners;

    protected $created_by;

    public function __construct($created_by)
    {
        $this->created_by = $created_by;
    }

    public function model(array $row)
    {
        $division = Division::where('division_name', 'LIKE', '%'.$row['division'].'%')->first();
        $block = Block::where('block_name', 'LIKE', '%'.$row['block'].'%')->first();
        $zone = Zone::where('name', 'LIKE', '%'.$row['zone'].'%')->first();

        $propertyUse = PropertyUser::where('name', 'LIKE', '%'.$row['property_use'].'%')
            ->where('zone_id', $zone->id)
            ->first();

        $assembly = Assembly::where('name', 'LIKE', '%'.$row['assembly'].'%')->first();

        return new Property([
            'rated' => $row['rated'] ?? null,
            'validated' => $row['validated'],
            'ratable_value' => $row['rateable_value'] ?? null,
            'longitude' => $row['longitude'] ?? null,
            'latitude' => $row['latitude'] ?? null,
            'assembly_code' => $assembly->assembly_code ?? null,
            'division_id' => $division->id ?? null,
            'block_id' => $block->id ?? null,
            'zone_id' => $zone->id ?? null,
            'property_use_id' => $propertyUse->id ?? null,
            'property_number' => $row['property_number'] ?? null,
            'created_by' => $this->created_by,
        ]);
    }

    public function rules(): array
    {
        return [
            'rated' => ['required', 'string'],
            'validated' => ['required', 'string'],
            'rateable_value' => ['required'],
            'longitude' => ['required'],
            'latitude' => ['required'],
            'assembly' => ['required', 'string'],
            'division' => ['required', 'string'],
            'block' => ['required', 'string'],
            'zone' => ['required', 'string'],
            'property_use' => ['required', 'string'],
            'property_number' => ['required', 'string', 'unique:properties,property_number'],
        ];
    }

    public static function beforeImport(BeforeImport $event)
    {
        if ($event->getReader()->getActiveSheet()->getHighestRow() <= 1) {
            throw ValidationException::withMessages([
                'file' => 'You cannot upload an empty excel sheet.',
            ]);
        }
    }
}

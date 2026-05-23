<?php

namespace App\Imports\PropertyRate;

use App\Models\Assembly;
use App\Models\PropertyUser;
use App\Models\Rate;
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

class PropertyRateImport implements SkipsEmptyRows, ToModel, WithEvents, WithHeadingRow, WithValidation
{
    use Importable, RegistersEventListeners;

    protected $created_by;

    public function __construct($created_by)
    {
        $this->created_by = $created_by;
    }

    public function model(array $row)
    {
        $zone = Zone::where('name', 'LIKE', '%'.$row['zone'].'%')->first();
        $propertyUse = PropertyUser::where('name', 'LIKE', '%'.$row['property_use'].'%')
            ->where('zone_id', $zone->id)
            ->first();

        $assembly = Assembly::where('name', 'LIKE', '%'.$row['assembly'].'%')->first();

        return new Rate([
            'assembly_code' => $assembly->assembly_code ?? null,
            'zone_id' => $zone->id ?? null,
            'property_use_id' => $propertyUse->id ?? null,
            'rate' => $row['rate'] ?? null,
            'minimum_rate' => $row['min_rate'] ?? null,
            'created_by' => $this->created_by,
        ]);
    }

    public function rules(): array
    {
        return [
            'assembly' => ['required', 'string'],
            'zone' => ['required', 'string'],
            'property_use' => ['required', 'string'],
            'rate' => ['required'],
            'min_rate' => ['required'],
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

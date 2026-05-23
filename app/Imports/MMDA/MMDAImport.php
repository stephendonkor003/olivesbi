<?php

namespace App\Imports\MMDA;

use App\Models\GhanaRegion;
use App\Models\Mmda;
use Illuminate\Validation\ValidationException;
use Maatwebsite\Excel\Concerns\Importable;
use Maatwebsite\Excel\Concerns\RegistersEventListeners;
use Maatwebsite\Excel\Concerns\SkipsEmptyRows;
use Maatwebsite\Excel\Concerns\ToModel;
use Maatwebsite\Excel\Concerns\WithEvents;
use Maatwebsite\Excel\Concerns\WithHeadingRow;
use Maatwebsite\Excel\Concerns\WithValidation;
use Maatwebsite\Excel\Events\BeforeImport;

class MMDAImport implements SkipsEmptyRows, ToModel, WithEvents, WithHeadingRow, WithValidation
{
    use Importable, RegistersEventListeners;

    protected $created_by;

    public function __construct($created_by)
    {
        $this->created_by = $created_by;
    }

    public function model(array $row)
    {
        $inputRegion = strtolower(trim($row['region_name']));

        if (! str_contains($inputRegion, 'region')) {
            $inputRegion .= ' region';
        }

        $inputRegion = ucwords($inputRegion);

        $region = GhanaRegion::get()
            ->first(function ($r) use ($inputRegion) {
                return $r->name === $inputRegion;
            });

        return new Mmda([
            'region_id' => $region->id ?? null,
            'region_code' => $row['region_code'],
            'assembly_code' => $row['assembly_code'] ?? null,
            'assembly_name' => $row['assembly_name'] ?? null,
            'assembly_id' => $row['assembly_id'] ?? null,
            'assembly_category' => $row['assembly_category'] ?? null,
            'created_by' => $this->created_by,
        ]);
    }

    public function rules(): array
    {
        return [
            'region_name' => ['required'],
            'region_code' => ['required'],
            'assembly_code' => ['required'],
            'assembly_name' => ['required'],
            'assembly_id' => ['required'],
            'assembly_category' => ['required'],
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

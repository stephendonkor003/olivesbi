<?php

namespace App\Imports\RevenueStream;

use App\Models\PropertyClasses;
use App\Models\PropertyType;
use App\Models\RevenueStream;
use App\Models\RevenueStreamType;
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

class RevenueStreamImport implements SkipsEmptyRows, ToModel, WithEvents, WithHeadingRow, WithValidation
{
    use Importable, RegistersEventListeners;

    protected $created_by;

    protected $assemblyCode;

    public function __construct($created_by, $assemblyCode)
    {
        $this->created_by = $created_by;
        $this->assemblyCode = $assemblyCode;
    }

    public function model(array $row)
    {
        $zoneType = $this->getZoneType($row['zone_type']);

        $propertyClass = null;
        if (! empty($row['property_class'])) {
            $propertyClass = $this->getPropertyClass($row['property_class']);
        }
        $propertyType = null;
        if (! empty($row['property_type'])) {
            $propertyType = $this->getPropertyType($row['property_type']);
        }

        // Get Revenue Stream Type for "Property Rate"
        $revenueStreamType = RevenueStreamType::where('name', 'Property Rate')->first();

        $row['assessed_property'] = in_array($row['assessed_property'], ['Y', 'YES']) ? 'Y' : 'N';

        $data = [
            'assembly_code' => $this->assemblyCode,
            'revenue_stream_type_id' => $revenueStreamType?->id,
            'is_rated' => $row['assessed_property'],
            'zone_type_id' => $zoneType?->id,
            'property_classes_id' => $propertyClass?->id,
            'property_type_id' => $propertyType?->id,
            'rate_impost' => $row['rate_impost'] ?? 0,
            'min_amount' => $row['min_amount'] ?? 0,
            'flat_rate' => $row['flat_rate'] ?? 0,
            'basic_rate' => $row['basic_rate'] ?? 0,
            'description' => $row['description'] ?? null,
            'created_by' => $this->created_by,
        ];

        return RevenueStream::query()
            ->firstOrCreate([
                'assembly_code' => $this->assemblyCode,
                'is_rated' => $data['is_rated'],
                'revenue_stream_type_id' => $data['revenue_stream_type_id'],
                'zone_type_id' => $data['zone_type_id'],
                'property_classes_id' => $data['property_classes_id'],

            ], $data);
    }

    public function rules(): array
    {
        return [
            'assessed_property' => ['required', 'string', 'in:Y,N,YES,NO'],
            'zone_type' => ['required', 'string'],
            'property_class' => ['nullable', 'string'],
            'rate_impost' => ['nullable', 'numeric'],
            'min_amount' => ['nullable', 'numeric'],
            'flat_rate' => ['nullable', 'numeric'],
            'property_type' => ['nullable', 'string'],
            'description' => ['nullable', 'string'],
            'basic_rate' => ['required', 'numeric'],
        ];
    }

    public function prepareForValidation($data, $index)
    {
        $data['assessed_property'] = strtoupper($data['assessed_property_yn'] ?? $data['assessed_property'] ?? '');

        return $data;
    }

    public static function beforeImport(BeforeImport $event)
    {
        if ($event->getReader()->getActiveSheet()->getHighestRow() <= 1) {
            throw ValidationException::withMessages([
                'file' => 'You cannot upload an empty excel sheet.',
            ]);
        }
    }

    private function getZoneType($name)
    {
        $item = Zone::where('name', $name)
            ->where('assembly_code', $this->assemblyCode)
            ->first();

        if ($item) {
            return $item;
        }

        return Zone::create([
            'name' => $name,
            'assembly_code' => $this->assemblyCode,
            'created_by' => $this->created_by,
        ]);
    }

    private function getPropertyClass($name)
    {
        $item = PropertyClasses::where('name', $name)
            ->where('assembly_code', $this->assemblyCode)
            ->first();

        if ($item) {
            return $item;
        }

        return PropertyClasses::create([
            'name' => $name,
            'assembly_code' => $this->assemblyCode,
            'created_by' => $this->created_by,
        ]);
    }

    private function getPropertyType($name)
    {
        $item = PropertyType::where('name', $name)
            ->where('assembly_code', $this->assemblyCode)
            ->first();

        if ($item) {
            return $item;
        }

        return PropertyType::create([
            'name' => $name,
            'assembly_code' => $this->assemblyCode,
            'created_by' => $this->created_by,
        ]);
    }
}

<?php

namespace App\Imports\RevenueStream;

use App\Models\Category;
use App\Models\ItemDescription;
use App\Models\MainItem;
use App\Models\RevenueStream;
use App\Models\RevenueStreamType;
use App\Models\SubItem;
use Illuminate\Support\Str;
use Illuminate\Validation\ValidationException;
use Maatwebsite\Excel\Concerns\Importable;
use Maatwebsite\Excel\Concerns\RegistersEventListeners;
use Maatwebsite\Excel\Concerns\SkipsEmptyRows;
use Maatwebsite\Excel\Concerns\ToModel;
use Maatwebsite\Excel\Concerns\WithEvents;
use Maatwebsite\Excel\Concerns\WithHeadingRow;
use Maatwebsite\Excel\Concerns\WithValidation;
use Maatwebsite\Excel\Events\BeforeImport;

class BOPRevenueStreamImport implements SkipsEmptyRows, ToModel, WithEvents, WithHeadingRow, WithValidation
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
        $mainItem = null;
        if (! empty($row['main_item'])) {
            $mainItem = $this->getMainItem($row['main_item']);
        }

        $subItem = null;
        if (! empty($row['sub_item'])) {
            $subItem = $this->getSubItem($row['sub_item']);
        }

        $category = null;
        if (! empty($row['category'])) {
            $category = $this->getCategory($row['category']);
        }

        $itemDescription = null;
        if (! empty($row['item_description'])) {
            $itemDescription = $this->getItemDescription($row['item_description']);
        }

        $revenueStreamType = RevenueStreamType::where('name', 'BOP (Licenses)')->first();

        $data = [
            'code' => $row['code'],
            'assembly_code' => $this->assemblyCode,
            'revenue_stream_type_id' => $revenueStreamType?->id,
            'is_rated' => strtoupper($row['assessed_property'] ?? 'Y'),
            'description_id' => $itemDescription?->id,
            'category_id' => $category?->id,
            'main_item_id' => $mainItem?->id,
            'sub_item_id' => $subItem?->id,
            'duration' => $row['duration'] ?? null,
            'flat_rate' => $row['flat_rate'] ?? 0,
            'basic_rate' => $row['basic_rate'] ?? 0,
            'created_by' => $this->created_by,
        ];

        return RevenueStream::query()
            ->firstOrCreate([
                'assembly_code' => $this->assemblyCode,
                'revenue_stream_type_id' => $data['revenue_stream_type_id'],
                'description_id' => $data['description_id'],
                'category_id' => $data['category_id'],
            ], $data);
    }

    public function rules(): array
    {
        return [
            'code' => ['nullable', 'numeric'],
            'main_item' => ['nullable', 'string'],
            'sub_item' => ['nullable', 'string'],
            'category' => ['required', 'string'],
            'item_description' => ['required', 'string'],
            'duration' => ['nullable', 'string', 'in:Per Annum,Per Square Meter'],
            'flat_rate' => ['required', 'numeric'],
            'basic_rate' => ['required', 'numeric'],
        ];
    }

    /**
     * @return array
     */
    public function customValidationMessages()
    {
        return [
            'duration.in' => 'Duration should be either Per Annum, Per Square Meter',
        ];
    }

    public function prepareForValidation($data, $index)
    {
        $data['duration'] = Str::title($data['duration'] ?? '');

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

    private function getMainItem($name)
    {
        $item = MainItem::where('name', $name)
            ->where('assembly_code', $this->assemblyCode)
            ->first();

        if ($item) {
            return $item;
        }

        return MainItem::create([
            'name' => $name,
            'assembly_code' => $this->assemblyCode,
            'created_by' => $this->created_by,
        ]);
    }

    private function getSubItem($name)
    {
        $item = SubItem::where('name', $name)
            ->where('assembly_code', $this->assemblyCode)
            ->first();

        if ($item) {
            return $item;
        }

        return SubItem::create([
            'name' => $name,
            'assembly_code' => $this->assemblyCode,
            'created_by' => $this->created_by,
        ]);
    }

    private function getCategory($name)
    {
        $item = Category::where('name', $name)
            ->where('assembly_code', $this->assemblyCode)
            ->first();

        if ($item) {
            return $item;
        }

        return Category::create([
            'name' => $name,
            'assembly_code' => $this->assemblyCode,
            'created_by' => $this->created_by,
        ]);
    }

    private function getItemDescription($name)
    {
        $item = ItemDescription::where('name', $name)
            ->where('assembly_code', $this->assemblyCode)
            ->first();

        if ($item) {
            return $item;
        }

        return ItemDescription::create([
            'name' => $name,
            'assembly_code' => $this->assemblyCode,
            'created_by' => $this->created_by,
        ]);
    }
}

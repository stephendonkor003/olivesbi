<?php

namespace App\Http\Requests\BusRate;

use Illuminate\Foundation\Http\FormRequest;
use Illuminate\Validation\Rule;

class CreateBusRateRequest extends FormRequest
{
    /**
     * Determine if the user is authorized to make this request.
     */
    public function authorize(): bool
    {
        return true;
    }

    /**
     * Get the validation rules that apply to the request.
     *
     * @return array<string, \Illuminate\Contracts\Validation\ValidationRule|array<mixed>|string>
     */
    public function rules(): array
    {
        return [
            'zone_id' => [
                'required',
                'string',
                'exists:zones,id',
                $this->uniqueCombinationRule(),
            ],
            'assembly_code' => [
                'required',
                'string',
                'exists:assemblies,assembly_code',
            ],
            'property_use_id' => [
                'required',
                'string',
                'exists:property_users,id',
                $this->uniqueCombinationRule(),
            ],
            'amount' => ['required', 'numeric'],
        ];
    }

    private function uniqueCombinationRule()
    {
        return Rule::unique('bop_rates')
            ->where(function ($query) {
                return $query->where('assembly_code', $this->assembly_code)
                    ->where('zone_id', $this->zone_id)
                    ->where('property_use_id', $this->property_use_id);
            })
            ->ignore($this->id);
    }
}

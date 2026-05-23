<?php

namespace App\Http\Requests\Bill;

use Illuminate\Foundation\Http\FormRequest;

class CreateSingleBillRequest extends FormRequest
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
            'bills_year' => ['required', 'string'],
            'assembly_code' => ['nullable', 'string', 'exists:assemblies,assembly_code'],
            'property_id' => ['required', 'array', 'min:1'],
            'property_id.*' => ['integer'],
        ];
    }

    /**
     * Configure the validator instance.
     */
    public function withValidator($validator)
    {
        $validator->after(function ($validator) {
            if ($this->has('property_id')) {
                $propertyIds = $this->input('property_id');

                // Validate all property IDs exist in a single query
                $existingCount = \DB::table('properties')
                    ->whereIn('id', $propertyIds)
                    ->count();

                if ($existingCount !== count($propertyIds)) {
                    $validator->errors()->add('property_id', 'Some selected properties are invalid.');
                }
            }
        });
    }
}

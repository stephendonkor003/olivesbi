<?php

namespace App\Http\Requests\BusBill;

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
            'business_id' => ['required', 'array', 'min:1'],
            'business_id.*' => ['integer'],
        ];
    }

    /**
     * Configure the validator instance.
     */
    public function withValidator($validator)
    {
        $validator->after(function ($validator) {
            if ($this->has('business_id')) {
                $businessIds = $this->input('business_id');

                // Validate all business IDs exist in a single query
                $existingCount = \DB::table('businesses')
                    ->whereIn('id', $businessIds)
                    ->count();

                if ($existingCount !== count($businessIds)) {
                    $validator->errors()->add('business_id', 'Some selected businesses are invalid.');
                }
            }
        });
    }
}

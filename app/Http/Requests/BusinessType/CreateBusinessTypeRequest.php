<?php

namespace App\Http\Requests\BusinessType;

use Illuminate\Foundation\Http\FormRequest;

class CreateBusinessTypeRequest extends FormRequest
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
            'name' => 'required|string|unique:business_types',
            'parent_category' => 'required|string',
            'sub_categories' => 'required|array',
            'sub_categories.*' => 'in:Category A,Category B,Category C,Category D,Category E,Category F,Category G,Category H',
            'rate' => 'required|numeric',
        ];
    }
}

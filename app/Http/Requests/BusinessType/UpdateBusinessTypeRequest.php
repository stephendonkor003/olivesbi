<?php

namespace App\Http\Requests\BusinessType;

use Illuminate\Foundation\Http\FormRequest;

class UpdateBusinessTypeRequest extends FormRequest
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
            'name' => 'nullable|string',
            'parent_category' => 'nullable|string',
            'sub_categories' => 'nullable|array',
            'sub_categories.*' => 'in:Category A,Category B,Category C,Category D,Category E,Category F,Category G,Category H',
            'rate' => 'nullable|numeric',
        ];
    }
}

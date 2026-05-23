<?php

namespace App\Http\Requests\Rate;

use Illuminate\Foundation\Http\FormRequest;

class UpdateRateRequest extends FormRequest
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
            'zone_id' => ['required', 'string', 'exists:zones,id'],
            'assembly_code' => ['required', 'string', 'exists:assemblies,assembly_code'],
            'property_use_id' => ['required', 'string', 'exists:property_users,id'],
            'rate' => ['nullable', 'numeric'],
            'minimum_rate' => ['required', 'numeric'],
        ];
    }
}

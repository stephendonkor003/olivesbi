<?php

namespace App\Http\Requests\Assembly;

use Illuminate\Foundation\Http\FormRequest;

class UpdateAssemblyRequest extends FormRequest
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
            'name' => ['nullable', 'string'],
            'assembly_code' => ['nullable', 'string'],
            'regional_code' => ['nullable', 'string', 'exists:ghana_regions,regional_code'],
            'supervisor' => ['nullable', 'string'],
            'logo' => ['nullable', 'file', 'mimes:jpeg,jpg,png,gif'],
            'invoice_layout' => ['nullable', 'string'],
            'status' => ['nullable', 'string'],
            'geo_reference_area' => ['nullable', 'string'],
            'address' => ['nullable', 'string'],
            'assembly_category' => ['required', 'string', 'in:Municipal,Metropolitan,District'],
            'phone' => ['nullable', 'string'],
            'geo_coordinate' => ['nullable', 'string'],
            'bank_name' => ['nullable', 'string'],
            'bank_number' => ['nullable', 'numeric'],
            'bank_branch' => ['nullable', 'string'],
        ];
    }
}

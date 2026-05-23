<?php

namespace App\Http\Requests\Assembly;

use Illuminate\Foundation\Http\FormRequest;
use Illuminate\Validation\Rule;

class CreateAssemblyRequest extends FormRequest
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
        $assembly = $this->route('assembly');

        return [
            'name' => ['required', 'string', Rule::unique('assemblies', 'name')->ignore($this->assembly->id ?? null)],
            'assembly_code' => ['required', 'string', Rule::unique('assemblies', 'assembly_code')->ignore($this->assembly->id ?? null)],
            'regional_code' => ['required', 'string', 'exists:ghana_regions,regional_code'],
            'supervisor' => ['required', 'string', 'exists:users,id'],
            'logo' => ['nullable', 'file', 'mimes:jpeg,jpg,png,gif'],
            'invoice_layout' => ['nullable', 'string'],
            'address' => ['required', 'string'],
            'assembly_category' => ['required', 'string', 'in:Municipal,Metropolitan,District'],
            'phone' => ['required', 'string'],
            'geo_coordinate' => ['nullable', 'string'],
            'bank_name' => ['nullable', 'string'],
            'bank_number' => ['nullable', 'numeric'],
            'bank_branch' => ['nullable', 'string'],
        ];
    }
}

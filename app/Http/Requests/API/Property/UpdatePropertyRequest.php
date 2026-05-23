<?php

namespace App\Http\Requests\API\Property;

use Illuminate\Foundation\Http\FormRequest;

class UpdatePropertyRequest extends FormRequest
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
            'hseno' => 'nullable|string',
            'digitaladdress' => 'nullable|string',
            'location' => 'required|string',
            'streetname' => 'nullable|string',
            'is_rated' => 'nullable|string|in:Yes,No',
            'rateable_value' => 'nullable|numeric',
            'owner_id' => 'nullable|string|exists:entity_owners,erms_id',
            'assembly_code' => 'required|string|exists:assemblies,assembly_code',
            'longitude' => 'required|string',
            'latitude' => 'required|string',
            'division_id' => 'required|string|exists:divisions,id',
            'block_id' => 'required|string|exists:blocks,id',
        ];
    }
}

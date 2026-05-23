<?php

namespace App\Http\Requests\Division;

use App\Rules\GeofenceNotIntersecting;
use Illuminate\Foundation\Http\FormRequest;
use Illuminate\Validation\Rule;

class UpdateDivisionRequest extends FormRequest
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
        $hasAssemblyCode = ! empty($this->user()->assembly_code);

        $divisionId = $this->route('division');
        $assemblyCode = $this->input('assembly_code') ?? $this->user()->assembly_code;

        return [
            'assembly_code' => [Rule::excludeIf($hasAssemblyCode), 'required', Rule::exists('assemblies', 'assembly_code')],
            'code' => ['required', 'string', Rule::unique('divisions')->ignore($divisionId)],
            'name' => ['required', 'string', Rule::unique('divisions')->ignore($divisionId)],
            'geofence' => [
                'required',
                'string',
                new GeofenceNotIntersecting($assemblyCode, $divisionId),
            ],
        ];
    }

    public function messages(): array
    {
        return [
            'geofence.required' => 'Please draw a geofence for the division on the map.',
        ];
    }
}

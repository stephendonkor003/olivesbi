<?php

namespace App\Http\Requests\Division;

use App\Rules\GeofenceNotIntersecting;
use Illuminate\Foundation\Http\FormRequest;
use Illuminate\Validation\Rule;

class CreateDivisionRequest extends FormRequest
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

        $assemblyCode = $this->user()->assembly_code ?? $this->get('assembly_code');

        return [
            'assembly_code' => [Rule::excludeIf($hasAssemblyCode), 'required', Rule::exists('assemblies', 'assembly_code')],
            'code' => ['required', 'string', 'unique:divisions,code'],
            'name' => ['required', 'string', 'unique:divisions,name'],
            'geofence' => [
                'required',
                'string',
                new GeofenceNotIntersecting($assemblyCode),
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

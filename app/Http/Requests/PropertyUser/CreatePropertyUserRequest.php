<?php

namespace App\Http\Requests\PropertyUser;

use Illuminate\Foundation\Http\FormRequest;
use Illuminate\Validation\Rule;

class CreatePropertyUserRequest extends FormRequest
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
            'name' => [
                'required',
                'string',
                Rule::unique('property_users')->where(function ($query) {
                    return $query->where('zone_id', $this->zone_id)
                        ->where('assembly_code', $this->assembly_code);
                }),
            ],
            'zone_id' => ['required', 'string', 'exists:zones,id'],
            'assembly_code' => ['required', 'string', 'exists:assemblies,assembly_code'],
        ];
    }
}

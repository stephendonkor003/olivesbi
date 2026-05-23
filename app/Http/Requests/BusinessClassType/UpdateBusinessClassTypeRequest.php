<?php

namespace App\Http\Requests\BusinessClassType;

use Illuminate\Foundation\Http\FormRequest;

class UpdateBusinessClassTypeRequest extends FormRequest
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
            'category' => 'nullable|string',
            'rate' => 'nullable|numeric',
            'identifier' => 'nullable|string',
            'extra_class_identifier' => 'nullable|string',
        ];
    }
}

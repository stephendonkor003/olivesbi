<?php

namespace App\Http\Requests\MMDA;

use Illuminate\Foundation\Http\FormRequest;

class UpdateMMDARequest extends FormRequest
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
            'region_id' => ['required', 'string', 'exists:ghana_regions,id'],
            'region_code' => ['required', 'string'],
            'assembly_code' => ['required', 'string'],
            'assembly_name' => ['required', 'string'],
            'assembly_id' => ['required', 'string'],
            'assembly_category' => ['required', 'string'],
        ];
    }
}

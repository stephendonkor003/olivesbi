<?php

namespace App\Http\Requests\BusBill;

use Illuminate\Foundation\Http\FormRequest;

class CreateDivisionBillRequest extends FormRequest
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
            'bills_year' => ['required', 'string'],
            'division_id' => ['required', 'exists:divisions,id'],
        ];
    }
}

<?php

namespace App\Http\Requests\API\Statement;

use Illuminate\Foundation\Http\FormRequest;

class PaymentStatementRequest extends FormRequest
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
            'erms_id' => ['required', 'exists:users,erms_id'],
            'email' => ['required', 'email'],
        ];
    }
}

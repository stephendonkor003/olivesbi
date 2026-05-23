<?php

namespace App\Http\Requests\Customer;

use Illuminate\Foundation\Http\FormRequest;

class UpdateCustomerRequest extends FormRequest
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
        $citizen = $this->route('citizen');

        return [
            'first_name' => 'required|string',
            'last_name' => 'required|string',
            'prefix' => 'required|string',
            'gender' => 'required|string',
            'date_of_birth' => 'required|date',
            'marital_status' => 'required|string',
            'telephone_number' => 'required|string|phone:GH|unique:citizens,telephone_number,'.$citizen->id,
            'country_of_citizenship' => 'required|string',
            'customer_type' => 'required|string|exists:customer_types,id',
            'Ghana_card_number' => 'nullable|string|unique:citizens,Ghana_card_number,'.$citizen->id,
            'status' => 'required|string',
        ];
    }
}

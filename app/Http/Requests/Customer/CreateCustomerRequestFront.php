<?php

namespace App\Http\Requests\Customer;

use Illuminate\Foundation\Http\FormRequest;
use Illuminate\Validation\Rule;

class CreateCustomerRequestFront extends FormRequest
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
            'registration_type' => 'required|in:individual,organization',
            'account_plan' => 'nullable|in:subscriber,account,pro,sky,cloud,shell',
            'first_name' => 'nullable|string',
            'last_name' => 'nullable|string',
            'org_first_name' => 'nullable|string',
            'org_last_name' => 'nullable|string',
            'prefix' => 'nullable|string',
            'gender' => 'nullable|string',
            'date_of_birth' => 'nullable|date',
            'marital_status' => 'nullable|string',
            'telephone_number' => ['required_if:registration_type,individual', 'nullable', 'string', 'phone:INTERNATIONAL', 'unique:citizens,telephone_number'],
            'org_telephone_number' => ['required_if:registration_type,organization', 'nullable', 'string', 'phone:INTERNATIONAL', 'unique:citizens,telephone_number'],
            'password' => ['required_if:registration_type,individual', 'nullable', 'string', 'confirmed'],
            'org_password' => ['required_if:registration_type,organization', 'nullable', 'string', 'confirmed'],
            'country_of_citizenship' => 'nullable|string',
            'customer_type' => 'nullable|string|exists:customer_types,id',
            'Ghana_card_number' => 'nullable|string|unique:citizens',
            'id_type' => 'nullable|string',
            'id_number' => 'nullable|string',
            'business_name' => 'nullable|string',
            'email' => [
                Rule::requiredIf(fn () => $this->boolean('newsletter_opt_in')),
                'nullable',
                'email',
                'unique:citizens,email',
            ],
            'date_of_commencement' => 'nullable|string',
            'security_question' => 'nullable|string',
            'security_answer' => 'nullable|string',
            'tin_number' => 'nullable|string',
            'newsletter_opt_in' => 'nullable|boolean',
            'newsletter_topics' => 'nullable|array',
            'newsletter_topics.*' => 'nullable|string|max:50',
        ];
    }
}

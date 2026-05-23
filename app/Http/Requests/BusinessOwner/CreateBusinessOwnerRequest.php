<?php

namespace App\Http\Requests\BusinessOwner;

use Illuminate\Foundation\Http\FormRequest;

class CreateBusinessOwnerRequest extends FormRequest
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
        // Common validation rules
        $rules = [
            'entity_type' => 'required|string|in:individual,organization',
        ];

        $entityType = $this->input('entity_type');

        if ($entityType === 'individual') {
            $rules = array_merge($rules, [
                'firstname' => 'required|string|max:255',
                'lastname' => 'required|string|max:255',
                'middlename' => 'nullable|string|max:255',
                'gender' => 'required|string|in:Male,Female',
                'tin_number' => 'nullable|string|max:255',
                'email_i' => 'required|email|max:255',
                'primary_phone_i' => 'required|string|max:255',
                'secondary_phone_i' => 'nullable|string|max:255',
                'house_number_i' => 'required|string|max:255',
                'digital_address_i' => 'required|string|max:255',
                'residential_address_i' => 'nullable|string|max:255',
                'postal_address_i' => 'nullable|string|max:255',
            ]);
        } elseif ($entityType === 'organization') {
            $rules = array_merge($rules, [
                'organization_data' => 'nullable|array',
                'organization_data.*.organization_name' => 'required|string|max:255',
                'organization_data.*.email_o' => 'required|email|max:255',
                'organization_data.*.primary_phone_o' => 'required|string|max:255',
                'organization_data.*.secondary_phone_o' => 'nullable|string|max:255',
                'organization_data.*.house_number_o' => 'required|string|max:255',
                'organization_data.*.organization_data.*.digital_address_o' => 'required|string|max:255',
                'organization_data.*.residential_address_o' => 'nullable|string|max:255',
                'organization_data.*.postal_address_o' => 'nullable|string|max:255',
            ]);
        }

        return $rules;
    }
}

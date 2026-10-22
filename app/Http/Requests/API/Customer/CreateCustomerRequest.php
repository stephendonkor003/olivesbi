<?php

namespace App\Http\Requests\API\Customer;

use App\Support\OwnerRecordAccess;
use Illuminate\Contracts\Validation\ValidationRule;
use Illuminate\Foundation\Http\FormRequest;

class CreateCustomerRequest extends FormRequest
{
    /**
     * Determine if the user is authorized to make this request.
     */
    public function authorize(): bool
    {
        return $this->route()?->getActionMethod() === 'frontstore'
            || OwnerRecordAccess::mayCreate($this->user(), $this);
    }

    /**
     * Get the validation rules that apply to the request.
     *
     * @return array<string, ValidationRule|array<mixed>|string>
     */
    public function rules(): array
    {
        return [
            'first_name' => ['required', 'string', 'max:255'],
            'last_name' => ['required', 'string', 'max:255'],
            'email' => ['nullable', 'email', 'max:255', 'unique:entity_owners,email'],
            'gender' => ['nullable', 'in:M,F'],
            'phone_no_primary' => ['required', 'string', 'max:20', 'unique:entity_owners,phone_no_primary'],
            'tin_no' => ['nullable', 'string', 'max:50'],
            'house_no' => ['nullable', 'string', 'max:50'],
            'residential_address' => ['nullable', 'string', 'max:255'],
            'postal_address' => ['nullable', 'string', 'max:255'],
            'digital_address' => ['nullable', 'string', 'max:50'],
            'assembly_code' => ['nullable', 'string', 'exists:assemblies,assembly_code'],
        ];
    }
}

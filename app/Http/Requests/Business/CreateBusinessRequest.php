<?php

namespace App\Http\Requests\Business;

use Illuminate\Foundation\Http\FormRequest;

class CreateBusinessRequest extends FormRequest
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
            'business_name' => 'required|string',
            'registration_no' => 'nullable|string',
            'permit_no' => 'nullable|string',
            'hseno' => 'nullable|string',
            'digitaladdress' => 'nullable|string',
            'location' => 'required|string',
            'street_name' => 'nullable|string',
            'phone_no' => 'required|string|max:15',
            'address' => 'nullable|string',
            'email' => 'nullable|email',
            'owner_id' => 'required|string',
            'assembly_code' => 'required|string|exists:assemblies,assembly_code',
            'division_id' => 'required|exists:divisions,id',
            'block_id' => 'required|exists:blocks,id',
            'longitude' => 'required|string',
            'latitude' => 'required|string',
        ];
    }
}

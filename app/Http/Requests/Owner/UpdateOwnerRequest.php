<?php

namespace App\Http\Requests\Owner;

use Illuminate\Foundation\Http\FormRequest;
use Illuminate\Validation\Rule;

class UpdateOwnerRequest extends FormRequest
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
        $owner = $this->route('owner');

        $user = $owner->user;

        return [
            'first_name' => ['required', 'string', 'max:255'],
            'last_name' => ['required', 'string', 'max:255'],
            'email' => Rule::when(
                $user,
                function () use ($owner, $user) {
                    return ['nullable', 'email', 'max:255', Rule::unique('entity_owners', 'email')->ignore($owner->id), Rule::unique('users', 'email')->ignore($user->id)];
                },
                function () use ($owner) {
                    return ['nullable', 'email', 'max:255', Rule::unique('entity_owners', 'email')->ignore($owner->id), Rule::unique('users', 'email')];
                }
            ),
            'gender' => ['required', 'in:M,F'],
            'phone_no_primary' => Rule::when(
                $user,
                function () use ($owner, $user) {
                    return ['nullable', 'string', 'phone:GH', Rule::unique('entity_owners', 'phone_no_primary')->ignore($owner->id), Rule::unique('users', 'phone')->ignore($user->id)];
                },
                function () use ($owner) {
                    return ['nullable', 'string', 'phone:GH', Rule::unique('entity_owners', 'phone_no_primary')->ignore($owner->id), Rule::unique('users', 'phone')];
                }
            ),
            'tin_no' => ['nullable', 'string', 'max:50'],
            'house_no' => ['nullable', 'string', 'max:50'],
            'residential_address' => ['nullable', 'string', 'max:255'],
            'postal_address' => ['nullable', 'string', 'max:255'],
            'digital_address' => ['nullable', 'string', 'max:50'],
            'assembly_code' => ['nullable', 'string', 'exists:assemblies,assembly_code'],
        ];
    }
}

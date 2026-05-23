<?php

namespace App\Http\Requests\API\User;

use Illuminate\Foundation\Http\FormRequest;
use Illuminate\Validation\Rule;

class UpdateUserRequest extends FormRequest
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
        $hasAssemblyCode = ! empty($this->user()->assembly_code);
        $hasDivisionCode = ! empty($this->user()->division_code);

        return [
            'assembly_code' => [Rule::excludeIf($hasAssemblyCode), 'required', Rule::exists('assemblies', 'assembly_code')],
            'division_code' => [Rule::excludeIf($hasDivisionCode), 'required', Rule::exists('divisions', 'code')],
            'name' => 'required|string|max:255',
            'email' => 'required|string|email|max:255',
            'password' => 'required|string|confirmed',
            'phone' => 'required|string|max:15',
            'access_level' => 'required|string|in:Assembly_Supervisor,Assembly_Agent,customer',
            'status' => 'nullable|string',
        ];
    }
}

<?php

namespace App\Http\Requests\User;

use Illuminate\Foundation\Http\FormRequest;
use Illuminate\Validation\Rule;

class CreateUserRequest extends FormRequest
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
     * @return array<string, \Illuminate\Contracts\Validation\ValidationRule|array|string>
     */
    public function rules(): array
    {
        $hasRegionCode = ! empty($this->user()->regional_code);
        $hasAssemblyCode = ! empty($this->user()->assembly_code);
        $hasDivisionCode = ! empty($this->user()->division_code);

        $regionCode = $this->user()->regional_code ?? $this->input('regional_code');
        $assemblyCode = $this->user()->assembly_code ?? $this->input('assembly_code');

        return [
            'regional_code' => [Rule::excludeIf($hasRegionCode), 'required', Rule::exists('ghana_regions', 'regional_code')],
            'assembly_code' => [
                Rule::excludeIf($hasAssemblyCode),
                'required',
                Rule::exists('assemblies', 'assembly_code')
                    ->when(isset($regionCode), function ($query) use ($regionCode) {
                        return $query->where('regional_code', $regionCode);
                    }),
            ],
            'division_code' => [
                Rule::excludeIf($hasDivisionCode),
                'required_if:access_level,Assembly_Supervisor', 'nullable',
                Rule::exists('divisions', 'code')
                    ->when(isset($assemblyCode), function ($query) use ($assemblyCode) {
                        return $query->where('assembly_code', $assemblyCode);
                    }),
            ],
            'name' => 'required|string|max:255',
            'email' => 'required|string|email|max:255|unique:users,email',
            'password' => 'required|string|confirmed',
            'phone' => 'required|string|phone:GH|max:15|unique:users,phone',
            'access_level' => 'required|string',
            'status' => 'required|string',
            'role' => 'required|string',
            'gender' => 'required|string',
            // 'assembly_code' => 'required_if:access_level,Assembly_Supervisor|nullable|string',
            // 'division_code' => 'required_if:access_level,Assembly_Supervisor|nullable|string',
        ];
    }
}

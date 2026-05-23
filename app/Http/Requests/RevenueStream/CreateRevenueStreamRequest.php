<?php

namespace App\Http\Requests\RevenueStream;

use Illuminate\Foundation\Http\FormRequest;
use Illuminate\Validation\Rule;

class CreateRevenueStreamRequest extends FormRequest
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
        $isRated = $this->input('is_rated');

        $hasAssemblyCode = ! empty($this->user()->assembly_code);

        $assemblyCode = $this->user()->assembly_code ?? $this->get('assembly_code');

        return [
            'assembly_code' => [Rule::excludeIf($hasAssemblyCode), 'required', Rule::exists('assemblies', 'assembly_code')],
            'is_rated' => ['required', 'string', 'in:Y,N'],
            'zone_type_id' => [
                'required', 'string',
                Rule::exists('zones', 'id')->when(! empty($assemblyCode), function ($query) use ($assemblyCode) {
                    return $query->where('assembly_code', $assemblyCode);
                })],
            'property_classes_id' => [
                'nullable', 'string',
                Rule::exists('property_classes', 'id')->when(! empty($assemblyCode), function ($query) use ($assemblyCode) {
                    return $query->where('assembly_code', $assemblyCode);
                })],
            'min_amount' => ['nullable', 'numeric'],
            'rate_impost' => [$isRated === 'Y' ? 'required' : 'nullable', 'numeric'],
            'flat_rate' => [$isRated === 'N' ? 'required' : 'nullable', 'numeric'],
            'property_type_id' => [
                $isRated === 'N' ? 'required' : 'nullable', 'string',
                Rule::exists('property_types', 'id')->when(! empty($assemblyCode), function ($query) use ($assemblyCode) {
                    return $query->where('assembly_code', $assemblyCode);
                }),
            ],
            'description' => ['nullable', 'string'],
            'basic_rate' => ['required', 'numeric'],
        ];
    }
}

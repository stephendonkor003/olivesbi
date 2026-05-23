<?php

namespace App\Http\Requests\RevenueStream;

use Illuminate\Foundation\Http\FormRequest;
use Illuminate\Validation\Rule;

class CreateRevenueStreamRequestBOP extends FormRequest
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

        $assemblyCode = $this->user()->assembly_code ?? $this->get('assembly_code');

        return [
            'assembly_code' => [Rule::excludeIf($hasAssemblyCode), 'required', Rule::exists('assemblies', 'assembly_code')],
            'code' => ['nullable', 'numeric'],
            'main_item_id' => [
                'nullable', 'string',
                Rule::exists('main_items', 'id')->when(! empty($assemblyCode), function ($query) use ($assemblyCode) {
                    return $query->where('assembly_code', $assemblyCode);
                })],
            'sub_item_id' => [
                'nullable', 'string',
                Rule::exists('sub_items', 'id')->when(! empty($assemblyCode), function ($query) use ($assemblyCode) {
                    return $query->where('assembly_code', $assemblyCode);
                })],
            'category_id' => [
                'required', 'string',
                Rule::exists('categories', 'id')->when(! empty($assemblyCode), function ($query) use ($assemblyCode) {
                    return $query->where('assembly_code', $assemblyCode);
                })],
            'description_id' => [
                'required', 'string',
                Rule::exists('item_descriptions', 'id')->when(! empty($assemblyCode), function ($query) use ($assemblyCode) {
                    return $query->where('assembly_code', $assemblyCode);
                })],
            'flat_rate' => ['required', 'numeric'],
            'duration' => ['nullable', 'string', 'in:Per Annum,Per Square Meter'],
            'basic_rate' => ['required', 'numeric'],
        ];
    }
}
